<?php

declare(strict_types=1);

use App\Http\Controllers\GithubController;
use Illuminate\Support\Facades\Route;
use Inertia\Response;

//Route::get("/", fn(): Response => inertia("Welcome"));
Route::inertia('/', 'Home');

Route::get("/auth/redirect", [GithubController::class, "redirect"]);
Route::get("/auth/callback", [GithubController::class, "callback"]);
