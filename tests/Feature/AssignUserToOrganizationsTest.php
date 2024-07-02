<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Integrations\GithubConnector;
use App\Models\Organization;
use App\Models\User;
use App\Services\AssignUserToOrganizationsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Saloon\Config;
use Saloon\Exceptions\Request\Statuses\InternalServerErrorException;
use Saloon\Exceptions\Request\Statuses\NotFoundException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\TestCase;

class AssignUserToOrganizationsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $assignUserService;
    protected $requestUrl;
    protected $githubConnector;

    protected function setUp(): void
    {
        parent::setUp();
        Config::preventStrayRequests();

        $this->githubConnector = new GithubConnector();
        $this->requestUrl = "https://api.github.com/user/orgs";
        $this->user = User::factory()->create();
        $this->assignUserService = new AssignUserToOrganizationsService($this->githubConnector);

        MockClient::destroyGlobal();
    }

    public function testAssignFunctionWithValidResponse(): void
    {
        $this->actingAs($this->user);

        $mockClient = new MockClient([
            $this->requestUrl => MockResponse::make([["id" => 123, "login" => "org1"]], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $organization = Organization::factory()->create(["github_id" => 123]);

        $this->assignUserService->assign($this->user);

        $this->assertDatabaseHas("user_organization", [
            "user_id" => $this->user->id,
            "organization_id" => $organization->id,
        ]);
    }

    public function testAssignFunctionWithEmptyResponse(): void
    {
        $this->actingAs($this->user);

        $mockClient = new MockClient([
            $this->requestUrl => MockResponse::make([], 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->assignUserService->assign($this->user);

        $this->assertDatabaseMissing("user_organization", [
            "user_id" => $this->user->id,
        ]);
    }

    public function testAssignFunctionWithStatus500(): void
    {
        $this->actingAs($this->user);

        $mockClient = new MockClient([
            $this->requestUrl => MockResponse::make([], 500),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(InternalServerErrorException::class);

        $this->assignUserService->assign($this->user);
    }

    public function testAssignFunctionWithStatus404(): void
    {
        $this->actingAs($this->user);

        $mockClient = new MockClient([
            $this->requestUrl => MockResponse::make([], 404),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(NotFoundException::class);

        $this->assignUserService->assign($this->user);
    }
}
