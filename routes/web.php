<?php

declare(strict_types=1);

use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\GithubController;
use App\Http\Controllers\RepositoriesController;
use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Route;

Route::inertia("/", "Home");

Route::get("/table", [TableController::class, "show"]);

Route::get("/repositories", [RepositoriesController::class, "show"]);

Route::get("/authors", [AuthorsController::class, "show"]);

Route::get("/auth/redirect", [GithubController::class, "redirect"]);
Route::get("/auth/callback", [GithubController::class, "callback"]);

Route::get("/{organizationId}/fetch", [GithubController::class, "fetchData"]);
