<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Handler\GithubWebhookHandler;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GithubWebhookController extends Controller
{

    public function handle(Request $request): void
    {
        $webhookHandler = new GithubWebhookHandler;

        $webhookHandler->handle($request);

        return;
    }
}
