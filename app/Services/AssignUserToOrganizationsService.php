<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Integrations\GithubConnector;
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
        $request = new GetUsersOrganizationsRequest($user);

        $response = $this->githubConnector->send($request);

        if ($response->json() !== null) {
            foreach ($response->json() as $data) {
                $organization = Organization::query()->where("github_id", $data["id"])->first();
                $user->organizations()->syncWithoutDetaching($organization->id);
            }
        }
    }
}
