<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\FetchRepositoriesJob;
use App\Models\User;
use App\Services\AssignUserToOrganizationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
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

    public function fetchData(int $organizationId): JsonResponse
    {
        $jobs = [
            new FetchRepositoriesJob($organizationId, Auth::user()->id),
        ];

        $batch = Bus::batch($jobs)->dispatch();

        return response()->json(["batch" => $batch->id]);
    }

    public function status(string $batchId): JsonResponse
    {
        $batch = Bus::findBatch($batchId);

        if ($batch === null) {
            return \response()->json(["message" => "Batch not found"], 404);
        }

        if ($batch->cancelled()) {
            return \response()->json(["message" => "There was an error, please try again latter."], 500);
        }

        return response()->json([
            "all" => $batch->totalJobs,
            "done" => $batch->processedJobs(),
            "finished" => $batch->finished(),
        ]);
    }
}
