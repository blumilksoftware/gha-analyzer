<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\OrganizationDTO;
use Illuminate\Http\Request;

class GithubWebhookService
{
    protected $organizationService;

    public function __construct()
    {
        $this->organizationService = new OrganizationService();
    }

    public function handle(Request $request): void
    {
        switch ($this->getActionType($request)) {
            case "created":
                $data = data_get($request, "installation.account");

                if ($data["type"] === config("services.organization.type")) {
                    $organization = new OrganizationDTO(
                        $data["login"],
                        $data["id"],
                        $data["avatar_url"],
                    );

                    $this->organizationService->create($organization);
                }

                break;
            case "member_removed":
                $this->organizationService->removeMember(
                    data_get($request, "membership.user"),
                    $request->json("organization"),
                );

                break;
        }
    }

    protected function getActionType(Request $request): string
    {
        return $request->json("action");
    }
}
