<?php

declare(strict_types=1);

namespace App\Http\Integrations\Requests;

use App\Models\User;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetMembershipRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected User $user,
        protected string $organizationName,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/orgs/" . $this->organizationName . "/memberships/" . $this->user->name;
    }

    protected function defaultHeaders(): array
    {
        return [
            "Authorization" => "Bearer " . $this->user->github_token,
        ];
    }
}
