<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\MemberDTO;
use App\DTO\OrganizationDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GithubWebhookService
{
    public function __construct(
        protected OrganizationService $organizationService,
    ) {}

    public function handle(Collection $data): void
    {
        switch ($this->getActionType($data)) {
            case "created":
                $organizationData = $data["installation"]["account"];

                if ($organizationData["type"] === config("services.organization.type")) {
                    $organization = OrganizationDTO::createFromArray($organizationData);

                    $this->organizationService->create($organization);
                }

                break;
            case "member_removed":
                $organization = OrganizationDTO::createFromArray($data["organization"]);
                $member = MemberDTO::createFromArray($data["membership"]["user"]);

                $this->organizationService->removeMember(
                    $member,
                    $organization,
                );

                break;
        }
    }

    protected function getActionType(Collection $data): string
    {
        return $data["action"];
    }
}
