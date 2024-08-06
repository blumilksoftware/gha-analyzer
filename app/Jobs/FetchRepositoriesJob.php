<?php

declare(strict_types=1);

namespace App\Jobs;

use App\DTO\OrganizationDTO;
use App\Models\Organization;
use App\Services\FetchRepositoriesService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Saloon\RateLimitPlugin\Helpers\ApiRateLimited;

class FetchRepositoriesJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected int $organizationId,
        protected int $userId,
    ) {}

    public function handle(FetchRepositoriesService $service): void
    {
        $organization = Organization::query()->where("id", $this->organizationId)->firstOrFail();
        $organizationDto = new OrganizationDTO(
            $organization->name,
            $organization->github_id,
            $organization->avatar_url,
        );

        $repositories = $service->fetchRepositories($organizationDto, $this->userId);

        foreach ($repositories as $repositoryDto) {
            $this->batch()->add(new FetchWorkflowRuns($this->userId, $repositoryDto));
        }
    }

    public function middleware(): array
    {
        return [new ApiRateLimited()];
    }
}
