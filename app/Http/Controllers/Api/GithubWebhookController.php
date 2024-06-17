<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GithubWebhookService;
use Illuminate\Http\Request;

class GithubWebhookController extends Controller
{
    protected $webhookService;

    public function __construct()
    {
        $this->webhookService = new GithubWebhookService();
    }

    public function handle(Request $request): void
    {
        $this->webhookService->handle($request);
    }
}
