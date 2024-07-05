<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTO\RepositoryDTO;
use App\Exceptions\FetchingWorkflowRunsErrorException;
use App\Http\Integrations\GithubConnector;
use App\Http\Integrations\Requests\GetWorkflowRunsRequest;
use App\Models\Organization;
use App\Models\Repository;
use App\Models\User;
use App\Services\FetchWorkflowRunsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\UnauthorizedException;
use Saloon\Config;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\TestCase;

class FetchWorkflowRunsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Repository $repository;
    protected Organization $organization;
    protected FetchWorkflowRunsService $fetchWorkflowRunsService;
    protected GithubConnector $githubConnector;
    protected RepositoryDTO $repositoryDto;

    protected function setUp(): void
    {
        parent::setUp();
        Config::preventStrayRequests();

        $this->githubConnector = new GithubConnector();
        $this->user = User::factory()->create();
        $this->repository = Repository::factory()->create();
        $this->repositoryDto = new RepositoryDTO(
            $this->repository->github_id,
            $this->repository->name,
            $this->repository->organization_id,
            $this->repository->is_private,
        );
        $this->fetchWorkflowRunsService = new FetchWorkflowRunsService($this->githubConnector);
        $this->actingAs($this->user);

        MockClient::destroyGlobal();
    }

    public function testFetchWorkflowRunsWithAdminUser(): void
    {
        $this->user->organizations()->attach($this->repository->organization_id, ["is_admin" => true]);

        $mockClient = new MockClient([
            GetWorkflowRunsRequest::class => MockResponse::make([
                "workflow_runs" => [
                    [
                        "id" => 123,
                        "name" => "run1",
                        "created_at" => "2024-06-19T08:25:09Z",
                    ],
                ],
            ], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->fetchWorkflowRunsService->fetchWorkflowRuns($this->repositoryDto);

        $this->assertDatabaseHas("workflow_runs", [
            "github_id" => 123,
            "name" => "run1",
            "repository_id" => $this->repository->id,
            "github_created_at" => "2024-06-19T08:25:09Z",
        ]);
    }

    public function testFetchWorkflowRunsWithMemberUser(): void
    {
        $this->user->organizations()->attach($this->repository->organization_id);

        $mockClient = new MockClient([
            GetWorkflowRunsRequest::class => MockResponse::make([
                "workflow_runs" => [
                    [
                        "id" => 123,
                        "name" => "run1",
                        "created_at" => "2024-06-19T08:25:09Z",
                    ],
                ],
            ], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(UnauthorizedException::class);

        $this->fetchWorkflowRunsService->fetchWorkflowRuns($this->repositoryDto);

        $this->assertDatabaseMissing("workflow_runs", [
            "github_id" => 123,
            "name" => "run1",
            "repository_id" => $this->repository->id,
            "github_created_at" => "2024-06-19T08:25:09Z",
        ]);
    }

    public function testFetchWorkflowRunsWithUserNotInOrganization(): void
    {
        $mockClient = new MockClient([
            GetWorkflowRunsRequest::class => MockResponse::make([
                "workflow_runs" => [
                    [
                        "id" => 123,
                        "name" => "run1",
                        "created_at" => "2024-06-19T08:25:09Z",
                    ],
                ],
            ], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(UnauthorizedException::class);

        $this->fetchWorkflowRunsService->fetchWorkflowRuns($this->repositoryDto);

        $this->assertDatabaseMissing("workflow_runs", [
            "github_id" => 123,
            "name" => "run1",
            "repository_id" => $this->repository->id,
            "github_created_at" => "2024-06-19T08:25:09Z",
        ]);
    }

    public function testFetchWorkflowRunsWithStatus500(): void
    {
        $this->user->organizations()->attach($this->repository->organization_id, ["is_admin" => true]);

        $mockClient = new MockClient([
            GetWorkflowRunsRequest::class => MockResponse::make([], 500),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(FetchingWorkflowRunsErrorException::class);

        $this->fetchWorkflowRunsService->fetchWorkflowRuns($this->repositoryDto);
    }

    public function testFetchWorkflowRunsWithStatus404(): void
    {
        $this->user->organizations()->attach($this->repository->organization_id, ["is_admin" => true]);

        $mockClient = new MockClient([
            GetWorkflowRunsRequest::class => MockResponse::make([], 404),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(FetchingWorkflowRunsErrorException::class);

        $this->fetchWorkflowRunsService->fetchWorkflowRuns($this->repositoryDto);
    }
}
