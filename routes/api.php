<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GithubWebhookController;

Route::post("/webhook", [GithubWebhookController::class, "handle"]);
