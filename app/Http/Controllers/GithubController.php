<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AssignUserToOrganizationsService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GithubController extends Controller
{
    public function __construct(
        protected AssignUserToOrganizationsService $assignUserService,
    ) {}

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

        $this->assignUserService->assign($user);

        return redirect("/");
    }

    public function login(): Response
    {
        return Inertia::render("Login");
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect("/");
    }
}
