<?php

declare(strict_types=1);

namespace App\Http\Integrations\Requests;

use App\DTO\RepositoryDTO;
use App\DTO\WorkflowActorDTO;
use App\DTO\WorkflowRunDTO;
use App\Models\Organization;
use App\Models\Repository;
use App\Models\User;
use DateTime;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetWorkflowRunsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected RepositoryDTO $repositoryDto,
        protected User $user,
    ) {}

    public function resolveEndpoint(): string
    {
        $organization = Organization::query()
            ->where("id", $this->repositoryDto->organizationId)
            ->firstOrFail();

        return "/repos/" . $organization->name . "/" . $this->repositoryDto->name . "/actions/runs";
    }

    public function createDtoFromResponse(Response $response): Collection
    {
        $repository = Repository::query()->where("github_id", $this->repositoryDto->githubId)->firstOrFail();

        $workflowRuns = collect();

        if ($response->json() !== null) {
            foreach ($response->json()["workflow_runs"] as $data) {
                $workflowRuns->push(new WorkflowRunDTO(
                    githubId: $data["id"],
                    name: $data["name"],
                    repositoryId: $repository->id,
                    createdAt: new DateTime($data["created_at"]),
                    actor: WorkflowActorDTO::createFromArray($data["actor"]),
                ));
            }
        }

        return $workflowRuns;
    }

    protected function defaultHeaders(): array
    {
        return [
            "Authorization" => "Bearer " . $this->user->github_token,
        ];
    }
}
