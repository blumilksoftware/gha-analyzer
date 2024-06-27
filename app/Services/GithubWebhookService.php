<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\MemberDTO;
use App\DTO\OrganizationDTO;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GithubWebhookService
{
    public function createOrganization(
        OrganizationDTO $organizationDto
    ): void {
        Organization::create([
            "name" => $organizationDto->name,
            "github_id" => $organizationDto->githubId,
            "avatar_url" => $organizationDto->avatarUrl,
        ]);
    }

    public function removeMember(
        OrganizationDTO $organizationDto,
        MemberDTO $memberDto
    ): void {

        $user = User::query()->where("github_id", $memberDto->githubId)->firstOrFail();
        $organization = Organization::query()->where("github_id", $organizationDto->githubId)->firstOrFail();

        $user->organizations()->detach($organization->id);
    }
}
