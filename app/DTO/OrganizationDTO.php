<?php

declare(strict_types=1);

namespace App\DTO;

class OrganizationDTO
{
    public function __construct(
        public string $name,
        public int $github_id,
        public string $avatar_url,
    ) {}
}
