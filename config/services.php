<?php

declare(strict_types=1);

return [
    "postmark" => [
        "token" => env("POSTMARK_TOKEN"),
    ],
    "ses" => [
        "key" => env("AWS_ACCESS_KEY_ID"),
        "secret" => env("AWS_SECRET_ACCESS_KEY"),
        "region" => env("AWS_DEFAULT_REGION", "us-east-1"),
    ],
    "slack" => [
        "notifications" => [
            "bot_user_oauth_token" => env("SLACK_BOT_USER_OAUTH_TOKEN"),
            "channel" => env("SLACK_BOT_USER_DEFAULT_CHANNEL"),
        ],
    ],
    "github" => [
        "client_id" => env("GITHUB_CLIENT_ID"),
        "client_secret" => env("GITHUB_CLIENT_SECRET"),
        "redirect" => env("GITHUB_REDIRECT_URL"),
        "webhook_secret" => env("GITHUB_WEBHOOK_SECRET"),
    ],
    "organization" => [
        "type" => "Organization",
    ],
    "runners" => [
        "multiplier" => [
            "ubuntu" => 1,
            "windows" => 2,
            "macos" => 10,
        ],
        "pricing" => [
            "ubuntu" => [
                "standard" => 0.008,
                "4" => 0.016,
                "8" => 0.032,
                "16" => 0.064,
                "32" => 0.128,
                "64" => 0.256,
            ],
            "windows" => [
                "standard" => 0.016,
                "4" => 0.032,
                "8" => 0.064,
                "16" => 0.128,
                "32" => 0.256,
                "64" => 0.512,
            ],
            "macos" => [
                "standard" => 0.08,
                "12" => 0.12,
            ],
        ],
    ],
];
