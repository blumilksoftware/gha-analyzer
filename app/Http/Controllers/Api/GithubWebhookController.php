<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GithubWebhookService;
use Illuminate\Http\Request;

class GithubWebhookController extends Controller
{
    public function __construct(
        protected GithubWebhookService $webhookService,
    ) {}

    public function __invoke(Request $request): void
    {
        $this->webhookService->handleRequest($request);
    }
}
