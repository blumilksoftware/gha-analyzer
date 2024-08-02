<?php

declare(strict_types=1);

namespace App\Http\Integrations;

use Illuminate\Support\Facades\Cache;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Helpers\RetryAfterHelper;
use Saloon\RateLimitPlugin\Limit;
use Saloon\RateLimitPlugin\Stores\LaravelCacheStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Symfony\Component\HttpFoundation\Response as Status;

class GithubConnector extends Connector
{
    use AlwaysThrowOnErrors;
    use HasRateLimits;

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

    protected function resolveLimits(): array
    {
        return [
            Limit::allow(config("services.rate_limit"))->everyHour(),
        ];
    }

    protected function resolveRateLimitStore(): RateLimitStore
    {
        return new LaravelCacheStore(Cache::store());
    }

    protected function handleTooManyAttempts(Response $response, Limit $limit): void
    {
        if (!in_array($response->status(), [Status::HTTP_TOO_MANY_REQUESTS, Status::HTTP_FORBIDDEN], false)) {
            return;
        }

        $limit->exceeded(
            releaseInSeconds: RetryAfterHelper::parse($response->header("Retry-After")),
        );
    }
}
