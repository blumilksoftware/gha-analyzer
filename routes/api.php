<?php

declare(strict_types=1);

use App\Http\Controllers\Api\GithubWebhookController;
use App\Http\Controllers\ColorsController;
use App\Http\Controllers\LogsController;
use App\Http\Middleware\ValidateGithubWebhook;
use Illuminate\Support\Facades\Route;

Route::post("/webhook", GithubWebhookController::class)
    ->middleware(ValidateGithubWebhook::class);

Route::get("/data/colors", [ColorsController::class, 'getData']);
Route::get("/data/sampleLogs", [LogsController::class, 'getSampleLogs']);