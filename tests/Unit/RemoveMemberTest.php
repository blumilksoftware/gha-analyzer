<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use App\Services\GithubWebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveMemberTest extends TestCase
{
    use RefreshDatabase;

    public function testRemoveMember(): void
    {
        $user = User::factory()->create(["github_id" => 123]);
        $organization = Organization::factory()->create(["github_id" => 456]);

        $user->organizations()->attach($organization->id);

        $this->assertDatabaseHas("user_organization", [
            "organization_id" => $organization->id,
            "user_id" => $user->id,
        ]);

        $service = new GithubWebhookService();
        $service->removeMember($organization->github_id, $user->github_id);

        $this->assertDatabaseMissing("user_organization", [
            "organization_id" => $organization->id,
            "user_id" => $user->id,
        ]);
    }
}
