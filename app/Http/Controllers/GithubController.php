<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Integrations\GithubConnector;
use App\Jobs\FetchDataFromApi;
use App\Models\User;
use App\Services\AssignUserToOrganizationsService;
use App\Services\FetchRepositoriesService;
use App\Services\FetchWorkflowJobsService;
use App\Services\FetchWorkflowRunsService;
use Illuminate\Support\Facades\Auth;
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

    public function fetchData($organizationId): RedirectResponse
    {
        $userId = Auth::user()->id;

        FetchDataFromApi::dispatch(
            (int)$organizationId,
            $githubConnector = new GithubConnector(),
            new FetchRepositoriesService($githubConnector, $userId),
            new FetchWorkflowRunsService($githubConnector, $userId),
            new FetchWorkflowJobsService($githubConnector, $userId),
        );

        return redirect()->back();
    }
}
