<?php

declare(strict_types=1);

namespace App\Jobs;

use App\DTO\WorkflowRunDTO;
use App\Services\FetchWorkflowJobsService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Saloon\RateLimitPlugin\Helpers\ApiRateLimited;

class FetchWorkflowJobs implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected int $userId,
        protected WorkflowRunDTO $workflowRunDto,
    ) {}

    public function handle(FetchWorkflowJobsService $service): void
    {
        $service->fetchWorkflowJobs($this->workflowRunDto, $this->userId);
    }

    public function middleware(): array
    {
        return [new ApiRateLimited()];
    }
}
