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
        $organizationId = intval($request->organizationId);

        if ((new PermissionService())->checkGitHubAppInstallation($organizationId) === false) {
            return redirect("/");
        }

        return $next($request);
    }
}
