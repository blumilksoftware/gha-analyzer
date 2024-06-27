<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\OrganizationDTO;
use App\Http\Controllers\Controller;
use App\Services\GithubWebhookService;
use Illuminate\Http\Request;
use App\DTO\MemberDTO;

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
                        OrganizationDTO::createFromArray($account)
                    );
                }

                break;
            case "member_removed":
                $this->webhookService->removeMember
                (
                    OrganizationDTO::createFromArray($request->get("organization")),
                    MemberDTO::createFromArray($request->get("membership")["user"])
                );

                break;
        }
    }
}
