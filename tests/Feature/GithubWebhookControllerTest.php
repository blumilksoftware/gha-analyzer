<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTO\OrganizationDTO;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GithubWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $organization;
    protected User $user;
    protected OrganizationDTO $organizationDto;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(["github_id" => 123]);
        $this->organization = Organization::factory()->create(["github_id" => 456]);
        $this->user->organizations()->attach($this->organization->id);

        $this->organizationDto = new OrganizationDTO("test", 123, "http://example.com/avatar.png");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testCreateOrganizationRequestWithSignature(): void
    {
        $payload = [
            "action" => "created",
            "installation" => [
                "account" => [
                    "type" => "Organization",
                    "login" => $this->organizationDto->name,
                    "id" => $this->organizationDto->githubId,
                    "avatar_url" => $this->organizationDto->avatarUrl,
                ],
            ],
        ];

        $headers = [
            "X-Hub-Signature" => "sha1=" . hash_hmac("sha1", json_encode($payload), config("services.github.webhook_secret")),
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ];

        $response = $this->withHeaders($headers)->postJson("/api/webhook", $payload);

        $this->assertSame(200, $response->getStatusCode());

        $this->assertDatabaseHas("organizations", [
            "name" => $this->organizationDto->name,
            "github_id" => $this->organizationDto->githubId,
            "avatar_url" => $this->organizationDto->avatarUrl,
        ]);
    }

    public function testCreateOrganizationRequestWithIncorrectData(): void
    {
        $payload = [
            "action" => "created",
        ];

        $headers = [
            "X-Hub-Signature" => "sha1=" . hash_hmac("sha1", json_encode($payload), config("services.github.webhook_secret")),
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ];

        $response = $this->withHeaders($headers)->postJson("/api/webhook", $payload);

        $this->assertSame(500, $response->getStatusCode());
    }

    public function testCreateOrganizationRequestWithoutSignature(): void
    {
        $payload = [
            "action" => "created",
            "installation" => [
                "account" => [
                    "type" => "Organization",
                    "login" => $this->organizationDto->name,
                    "id" => $this->organizationDto->githubId,
                    "avatar_url" => $this->organizationDto->avatarUrl,
                ],
            ],
        ];

        $headers = [
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ];

        $response = $this->withHeaders($headers)->postJson("/api/webhook", $payload);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function testRemoveMemberRequest(): void
    {
        $payload = [
            "action" => "member_removed",
            "organization" => [
                "login" => "test",
                "id" => 456,
                "avatar_url" => "http://example.com/avatar.png"
            ],
            "membership" => [
                "user" => [
                    "id" => 123,
                ],
            ],
        ];

        $headers = [
            "X-Hub-Signature" => "sha1=" . hash_hmac("sha1", json_encode($payload), config("services.github.webhook_secret")),
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ];

        $this->assertDatabaseHas("user_organization", [
            "organization_id" => $this->organization->id,
            "user_id" => $this->user->id,
        ]);

        $response = $this->withHeaders($headers)->postJson("/api/webhook", $payload);

        $this->assertSame(200, $response->getStatusCode());

        $this->assertDatabaseMissing("user_organization", [
            "organization_id" => $this->organization->id,
            "user_id" => $this->user->id,
        ]);
    }

    public function testRemoveMemberRequestWithWrongUser(): void
    {
        $payload = [
            "action" => "member_removed",
            "organization" => [
                "login" => "test",
                "id" => 456,
                "avatar_url" => "http://example.com/avatar.png"
            ],
            "membership" => [
                "user" => [
                    "id" => 789,
                ],
            ],
        ];

        $headers = [
            "X-Hub-Signature" => "sha1=" . hash_hmac("sha1", json_encode($payload), config("services.github.webhook_secret")),
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ];

        $this->withoutExceptionHandling()->expectException(ModelNotFoundException::class);
        $response = $this->withHeaders($headers)->postJson("/api/webhook", $payload);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testRemoveMemberRequestWithWrongOrganization(): void
    {
        $payload = [
            "action" => "member_removed",
            "organization" => [
                "login" => "test",
                "id" => 789,
                "avatar_url" => "http://example.com/avatar.png"
            ],
            "membership" => [
                "user" => [
                    "id" => 123,
                ],
            ],
        ];

        $headers = [
            "X-Hub-Signature" => "sha1=" . hash_hmac("sha1", json_encode($payload), config("services.github.webhook_secret")),
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ];

        $this->withoutExceptionHandling()->expectException(ModelNotFoundException::class);
        $response = $this->withHeaders($headers)->postJson("/api/webhook", $payload);

        $this->assertSame(404, $response->getStatusCode());
    }
}
