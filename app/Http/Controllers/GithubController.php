<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GithubController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver("github")->redirect();
    }

    public function callback(): RedirectResponse
    {
        $githubUser = Socialite::driver("github")->user();

        $user = User::updateOrCreate([
            "github_id" => $githubUser->id,
        ], [
            "name" => $githubUser->nickname,
            "email" => $githubUser->email,
            "github_token" => $githubUser->token,
            "github_refresh_token" => $githubUser->refreshToken,
        ]);

        Auth::login($user);

        $redirect_url = "https://github.com/apps/gha-analyzer";

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $githubUser->token,
        ])->get("https://api.github.com/user/installations");

        foreach ($response->json("installations") as $installation) {
            if ($installation["app_id"] === 918356) {
                $redirect_url = "/";
            }
        }

        return redirect($redirect_url);
    }
}
