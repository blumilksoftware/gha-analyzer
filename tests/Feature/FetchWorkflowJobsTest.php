<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTO\WorkflowRunDTO;
use App\Exceptions\FetchingWorkflowJobsErrorException;
use App\Http\Integrations\GithubConnector;
use App\Http\Integrations\Requests\GetWorkflowJobsRequest;
use App\Models\Organization;
use App\Models\Repository;
use App\Models\User;
use App\Models\WorkflowJob;
use App\Models\WorkflowRun;
use App\Services\FetchWorkflowJobsService;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\UnauthorizedException;
use Saloon\Config;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\TestCase;

class FetchWorkflowJobsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Repository $repository;
    protected WorkflowRun $workflowRun;
    protected Organization $organization;
    protected FetchWorkflowJobsService $fetchWorkflowJobsService;
    protected GithubConnector $githubConnector;
    protected WorkflowRunDTO $workflowRunDto;

    protected function setUp(): void
    {
        parent::setUp();
        Config::preventStrayRequests();

        $this->githubConnector = new GithubConnector();
        $this->user = User::factory()->create();
        $this->workflowRun = WorkflowRun::factory()->create();
        $this->workflowRunDto = new WorkflowRunDTO(
            $this->workflowRun->github_id,
            $this->workflowRun->name,
            $this->workflowRun->repository_id,
            new DateTime($this->workflowRun->github_created_at),
        );
        $this->repository = Repository::query()->where("id", $this->workflowRun->repository_id)->firstOrFail();
        $this->fetchWorkflowJobsService = new FetchWorkflowJobsService($this->githubConnector);
        $this->actingAs($this->user);

        MockClient::destroyGlobal();
    }

    public function testFetchWorkflowJobsWithAdminUser(): void
    {
        $this->user->organizations()->attach($this->repository->organization_id, ["is_admin" => true]);

        $mockClient = new MockClient([
            GetWorkflowJobsRequest::class => MockResponse::make([
                "jobs" => [
                    [
                        "id" => 123,
                        "name" => "job1",
                        "started_at" => "2024-06-19T08:25:09Z",
                        "completed_at" => "2024-06-19T08:26:09Z",
                        "labels" => [
                            "ubuntu-latest",
                        ],
                    ],
                ],
            ], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->fetchWorkflowJobsService->fetchWorkflowJobs($this->workflowRunDto);

        $this->assertDatabaseHas("workflow_jobs", [
            "github_id" => 123,
            "name" => "job1",
            "workflow_run_id" => $this->workflowRun->id,
            "runner_os" => "ubuntu",
            "runner_type" => "standard",
            "minutes" => 1,
            "multiplier" => 1,
            "price_per_unit" => 0.008,
        ]);
    }

    public function testFetchWorkflowJobsIfAlreadyFetched(): void
    {
        $this->user->organizations()->attach($this->repository->organization_id, ["is_admin" => true]);
        WorkflowJob::factory()->create([
            "workflow_run_id" => $this->workflowRun->id,
        ]);

        $mockClient = new MockClient([
            GetWorkflowJobsRequest::class => MockResponse::make([
                "jobs" => [
                    [
                        "id" => 123,
                        "name" => "job1",
                        "started_at" => "2024-06-19T08:25:09Z",
                        "completed_at" => "2024-06-19T08:26:09Z",
                        "labels" => [
                            "ubuntu-latest",
                        ],
                    ],
                ],
            ], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->fetchWorkflowJobsService->fetchWorkflowJobs($this->workflowRunDto);

        $this->assertDatabaseMissing("workflow_jobs", [
            "github_id" => 123,
            "name" => "job1",
            "workflow_run_id" => $this->workflowRun->id,
            "runner_os" => "ubuntu",
            "runner_type" => "standard",
            "minutes" => 1,
            "multiplier" => 1,
            "price_per_unit" => 0.008,
        ]);
    }

    public function testFetchWorkflowJobsWithMemberUser(): void
    {
        $this->user->organizations()->attach($this->repository->organization_id);

        $mockClient = new MockClient([
            GetWorkflowJobsRequest::class => MockResponse::make([
                "jobs" => [
                    [
                        "id" => 123,
                        "name" => "job1",
                        "started_at" => "2024-06-19T08:25:09Z",
                        "completed_at" => "2024-06-19T08:26:09Z",
                        "labels" => [
                            "ubuntu-latest",
                        ],
                    ],
                ],
            ], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(UnauthorizedException::class);

        $this->fetchWorkflowJobsService->fetchWorkflowJobs($this->workflowRunDto);

        $this->assertDatabaseMissing("workflow_jobs", [
            "github_id" => 123,
            "name" => "job1",
            "workflow_run_id" => $this->workflowRun->id,
            "runner_os" => "ubuntu",
            "runner_type" => "standard",
            "minutes" => 1,
            "multiplier" => 1,
            "price_per_unit" => 0.008,
        ]);
    }

    public function testFetchWorkflowJobsWithUserNotInOrganization(): void
    {
        $mockClient = new MockClient([
            GetWorkflowJobsRequest::class => MockResponse::make([
                "jobs" => [
                    [
                        "id" => 123,
                        "name" => "job1",
                        "started_at" => "2024-06-19T08:25:09Z",
                        "completed_at" => "2024-06-19T08:26:09Z",
                        "labels" => [
                            "ubuntu-latest",
                        ],
                    ],
                ],
            ], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(UnauthorizedException::class);

        $this->fetchWorkflowJobsService->fetchWorkflowJobs($this->workflowRunDto);

        $this->assertDatabaseMissing("workflow_jobs", [
            "github_id" => 123,
            "name" => "job1",
            "workflow_run_id" => $this->workflowRun->id,
            "runner_os" => "ubuntu",
            "runner_type" => "standard",
            "minutes" => 1,
            "multiplier" => 1,
            "price_per_unit" => 0.008,
        ]);
    }

    public function testFetchWorkflowJobsWithStatus500(): void
    {
        $this->user->organizations()->attach($this->repository->organization_id, ["is_admin" => true]);

        $mockClient = new MockClient([
            GetWorkflowJobsRequest::class => MockResponse::make([], 500),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(FetchingWorkflowJobsErrorException::class);

        $this->fetchWorkflowJobsService->fetchWorkflowJobs($this->workflowRunDto);
    }

    public function testFetchWorkflowJobsWithStatus404(): void
    {
        $this->user->organizations()->attach($this->repository->organization_id, ["is_admin" => true]);

        $mockClient = new MockClient([
            GetWorkflowJobsRequest::class => MockResponse::make([], 404),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(FetchingWorkflowJobsErrorException::class);

        $this->fetchWorkflowJobsService->fetchWorkflowJobs($this->workflowRunDto);
    }
}
