<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\WorkflowRunDTO;
use App\Exceptions\FetchingWorkflowJobsErrorException;
use App\Http\Integrations\GithubConnector;
use App\Http\Integrations\Requests\GetWorkflowJobsRequest;
use App\Models\Organization;
use App\Models\Repository;
use App\Models\User;
use App\Models\WorkflowJob;
use App\Models\WorkflowRun;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class FetchWorkflowJobsService
{
    public function __construct(
        protected GithubConnector $githubConnector,
    ) {}

    public function fetchWorkflowJobs(WorkflowRunDTO $workflowRunDto): void
    {
        $workflowRun = WorkflowRun::query()->where("github_id", $workflowRunDto->githubId)->firstOrFail();

        if ($workflowRun->workflowJobs()->exists()) {
            return;
        }

        $repository = Repository::query()
            ->where("id", $workflowRunDto->repositoryId)
            ->firstOrFail();

        $organization = Organization::query()
            ->where("id", $repository->organization_id)
            ->firstOrFail();

        $user = User::query()->where("id", Auth::user()->id)->firstOrFail();

        $userOrganizationExists = $user->organizations()
            ->where("organization_id", $organization->id)
            ->where("is_admin", true)
            ->exists();

        if ($userOrganizationExists) {
            try {
                $request = new GetWorkflowJobsRequest($workflowRunDto, $organization->name, $repository->name);

                $response = $this->githubConnector->send($request);

                $this->storeWorkflowJobs($response->dto());
            } catch (Exception $exception) {
                throw new FetchingWorkflowJobsErrorException(
                    message: "Error ocurred while fetching workflow jobs",
                    previous: $exception,
                );
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    public function storeWorkflowJobs(Collection $workflowJobs): void
    {
        if (!$workflowJobs->isEmpty()) {
            foreach ($workflowJobs as $workflowJobDto) {
                WorkflowJob::firstOrCreate([
                    "github_id" => $workflowJobDto->githubId,
                    "name" => $workflowJobDto->name,
                    "workflow_run_id" => $workflowJobDto->workflowRunId,
                    "runner_os" => $workflowJobDto->runnerOs,
                    "runner_type" => $workflowJobDto->runnerType,
                    "minutes" => $workflowJobDto->minutes,
                    "multiplier" => $workflowJobDto->multiplier,
                    "price_per_unit" => $workflowJobDto->pricePerUnit,
                ]);
            }
        }
    }
}
