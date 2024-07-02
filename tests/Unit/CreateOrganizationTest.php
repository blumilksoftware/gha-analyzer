<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTO\OrganizationDTO;
use App\Services\GithubWebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrganizationTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateOrganization(): void
    {
        $webhookService = new GithubWebhookService();

        $organizationDto = new OrganizationDTO("test", 123, "http://example.com/avatar.png");

        $webhookService->createOrganization($organizationDto);

        $this->assertDatabaseHas("organizations", [
            "name" => $organizationDto->name,
            "github_id" => $organizationDto->githubId,
            "avatar_url" => $organizationDto->avatarUrl,
        ]);
    }
}
