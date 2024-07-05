<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTO\OrganizationDTO;
use App\Exceptions\FetchingRepositoriesErrorException;
use App\Http\Integrations\GithubConnector;
use App\Http\Integrations\Requests\GetRepositoriesRequest;
use App\Models\Organization;
use App\Models\User;
use App\Services\FetchRepositoriesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\UnauthorizedException;
use Saloon\Config;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\TestCase;

class FetchRepositoriesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Organization $organization;
    protected FetchRepositoriesService $fetchRepositoriesService;
    protected GithubConnector $githubConnector;
    protected OrganizationDTO $organizationDto;

    protected function setUp(): void
    {
        parent::setUp();
        Config::preventStrayRequests();

        $this->githubConnector = new GithubConnector();
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create();
        $this->organizationDto = new OrganizationDTO(
            $this->organization->name,
            $this->organization->github_id,
            $this->organization->avatar_url,
        );
        $this->fetchRepositoriesService = new FetchRepositoriesService($this->githubConnector);
        $this->actingAs($this->user);

        MockClient::destroyGlobal();
    }

    public function testfetchRepositoriesWithAdminUser(): void
    {
        $this->user->organizations()->attach($this->organization->id, ["is_admin" => true]);

        $mockClient = new MockClient([
            GetRepositoriesRequest::class => MockResponse::make([[
                "id" => 123,
                "name" => "repo",
                "owner" => [
                    "id" => $this->organization->id,
                ],
                "private" => true,
            ]], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->fetchRepositoriesService->fetchRepositories($this->organizationDto);

        $this->assertDatabaseHas("repositories", [
            "github_id" => 123,
            "name" => "repo",
            "organization_id" => $this->organization->id,
            "is_private" => true,
        ]);
    }

    public function testfetchRepositoriesWithMemberUser(): void
    {
        $this->user->organizations()->attach($this->organization->id);

        $mockClient = new MockClient([
            GetRepositoriesRequest::class => MockResponse::make([[
                "id" => 123,
                "name" => "repo",
                "owner" => [
                    "id" => $this->organization->id,
                ],
                "private" => true,
            ]], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(UnauthorizedException::class);

        $this->fetchRepositoriesService->fetchRepositories($this->organizationDto);

        $this->assertDatabaseMissing("repositories", [
            "github_id" => 123,
            "name" => "repo",
            "organization_id" => $this->organization->id,
            "is_private" => true,
        ]);
    }

    public function testfetchRepositoriesWithUserNotInOrganization(): void
    {
        $mockClient = new MockClient([
            GetRepositoriesRequest::class => MockResponse::make([[
                "id" => 123,
                "name" => "repo",
                "owner" => [
                    "id" => $this->organization->id,
                ],
                "private" => true,
            ]], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(UnauthorizedException::class);

        $this->fetchRepositoriesService->fetchRepositories($this->organizationDto);

        $this->assertDatabaseMissing("repositories", [
            "github_id" => 123,
            "name" => "repo",
            "organization_id" => $this->organization->id,
            "is_private" => true,
        ]);
    }

    public function testfetchRepositoriesWithEmptyResponse(): void
    {
        $this->user->organizations()->attach($this->organization->id, ["is_admin" => true]);

        $mockClient = new MockClient([
            GetRepositoriesRequest::class => MockResponse::make([], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(FetchingRepositoriesErrorException::class);

        $this->fetchRepositoriesService->fetchRepositories($this->organizationDto);
    }

    public function testfetchRepositoriesWithStatus500(): void
    {
        $this->user->organizations()->attach($this->organization->id, ["is_admin" => true]);

        $mockClient = new MockClient([
            GetRepositoriesRequest::class => MockResponse::make([], 500),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(FetchingRepositoriesErrorException::class);

        $this->fetchRepositoriesService->fetchRepositories($this->organizationDto);
    }

    public function testfetchRepositoriesWithStatus404(): void
    {
        $this->user->organizations()->attach($this->organization->id, ["is_admin" => true]);

        $mockClient = new MockClient([
            GetRepositoriesRequest::class => MockResponse::make([], 404),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(FetchingRepositoriesErrorException::class);

        $this->fetchRepositoriesService->fetchRepositories($this->organizationDto);
    }
}
