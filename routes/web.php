<?php

declare(strict_types=1);

use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\GithubController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\RepositoriesController;
use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Route;

Route::middleware("auth")->group(function (): void {
    Route::get("/table", [TableController::class, "show"]);
    Route::get("/repositories", [RepositoriesController::class, "show"]);
    Route::get("/authors", [AuthorsController::class, "show"]);

    Route::get("/organization", [OrganizationController::class, "show"]);
    Route::post("/organization/{organizationId}/fetch", [OrganizationController::class, "fetchData"]);
    Route::get("/organization/{organizationId}/progress", [OrganizationController::class, "status"]);
})->middleware("auth");

Route::redirect("/", "table");
Route::get("/auth/login", [GithubController::class, "login"])->name("login");
Route::get("/auth/redirect", [GithubController::class, "redirect"]);
Route::get("/auth/callback", [GithubController::class, "callback"]);
