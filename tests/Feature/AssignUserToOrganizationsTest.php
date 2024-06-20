<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use App\Services\AssignUserToOrganizationsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AssignUserToOrganizationsTest extends TestCase
{
    use RefreshDatabase;

    public function testAssignFunctionWithValidResponse(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Http::fake([
            "https://api.github.com/user/orgs" => Http::response([
                ["id" => 123, "login" => "org1"],
            ], 200),
        ]);

        $organization = Organization::factory()->create(["github_id" => 123]);

        (new AssignUserToOrganizationsService())->assign();

        $this->assertDatabaseHas("user_organization", [
            "user_id" => $user->id,
            "organization_id" => $organization->id,
        ]);
    }

    public function testAssignFunctionWithEmptyResponse(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Http::fake([
            "https://api.github.com/user/orgs" => Http::response([], 200),
        ]);

        (new AssignUserToOrganizationsService())->assign();

        $this->assertDatabaseMissing("user_organization", [
            "user_id" => $user->id,
        ]);
    }

    public function testAssignFunctionWithNullResponse(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Http::fake([
            "https://api.github.com/user/orgs" => Http::response(null, 200),
        ]);

        (new AssignUserToOrganizationsService())->assign();

        $this->assertDatabaseMissing("user_organization", [
            "user_id" => $user->id,
        ]);
    }
}
