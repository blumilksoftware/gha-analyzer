<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GithubWebhookService;
use Illuminate\Http\Request;

class GithubWebhookController extends Controller
{
    public function __construct(
        protected GithubWebhookService $webhookService,
    ) {}

    public function __invoke(Request $request): void
    {
        $actionType = $request->get("action");

        switch ($actionType) {
            case "created":
                $account = $request->get("installation")["account"];

                if ($account["type"] === config("services.organization.type")) {
                    $this->webhookService->createOrganization
                    (
                        $account["login"],
                        $account["id"],
                        $account["avatar_url"],
                    );
                }

                break;
            case "member_removed":
                $this->webhookService->removeMember
                (
                    $request->get("organization")["id"],
                    $request->get("membership")["user"]["id"],
                );

                break;
        }
    }
}
