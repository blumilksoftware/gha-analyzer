<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Handlers\GithubWebhookHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GithubWebhookController extends Controller
{
    public function handle(Request $request): void
    {
        $webhookHandler = new GithubWebhookHandler();

        $webhookHandler->handle($request);
    }
}
