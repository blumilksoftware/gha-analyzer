<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\MemberDTO;
use App\DTO\OrganizationDTO;
use Illuminate\Http\Request;

class GithubWebhookService
{
    public function __construct(
        protected OrganizationService $organizationService,
    ) {}

    public function handleRequest(Request $request): void
    {
        switch ($this->getActionType($request)) {
            case "created":
                $data = $request->get("installation")["account"];

                if ($data["type"] === config("services.organization.type")) {
                    $organization = OrganizationDTO::createFromArray($data);

                    $this->organizationService->create($organization);
                }

                break;
            case "member_removed":
                $organization = OrganizationDTO::createFromArray($request->get("organization"));
                $member = MemberDTO::createFromArray($request->get("membership")["user"]);

                $this->organizationService->removeMember(
                    $member,
                    $organization,
                );

                break;
        }
    }

    protected function getActionType(Request $request): string
    {
        return $request->json("action");
    }
}
