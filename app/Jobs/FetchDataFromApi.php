<?php

declare(strict_types=1);

namespace App\Jobs;

use App\DTO\OrganizationDTO;
use App\Http\Integrations\GithubConnector;
use App\Models\Organization;
use App\Services\FetchRepositoriesService;
use App\Services\FetchWorkflowJobsService;
use App\Services\FetchWorkflowRunsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Saloon\RateLimitPlugin\Helpers\ApiRateLimited;

class FetchDataFromApi implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected int $organizationId,
        protected GithubConnector $githubConnector,
        protected FetchRepositoriesService $fetchRepositoriesService,
        protected FetchWorkflowRunsService $fetchWorkflowRunsService,
        protected FetchWorkflowJobsService $fetchWorkflowJobsService,
    ) {}

    public function handle(): void
    {
        $organization = Organization::query()->where("id", $this->organizationId)->firstOrFail();
        $organizationDto = new OrganizationDTO(
            $organization->name,
            $organization->github_id,
            $organization->avatar_url,
        );

        $repositories = collect();
        $repositories = $this->fetchRepositoriesService->fetchRepositories($organizationDto);

        $workflowRuns = collect();

        foreach ($repositories as $repositoryDto) {
            $workflowRuns = $workflowRuns->union($this->fetchWorkflowRunsService->fetchWorkflowRuns($repositoryDto));
        }

        foreach ($workflowRuns as $workflowRunDto) {
            $this->fetchWorkflowJobsService->fetchWorkflowJobs($workflowRunDto);
        }
    }

    public function middleware(): array
    {
        return [new ApiRateLimited()];
    }
}
