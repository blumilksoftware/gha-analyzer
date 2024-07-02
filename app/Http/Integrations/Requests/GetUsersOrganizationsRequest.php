<?php

declare(strict_types=1);

namespace App\Http\Integrations\Requests;

use App\Models\User;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetUsersOrganizationsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected User $user,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/user/orgs";
    }

    protected function defaultHeaders(): array
    {
        return [
            "Authorization" => "Bearer " . $this->user->github_token,
        ];
    }
}
