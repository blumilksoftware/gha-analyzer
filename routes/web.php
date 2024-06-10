<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\UserController;

Route::get("/", fn(): Response => inertia("Welcome"));

Route::get('/auth/redirect', function () {
    return Socialite::driver('github')->redirect();
});
Route::get('/auth/callback', [UserController::class, 'login']);