<?php

declare(strict_types=1);

namespace App\Http\Integrations;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class GithubConnector extends Connector
{
    use AlwaysThrowOnErrors;

    public function resolveBaseUrl(): string
    {
        return "https://api.github.com";
    }

    protected function defaultHeaders(): array
    {
        return [
            "Content-Type" => "application/json",
            "Accept" => "application/json",
        ];
    }
}
