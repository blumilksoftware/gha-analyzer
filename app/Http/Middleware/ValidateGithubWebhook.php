<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidateGithubWebhook
{
    public function handle(Request $request, Closure $next): Response
    {
        if (($signature = $request->headers->get("X-Hub-Signature")) === null) {
            throw new BadRequestHttpException("Header not set");
        }

        $signatureParts = explode("=", $signature);

        if (count($signatureParts) !== 2) {
            throw new BadRequestHttpException("signature has invalid format");
        }

        $knownSignature = hash_hmac("sha1", $request->getContent(), config("services.github.webhook_secret"));

        if (!hash_equals($knownSignature, $signatureParts[1])) {
            throw new UnauthorizedException("Could not verify request signature " . $signatureParts[1]);
        }

        return $next($request);
    }
}
