<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AssignUserToOrganizationsService
{
    public function assign(): void
    {
        $user = User::query()->where("id", auth()->user()->id)->first();

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $user->github_token,
        ])->get("https://api.github.com/user/orgs");

        if ($response->json() !== null) {
            foreach ($response->json() as $data) {
                $organization = Organization::query()->where("github_id", $data["id"])->first();
                $user->organizations()->syncWithoutDetaching($organization->id);
            }
        }
    }
}
