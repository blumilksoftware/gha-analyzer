<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

        return redirect("/");
    }
}
