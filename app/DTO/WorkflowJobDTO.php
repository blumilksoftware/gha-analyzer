<?php

declare(strict_types=1);

namespace App\DTO;

class WorkflowJobDTO
{
    public function __construct(
        public int $githubId,
        public string $name,
        public string $runner_os,
        public string $runner_type,
        public int $workflowRunId,
        public int $minutes,
        public int $multiplier,
        public float $pricePerUnit
    ) {}
}
