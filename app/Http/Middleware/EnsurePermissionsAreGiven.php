<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermissionsAreGiven
{
    public function handle(Request $request, Closure $next): Response
    {
        if ((new PermissionService())->checkPermissions() === false) {
            return redirect(env("GITHUB_APP_URL"));
        }

        return $next($request);
    }
}
