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

    protected $webhookService;
    protected $webhookController;
    protected $organization;
    protected $user;
    protected $organizationName;
    protected $organizationId;
    protected $organizationAvatarUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->webhookService = Mockery::mock(GithubWebhookService::class);
        $this->webhookController = new GithubWebhookController($this->webhookService);

        $this->user = User::factory()->create(["github_id" => 123]);
        $this->organization = Organization::factory()->create(["github_id" => 456]);
        $this->user->organizations()->attach($this->organization->id);

        $this->organizationName = "test";
        $this->organizationId = 123;
        $this->organizationAvatarUrl = "http://example.com/avatar.png";
    }

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

        $this->webhookService->shouldReceive("createOrganization")
            ->once()
            ->with("test", 123, "http://example.com/avatar.png");

        $this->webhookController->__invoke($request);
    }

    public function testInvokeWithIncorrectType(): void
    {
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

        $this->webhookService->shouldNotReceive("createOrganization");

        $this->webhookController->__invoke($request);
    }

    public function testInvokeWithMemberRemovedAction(): void
    {
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

        $this->webhookService->shouldReceive("removeMember")
            ->once()
            ->with(123, 456);

        $this->webhookController->__invoke($request);
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

        $this->assertDatabaseHas("user_organization", [
            "organization_id" => $this->organization->id,
            "user_id" => $this->user->id,
        ]);

        $response = $this->withHeaders($headers)->postJson("/api/webhook", $payload);

        $this->assertSame(500, $response->getStatusCode());
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

        $this->assertDatabaseHas("user_organization", [
            "organization_id" => $this->organization->id,
            "user_id" => $this->user->id,
        ]);

        $response = $this->withHeaders($headers)->postJson("/api/webhook", $payload);

        $this->assertSame(500, $response->getStatusCode());
    }
}
