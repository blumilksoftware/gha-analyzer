<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Integrations\GithubConnector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Saloon\Config as SaloonConfig;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;
use Saloon\RateLimitPlugin\Exceptions\RateLimitReachedException;
use Tests\TestCase;

class RateLimiterTest extends TestCase
{
    use RefreshDatabase;

    protected GithubConnector $githubConnector;
    protected string $requestUrl;
    protected Request $request;

    protected function setUp(): void
    {
        parent::setUp();
        SaloonConfig::preventStrayRequests();
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

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
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
        Config::set("services.rate_limit", 0);

        $mockClient = new MockClient([
            $this->requestUrl => MockResponse::make(body: "", status: 200),
        ]);

        $this->githubConnector->withMockClient($mockClient);

        $this->expectException(RateLimitReachedException::class);

        $this->githubConnector->send($this->request);
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
