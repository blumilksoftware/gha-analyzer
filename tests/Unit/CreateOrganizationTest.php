<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\GithubWebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrganizationTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateOrganization(): void
    {
        $webhookService = new GithubWebhookService();

        $organizationName = "test";
        $organizationId = 123;
        $organizationAvatarUrl = "http://example.com/avatar.png";

        $webhookService->createOrganization($organizationName, $organizationId, $organizationAvatarUrl);

        $this->assertDatabaseHas("organizations", [
            "name" => $organizationName,
            "github_id" => $organizationId,
            "avatar_url" => $organizationAvatarUrl,
        ]);
    }
}
