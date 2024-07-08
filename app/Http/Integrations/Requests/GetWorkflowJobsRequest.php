<?php

declare(strict_types=1);

namespace App\Http\Integrations\Requests;

use App\DTO\WorkflowJobDTO;
use App\DTO\WorkflowRunDTO;
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
        protected string $organizationName,
        protected string $repositoryName,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/repos/" . $this->organizationName . "/" . $this->repositoryName . "/actions/runs/" . $this->workflowRunDto->githubId . "/jobs";
    }

    public function createDtoFromResponse(Response $response): Collection
    {
        $workflowRun = WorkflowRun::query()->where("github_id", $this->workflowRunDto->githubId)->first();
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
