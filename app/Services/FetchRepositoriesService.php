<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\OrganizationDTO;
use App\Exceptions\FetchingRepositoriesErrorException;
use App\Http\Integrations\GithubConnector;
use App\Http\Integrations\Requests\GetRepositoriesRequest;
use App\Models\Organization;
use App\Models\Repository;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
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

        $userOrganizationExists = $user->organizations()
            ->where("organization_id", $organization->id)
            ->where("is_admin", true)
            ->exists();

        if ($userOrganizationExists) {
            try {
                $request = new GetRepositoriesRequest($organizationDto);

                $response = $this->githubConnector->send($request);

                $this->storeRepositories($response->dto());
            } catch (Exception $exception) {
                throw new FetchingRepositoriesErrorException(
                    message: "Error ocurred while fetching repositories",
                    previous: $exception,
                );
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    public function storeRepositories(Collection $repositories): void
    {
        if (!$repositories->isEmpty()) {
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
}
