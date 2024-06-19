<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Organization;
use App\Models\User;

class GithubWebhookService
{

    public function createOrganization(
        string $organizationName,
        int $organizationId,
        string $organizationAvatarUrl,
    ): void
    {
        Organization::create([
            "name" => $organizationName,
            "github_id" => $organizationId,
            "avatar_url" => $organizationAvatarUrl,
        ]);
    }

    public function removeMember(
        int $organizationId,
        int $memberId
    ): void
    {
        $user = User::query()->where("github_id", $memberId)->first();
        $organization = Organization::query()->where("github_id", $organizationId)->first();

        $user->organizations()->detach($organization->id);
    }
}
