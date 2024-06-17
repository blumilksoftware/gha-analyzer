<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Organization;
use App\Models\User;

class OrganizationService
{
    public function create(array $organization): void
    {
        if ($organization["type"] === "Organization") {
            Organization::create([
                "name" => $organization["login"],
                "github_id" => $organization["id"],
                "avatar_url" => $organization["avatar_url"],
            ]);
        }
    }

    public function removeMember(array $member, array $organizationData): void
    {
        $user = User::where("github_id", $member["id"])->first();
        $organization = Organization::where("github_id", $organizationData["id"])->first();
        $user->organizations()->detach($organization["id"]);
    }
}
