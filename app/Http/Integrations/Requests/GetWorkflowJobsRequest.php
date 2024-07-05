<?php

declare(strict_types=1);

namespace App\Http\Integrations\Requests;

use App\DTO\WorkflowJobDTO;
use App\DTO\WorkflowRunDTO;
use App\Models\Organization;
use App\Models\Repository;
use App\Models\WorkflowRun;
use App\Services\CalculateJobTimeService;
use App\Services\GetRunnerDataService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetWorkflowJobsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected WorkflowRunDTO $workflowRunDto,
    ) {}

    public function resolveEndpoint(): string
    {
        $repository = Repository::query()
            ->where("id", $this->workflowRunDto->repositoryId)
            ->firstOrFail();

        $organization = Organization::query()
            ->where("id", $repository->organization_id)
            ->firstOrFail();

        return "/repos/" . $organization->name . "/" . $repository->name . "/actions/runs/" . $this->workflowRunDto->githubId . "/jobs";
    }

    public function createDtoFromResponse(Response $response): Collection
    {
        $workflowRun = WorkflowRun::query()->where("github_id", $this->workflowRunDto->githubId)->firstOrFail();
        $getRunnerDataService = new GetRunnerDataService();
        $calculateJobTimeService = new CalculateJobTimeService();

        $workflowJobs = collect();

        if ($response->json() !== null) {
            foreach ($response->json()["jobs"] as $data) {
                $jobTime = $calculateJobTimeService->calculate($data["started_at"], $data["completed_at"]);
                $runnerData = $getRunnerDataService->getRunnerData($data["labels"]);

                $workflowJobs->push(new WorkflowJobDTO(
                    githubId: $data["id"],
                    name: $data["name"],
                    runnerOs: $runnerData["os"],
                    runnerType: $runnerData["cores"],
                    workflowRunId: $workflowRun->id,
                    minutes: $jobTime,
                    multiplier: $runnerData["multiplier"],
                    pricePerUnit: $runnerData["pricing"],
                ));
            }
        }

        return $workflowJobs;
    }

    protected function defaultHeaders(): array
    {
        return [
            "Authorization" => "Bearer " . Auth::user()->github_token,
        ];
    }
}
