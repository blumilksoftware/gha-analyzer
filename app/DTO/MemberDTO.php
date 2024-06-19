<?php

declare(strict_types=1);

namespace App\DTO;

class MemberDTO
{
    public function __construct(
        public int $githubId,
    ) {}

    public static function createFromArray(array $data): self
    {
        $member = new self(
            $data["id"],
        );

        return $member;
    }
}
