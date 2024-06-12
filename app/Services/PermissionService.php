<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PermissionService
{
    public function checkPermissions(): bool
    {
        $permissions_given = false;

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . auth()->user()->github_token,
        ])->get("https://api.github.com/user/installations");

        if ($response->json("installations") !== null) {
            foreach ($response->json("installations") as $installation) {
                if ($installation["app_id"] === intval(env("GITHUB_APP_ID"))) {
                    $permissions_given = true;

                    break;
                }
            }
        }

        return $permissions_given;
    }
}
