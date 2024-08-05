<?php

declare(strict_types=1);

namespace App\Jobs;

use App\DTO\RepositoryDTO;
use App\Services\FetchWorkflowRunsService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchWorkflowRuns implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected int $userId,
        protected RepositoryDTO $repositoryDto,
    ) {}

    public function handle(FetchWorkflowRunsService $service): void
    {
        $workflowRuns = $service->fetchWorkflowRuns($this->repositoryDto, $this->userId);

        foreach ($workflowRuns as $run) {
            $this->batch()->add(new FetchWorkflowJobs($this->userId, $run));
        }
    }
}
