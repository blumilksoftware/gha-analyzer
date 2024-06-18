<?php

declare(strict_types=1);

use App\Http\Controllers\Api\GithubWebhookController;
use App\Http\Middleware\ValidateGithubWebhook;
use Illuminate\Support\Facades\Route;

Route::post("/webhook", GithubWebhookController::class)
    ->middleware(ValidateGithubWebhook::class);
