<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Controllers\Api\GithubWebhookController;
use App\Models\Organization;
use App\Models\User;
use App\Services\GithubWebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Request;
use Mockery;
use Tests\TestCase;

class GithubWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        if ($container = Mockery::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }
        Mockery::close();
        parent::tearDown();
    }

    public function testInvokeWithCreatedAction(): void
    {
        $webhookService = Mockery::mock(GithubWebhookService::class);
        $controller = new GithubWebhookController($webhookService);

        $request = Request::create("/webhook", "POST", [
            "action" => "created",
            "installation" => [
                "account" => [
                    "type" => config("services.organization.type"),
                    "login" => "test",
                    "id" => 123,
                    "avatar_url" => "http://example.com/avatar.png",
                ],
            ],
        ]);

        $webhookService->shouldReceive("createOrganization")
            ->once()
            ->with("test", 123, "http://example.com/avatar.png");

        $controller($request);
    }

    public function testInvokeWithIncorrectType(): void
    {
        $webhookService = Mockery::mock(GithubWebhookService::class);
        $controller = new GithubWebhookController($webhookService);

        $request = Request::create("/webhook", "POST", [
            "action" => "created",
            "installation" => [
                "account" => [
                    "type" => "wrong_type",
                    "login" => "test",
                    "id" => 123,
                    "avatar_url" => "http://example.com/avatar.png",
                ],
            ],
        ]);

        $webhookService->shouldNotReceive("createOrganization");

        $controller($request);
    }

    public function testInvokeWithMemberRemovedAction(): void
    {
        $webhookService = Mockery::mock(GithubWebhookService::class);
        $controller = new GithubWebhookController($webhookService);

        $request = Request::create("/webhook", "POST", [
            "action" => "member_removed",
            "organization" => [
                "id" => 123,
            ],
            "membership" => [
                "user" => [
                    "id" => 456,
                ],
            ],
        ]);

        $webhookService->shouldReceive("removeMember")
            ->once()
            ->with(123, 456);

        $controller($request);
    }

    public function testCreateOrganizationRequestWithSignature(): void
    {
        $organizationName = "test";
        $organizationId = 123;
        $organizationAvatarUrl = "http://example.com/avatar.png";

        $payload = [
            "action" => "created",
            "installation" => [
                "account" => [
                    "type" => "Organization",
                    "login" => $organizationName,
                    "id" => $organizationId,
                    "avatar_url" => $organizationAvatarUrl,
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
            "name" => $organizationName,
            "github_id" => $organizationId,
            "avatar_url" => $organizationAvatarUrl,
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
        $organizationName = "test";
        $organizationId = 123;
        $organizationAvatarUrl = "http://example.com/avatar.png";

        $payload = [
            "action" => "created",
            "installation" => [
                "account" => [
                    "type" => "Organization",
                    "login" => $organizationName,
                    "id" => $organizationId,
                    "avatar_url" => $organizationAvatarUrl,
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
        $user = User::factory()->create(["github_id" => 123]);
        $organization = Organization::factory()->create(["github_id" => 456]);
        $user->organizations()->attach($organization->id);

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
            "organization_id" => $organization->id,
            "user_id" => $user->id,
        ]);

        $response = $this->withHeaders($headers)->postJson("/api/webhook", $payload);

        $this->assertSame(200, $response->getStatusCode());

        $this->assertDatabaseMissing("user_organization", [
            "organization_id" => $organization->id,
            "user_id" => $user->id,
        ]);
    }

    public function testRemoveMemberRequestWithWrongUser(): void
    {
        $user = User::factory()->create(["github_id" => 123]);
        $organization = Organization::factory()->create(["github_id" => 456]);
        $user->organizations()->attach($organization->id);

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

        $this->assertDatabaseHas("user_organization", [
            "organization_id" => $organization->id,
            "user_id" => $user->id,
        ]);

        $response = $this->withHeaders($headers)->postJson("/api/webhook", $payload);

        $this->assertSame(500, $response->getStatusCode());
    }

    public function testRemoveMemberRequestWithWrongOrganization(): void
    {
        $user = User::factory()->create(["github_id" => 123]);
        $organization = Organization::factory()->create(["github_id" => 456]);
        $user->organizations()->attach($organization->id);

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

        $this->assertDatabaseHas("user_organization", [
            "organization_id" => $organization->id,
            "user_id" => $user->id,
        ]);

        $response = $this->withHeaders($headers)->postJson("/api/webhook", $payload);

        $this->assertSame(500, $response->getStatusCode());
    }
}
