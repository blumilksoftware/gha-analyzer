<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GithubWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $organization;
    protected $user;
    protected $organizationName;
    protected $organizationId;
    protected $organizationAvatarUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(["github_id" => 123]);
        $this->organization = Organization::factory()->create(["github_id" => 456]);
        $this->user->organizations()->attach($this->organization->id);

        $this->organizationName = "test";
        $this->organizationId = 123;
        $this->organizationAvatarUrl = "http://example.com/avatar.png";
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
                    "login" => $this->organizationName,
                    "id" => $this->organizationId,
                    "avatar_url" => $this->organizationAvatarUrl,
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
            "name" => $this->organizationName,
            "github_id" => $this->organizationId,
            "avatar_url" => $this->organizationAvatarUrl,
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
                    "login" => $this->organizationName,
                    "id" => $this->organizationId,
                    "avatar_url" => $this->organizationAvatarUrl,
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
                "id" => 456,
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
                "id" => 456,
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
                "id" => 789,
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
