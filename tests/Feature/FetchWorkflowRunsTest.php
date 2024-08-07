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
use App\Models\WorkflowActor;
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
    protected WorkflowActor $actor;
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
        $this->actor = WorkflowActor::factory()->create();
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
                        "actor" => [
                            "id" => 321,
                            "login" => "actor21",
                            "avatar_url" => "http://localhost/actor21.png",
                        ],
                    ],
                ],
            ], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->fetchWorkflowRunsService->fetchWorkflowRuns($this->repositoryDto, $this->user->id);

        $this->assertDatabaseHas("workflow_runs", [
            "github_id" => 123,
            "name" => "run1",
            "repository_id" => $this->repository->id,
            "github_created_at" => "2024-06-19T08:25:09Z",
        ]);

        $this->assertDatabaseCount("workflow_actors", 2);

        $this->assertDatabaseHas("workflow_actors", [
            "github_id" => 321,
            "name" => "actor21",
            "avatar_url" => "http://localhost/actor21.png",
        ]);
    }

    public function testFetchWorkflowRunsWithKnownActor(): void
    {
        $this->user->organizations()->attach($this->repository->organization_id, ["is_admin" => true]);

        $mockClient = new MockClient([
            GetWorkflowRunsRequest::class => MockResponse::make([
                "workflow_runs" => [
                    [
                        "id" => 123,
                        "name" => "run1",
                        "created_at" => "2024-06-19T08:25:09Z",
                        "actor" => [
                            "id" => $this->actor->github_id,
                            "login" => $this->actor->name,
                            "avatar_url" => $this->actor->avatar_url,
                        ],
                    ],
                ],
            ], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->fetchWorkflowRunsService->fetchWorkflowRuns($this->repositoryDto, $this->user->id);

        $this->assertDatabaseHas("workflow_runs", [
            "github_id" => 123,
            "name" => "run1",
            "repository_id" => $this->repository->id,
            "github_created_at" => "2024-06-19T08:25:09Z",
        ]);

        $this->assertDatabaseCount("workflow_actors", 1);
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
                        "actor" => [
                            "id" => 321,
                            "login" => "actor21",
                            "avatar_url" => "http://localhost/actor21.png",
                        ],
                    ],
                ],
            ], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(UnauthorizedException::class);

        $this->fetchWorkflowRunsService->fetchWorkflowRuns($this->repositoryDto, $this->user->id);

        $this->assertDatabaseMissing("workflow_runs", [
            "github_id" => 123,
            "name" => "run1",
            "repository_id" => $this->repository->id,
            "github_created_at" => "2024-06-19T08:25:09Z",
            "actor" => [
                "id" => 321,
                "login" => "actor21",
                "avatar_url" => "http://localhost/actor21.png",
            ],
        ]);

        $this->assertDatabaseMissing("workflow_actors", [
            "github_id" => 321,
            "name" => "actor21",
            "avatar_url" => "http://localhost/actor21.png",
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

        $this->fetchWorkflowRunsService->fetchWorkflowRuns($this->repositoryDto, $this->user->id);
    }

    public function testFetchWorkflowRunsWithStatus404(): void
    {
        $this->user->organizations()->attach($this->repository->organization_id, ["is_admin" => true]);

        $mockClient = new MockClient([
            GetWorkflowRunsRequest::class => MockResponse::make([], 404),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(FetchingWorkflowRunsErrorException::class);

        $this->fetchWorkflowRunsService->fetchWorkflowRuns($this->repositoryDto, $this->user->id);
    }
}
