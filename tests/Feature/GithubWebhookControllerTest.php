<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Controllers\Api\GithubWebhookController;
use App\Services\GithubWebhookService;
use Illuminate\Support\Facades\Request;
use Mockery;
use Tests\TestCase;

class GithubWebhookControllerTest extends TestCase
{
    protected function tearDown(): void
    {
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

        $this->assertTrue(true);
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

        $this->assertTrue(true);
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

        $this->assertTrue(true);
    }
}
