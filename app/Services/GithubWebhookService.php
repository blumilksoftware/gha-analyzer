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
    ): void {
        Organization::create([
            "name" => $organizationName,
            "github_id" => $organizationId,
            "avatar_url" => $organizationAvatarUrl,
        ]);
    }

    public function removeMember(
        int $organizationId,
        int $memberId,
    ): void {
        $user = User::query()->where("github_id", $memberId)->first();
        if (!$user) {
            throw new \InvalidArgumentException("User with GitHub ID {$memberId} not found.");
        }

        $organization = Organization::query()->where("github_id", $organizationId)->first();
        if (!$organization) {
            throw new \InvalidArgumentException("Organization with GitHub ID {$organizationId} not found.");
        }

        $user->organizations()->detach($organization->id);
    }
}
