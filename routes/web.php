<?php

declare(strict_types=1);

use App\Http\Controllers\GithubController;
use Illuminate\Support\Facades\Route;

Route::inertia("/", "Home");
Route::inertia("/table", "Table");
Route::inertia("/repositories", "Repositories");
Route::inertia("/authors", "Authors");

Route::get("/auth/redirect", [GithubController::class, "redirect"]);
Route::get("/auth/callback", [GithubController::class, "callback"]);
