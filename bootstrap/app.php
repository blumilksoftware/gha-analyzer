<?php

declare(strict_types=1);

use App\Exceptions\ErrorHandlingConfig;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . "/../routes/web.php",
        api: __DIR__ . "/../routes/api.php",
        commands: __DIR__ . "/../routes/console.php",
        health: "/up",
    )
    ->withMiddleware(function (Middleware $middleware): void {
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request): Response {
            if (!app()->environment(["local", "testing"]) && in_array($response->getStatusCode(), ErrorHandlingConfig::HANDLED_ERROR_CODES, true)) {
                return Inertia::render("Errors/Error", ["status" => $response->getStatusCode()])
                    ->toResponse($request)
                    ->setStatusCode($response->getStatusCode());
            } elseif ($response->getStatusCode() === ErrorHandlingConfig::HTTP_SESSION_EXPIRED) {
                return back()->with([
                    "message" => "The page expired, please try again.",
                ]);
            }

            return $response;
        });
    })->create();
