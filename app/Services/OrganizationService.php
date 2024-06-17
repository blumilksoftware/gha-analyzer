<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\OrganizationDTO;
use App\Models\Organization;
use App\Models\User;

class OrganizationService
{
    public function create(OrganizationDTO $organization): void
    {
        Organization::create([
            "name" => $organization->name,
            "github_id" => $organization->github_id,
            "avatar_url" => $organization->avatar_url,
        ]);
    }

    public function removeMember(array $member, array $organizationData): void
    {
        $user = User::where("github_id", $member["id"])->first();
        $organization = Organization::where("github_id", $organizationData["id"])->first();
        $user->organizations()->detach($organization["id"]);
    }
}
