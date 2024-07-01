<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Integrations\GithubConnector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Saloon\Config;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;
use Saloon\RateLimitPlugin\Exceptions\RateLimitReachedException;
use Saloon\RateLimitPlugin\Limit;
use Tests\TestCase;

class GithubConnectorTestVersion extends GithubConnector
{
    protected function resolveLimits(): array
    {
        return [
            Limit::allow(0)->everyHour(),
        ];
    }
}

class RateLimiterTest extends TestCase
{
    use RefreshDatabase;

    protected GithubConnector $githubConnector;
    protected string $requestUrl;
    protected Request $request;

    protected function setUp(): void
    {
        parent::setUp();
        Config::preventStrayRequests();
        Cache::clear();

        $this->requestUrl = "https://api.github.com/test";
        $this->githubConnector = new GithubConnector();

        $this->request = new class() extends Request {
            protected Method $method = Method::GET;

            public function resolveEndpoint(): string
            {
                return "/test";
            }
        };

        MockClient::destroyGlobal();
    }

    public function testRateLimitNotReached(): void
    {
        $mockClient = new MockClient([
            $this->requestUrl => MockResponse::make(body: "", status: 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectNotToPerformAssertions();

        $this->githubConnector->send($this->request);
    }

    public function testRateLimitReached(): void
    {
        $mockClient = new MockClient([
            $this->requestUrl => MockResponse::make(body: "", status: 200),
        ]);

        $githubConnector = new GithubConnectorTestVersion();

        $githubConnector->withMockClient($mockClient);

        $this->expectException(RateLimitReachedException::class);

        $githubConnector->send($this->request);
    }

    public function testResponse403(): void
    {
        $mockClient = new MockClient([
            $this->requestUrl => MockResponse::make(body: "", status: 403),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(RateLimitReachedException::class);

        $this->githubConnector->send($this->request);
    }

    public function testResponse429(): void
    {
        $mockClient = new MockClient([
            $this->requestUrl => MockResponse::make(body: "", status: 429),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(RateLimitReachedException::class);

        $this->githubConnector->send($this->request);

        Cache::clear();
    }
}
