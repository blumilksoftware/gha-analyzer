<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\OrganizationDTO;
use App\Exceptions\UnableToMakeDtoFromResponseException;
use App\Http\Integrations\GithubConnector;
use App\Http\Integrations\Requests\GetRepositoriesRequest;
use App\Models\Organization;
use App\Models\Repository;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class FetchRepositoriesService
{
    public function __construct(
        protected GithubConnector $githubConnector,
    ) {}

    public function fetchRepositories(OrganizationDTO $organizationDto): void
    {
        $organization = Organization::query()->where("github_id", $organizationDto->githubId)->firstOrFail();
        $user = User::query()->where("id", Auth::user()->id)->firstOrFail();

        if ($user->organizations()
            ->where("organization_id", $organization->id)
            ->where("is_admin", true)
            ->exists()) {
            $request = new GetRepositoriesRequest($organizationDto);

            $response = $this->githubConnector->send($request);

            try {
                $this->storeRepositories($response->dto());
            } catch (Exception) {
                throw new UnableToMakeDtoFromResponseException("Unable to make DTO from response");
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    public function storeRepositories(array $repositories): void
    {
        foreach ($repositories as $repositoryDto) {
            Repository::firstOrCreate([
                "github_id" => $repositoryDto->githubId,
                "name" => $repositoryDto->name,
                "organization_id" => $repositoryDto->organizationId,
                "is_private" => $repositoryDto->isPrivate,
            ]);
        }
    }
}
