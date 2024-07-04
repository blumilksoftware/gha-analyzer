<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Integrations\GithubConnector;
use App\Http\Integrations\Requests\GetMembershipRequest;
use App\Http\Integrations\Requests\GetUsersOrganizationsRequest;
use App\Models\Organization;
use App\Models\User;

class AssignUserToOrganizationsService
{
    public function __construct(
        protected GithubConnector $githubConnector,
    ) {}

    public function assign(User $user): void
    {
        $response = $this->githubConnector->send(new GetUsersOrganizationsRequest($user));

        if ($response->json() !== null) {
            foreach ($response->json() as $data) {
                $organization = Organization::query()->where("github_id", $data["id"])->first();

                if ($this->getRole($user, $organization->name) === "admin") {
                    $user->organizations()->syncWithoutDetaching([
                        $organization->id => ["is_admin" => true],
                    ]);
                } else {
                    $user->organizations()->syncWithoutDetaching($organization->id);
                }
            }
        }
    }

    public function getRole(User $user, string $organizationName): string
    {
        $response = $this->githubConnector->send(new GetMembershipRequest($user, $organizationName));

        $data = $response->json();

        return $data["role"];
    }
}
