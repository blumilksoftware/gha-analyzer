<?php

declare(strict_types=1);

namespace App\DTO;

class WorkflowActorDTO
{
    public function __construct(
        public int $githubId,
        public string $name,
        public string $avatarUrl,
    ) {}

    public static function createFromArray(array $data): self
    {
        return new self(
            $data["id"],
            $data["login"],
            $data["avatar_url"],
        );
    }
}
