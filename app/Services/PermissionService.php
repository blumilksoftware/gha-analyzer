<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PermissionService
{
    public function checkGitHubAppInstallation(int $organizationId): bool
    {
        $permissionsGiven = false;

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . auth()->user()->github_token,
        ])->get("https://api.github.com/user/orgs");

        if ($response->json() !== null) {
            foreach ($response->json() as $organization) {
                if ($organization["id"] === $organizationId) {
                    $permissionsGiven = true;

                    break;
                }
            }
        }

        return $permissionsGiven;
    }
}
