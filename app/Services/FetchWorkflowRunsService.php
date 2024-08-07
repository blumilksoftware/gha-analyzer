<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\RepositoryDTO;
use App\DTO\WorkflowRunDTO;
use App\Exceptions\FetchingWorkflowRunsErrorException;
use App\Http\Integrations\GithubConnector;
use App\Http\Integrations\Requests\GetWorkflowRunsRequest;
use App\Models\Organization;
use App\Models\User;
use App\Models\WorkflowActor;
use App\Models\WorkflowRun;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Validation\UnauthorizedException;

class FetchWorkflowRunsService
{
    public function __construct(
        protected GithubConnector $githubConnector,
    ) {}

    public function fetchWorkflowRuns(RepositoryDTO $repositoryDto, int $userId): Collection
    {
        $organization = Organization::query()->where("id", $repositoryDto->organizationId)->firstOrFail();
        $user = User::query()->where("id", $userId)->firstOrFail();

        $userOrganizationExists = $user->organizations()
            ->where("organization_id", $organization->id)
            ->exists();

        if ($userOrganizationExists) {
            try {
                $request = new GetWorkflowRunsRequest($repositoryDto, $user);

                $response = $this->githubConnector->send($request);

                $this->storeWorkflowRuns($response->dto());

                return $response->dto();
            } catch (Exception $exception) {
                throw new FetchingWorkflowRunsErrorException(
                    message: "Error ocurred while fetching workflow runs",
                    previous: $exception,
                );
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * @param Collection<WorkflowRunDTO> $workflowRuns
     */
    public function storeWorkflowRuns(Collection $workflowRuns): void
    {
        if (!$workflowRuns->isEmpty()) {
            foreach ($workflowRuns as $workflowRunDto) {
                $actor = WorkflowActor::firstOrCreate(["github_id" => $workflowRunDto->actor->githubId], [
                    "github_id" => $workflowRunDto->actor->githubId,
                    "name" => $workflowRunDto->actor->name,
                    "avatar_url" => $workflowRunDto->actor->avatarUrl,
                ]);

                WorkflowRun::firstOrCreate([
                    "github_id" => $workflowRunDto->githubId,
                    "name" => $workflowRunDto->name,
                    "repository_id" => $workflowRunDto->repositoryId,
                    "workflow_actor_id" => $actor->id,
                    "github_created_at" => $workflowRunDto->createdAt,
                ]);
            }
        }
    }
}
