<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Services\OrganizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GithubWebhookHandler
{
    public function handle(Request $request): void
    {
        Log::info("Received webhook request:", $request->all());

        $this->validateGithubWebhook(config("services.github.webhook_secret"), $request);

        Log::info($request->getContent());

        $organizationService = new OrganizationService();

        switch ($this->getActionType($request)) {
            case "created":
                $organizationService->create(data_get($request, "installation.account"));

                break;
            case "member_removed":
                $organizationService->removeMember(
                    data_get($request, "membership.user"),
                    $request->json("organization"),
                );

                break;
        }
    }

    protected function validateGithubWebhook($known_token, Request $request): void
    {
        if (($signature = $request->headers->get("X-Hub-Signature")) === null) {
            throw new BadRequestHttpException("Header not set");
        }

        $signature_parts = explode("=", $signature);

        if (count($signature_parts) !== 2) {
            throw new BadRequestHttpException("signature has invalid format");
        }

        $known_signature = hash_hmac("sha1", $request->getContent(), $known_token);

        if (!hash_equals($known_signature, $signature_parts[1])) {
            throw new UnauthorizedException("Could not verify request signature " . $signature_parts[1]);
        }
    }

    protected function getActionType(Request $request): string
    {
        return $request->json("action");
    }
}
