<?php

declare(strict_types=1);

namespace App\DTO;

class RepositoryDTO
{
    public function __construct(
        public int $githubId,
        public string $name,
        public int $organizationId,
        public bool $isPrivate,
    ) {}
}
