<?php

namespace App\Handler;

use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Support\Facades\Log;

class GithubWebhookHandler
{

    protected function validateGithubWebhook($known_token, Request $request)
    {
        if (($signature = $request->headers->get('X-Hub-Signature')) == null) {
            throw new BadRequestHttpException('Header not set');
        }

        $signature_parts = explode('=', $signature);

        if (count($signature_parts) != 2) {
            throw new BadRequestHttpException('signature has invalid format');
        }

        $known_signature = hash_hmac('sha1', $request->getContent(), $known_token);

        if (! hash_equals($known_signature, $signature_parts[1])) {
            throw new UnauthorizedException('Could not verify request signature ' . $signature_parts[1]);
        }
    }

    public function handle(Request $request)
    {

        Log::info('Received webhook request:', $request->all());

        $this->validateGithubWebhook(config('services.github.webhook_secret'), $request);

        Log::info($request->getContent());
    }
}
