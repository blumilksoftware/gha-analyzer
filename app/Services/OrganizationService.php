<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\MemberDTO;
use App\DTO\OrganizationDTO;
use App\Models\Organization;
use App\Models\User;

class OrganizationService
{
    public function create(OrganizationDTO $organization): void
    {
        Organization::create([
            "name" => $organization->name,
            "github_id" => $organization->githubId,
            "avatar_url" => $organization->avatarUrl,
        ]);
    }

    public function removeMember(MemberDTO $member, OrganizationDTO $organizationData): void
    {
        $user = User::query()->where("github_id", $member->githubId)->first();
        $organization = Organization::query()->where("github_id", $organizationData->githubId)->first();

        $user->organizations()->detach($organization->id);
    }
}
