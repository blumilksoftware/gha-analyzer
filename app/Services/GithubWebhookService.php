<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        try {
            $user = User::query()->where("github_id", $memberId)->firstOrFail();
            $organization = Organization::query()->where("github_id", $organizationId)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw $e;
        }

        $user->organizations()->detach($organization->id);
    }
}
