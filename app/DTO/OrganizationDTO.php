<?php

declare(strict_types=1);

namespace App\DTO;

class OrganizationDTO
{
    public function __construct(
        public string $name,
        public int $githubId,
        public string $avatarUrl,
    ) {}

    public static function createFromArray(array $data): self
    {
        $organization = new self(
            $data["login"],
            $data["id"],
            $data["avatar_url"],
        );

        return $organization;
    }
}
