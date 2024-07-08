<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\OrganizationDTO;
use App\Http\Integrations\GithubConnector;
use App\Models\Organization;
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
        protected FetchRepositoriesService $fetchRepositoriesService,
        protected FetchWorkflowRunsService $fetchWorkflowRunsService,
        protected FetchWorkflowJobsService $fetchWorkflowJobsService,
        protected GithubConnector $githubConnector,
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
        $organization = Organization::query()->where("id", $organizationId)->first();
        $organizationDto = new OrganizationDTO(
            $organization->name,
            $organization->github_id,
            $organization->avatar_url,
        );

        $repositories = collect();
        $repositories = $this->fetchRepositoriesService->fetchRepositories($organizationDto);

        $workflowRuns = collect();

        foreach ($repositories as $repositoryDto) {
            $workflowRuns = $workflowRuns->union($this->fetchWorkflowRunsService->fetchWorkflowRuns($repositoryDto));
        }

        foreach ($workflowRuns as $workflowRunDto) {
            $this->fetchWorkflowJobsService->fetchWorkflowJobs($workflowRunDto);
        }

        return redirect()->back();
    }
}
