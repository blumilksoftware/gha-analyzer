<?php

declare(strict_types=1);

namespace App\DTO;
use DateTime;

class WorkflowRunDTO
{
    public function __construct(
        public int $githubId,
        public string $name,
        public int $repositoryId,
        public DateTime $created_at,
    ) {}
}
