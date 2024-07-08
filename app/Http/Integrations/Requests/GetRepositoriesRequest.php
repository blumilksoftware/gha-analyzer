<?php

declare(strict_types=1);

namespace App\Http\Integrations\Requests;

use App\DTO\OrganizationDTO;
use App\DTO\RepositoryDTO;
use App\Models\Organization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetRepositoriesRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected OrganizationDTO $organizationDto,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/orgs/" . $this->organizationDto->name . "/repos";
    }

    public function createDtoFromResponse(Response $response): Collection
    {
        $organization = Organization::query()->where("github_id", $this->organizationDto->githubId)->firstOrFail();

        $repositories = collect();

        if ($response->json() !== null) {
            foreach ($response->json() as $data) {
                $repositories->push(new RepositoryDTO(
                    githubId: $data["id"],
                    name: $data["name"],
                    organizationId: $organization->id,
                    isPrivate: $data["private"],
                ));
            }
        }

        return $repositories;
    }

    protected function defaultHeaders(): array
    {
        return [
            "Authorization" => "Bearer " . Auth::user()->github_token,
        ];
    }
}
