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

    private $user;
    private $organization;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(["github_id" => 123]);
        $this->organization = Organization::factory()->create(["github_id" => 456]);
        $this->user->organizations()->attach($this->organization->id);
    }

    public function testRemoveMember(): void
    {
        $this->assertDatabaseHas("user_organization", [
            "organization_id" => $this->organization->id,
            "user_id" => $this->user->id,
        ]);

        $service = new GithubWebhookService();
        $service->removeMember($this->organization->github_id, $this->user->github_id);

        $this->assertDatabaseMissing("user_organization", [
            "organization_id" => $this->organization->id,
            "user_id" => $this->user->id,
        ]);
    }
}
