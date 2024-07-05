<?php

declare(strict_types=1);

namespace App\Services;

use DateTime;

class CalculateJobTimeService
{
    public function calculate(string $startedAt, string $completedAt): int
    {
        $startedAt = new DateTime($startedAt);
        $completedAt = new DateTime($completedAt);

        $interval = $startedAt->diff($completedAt);

        $seconds = ($interval->days * 24 * 60 * 60) + 
                ($interval->h * 60 * 60) + 
                ($interval->i * 60) + 
                $interval->s;

        $minutes = (int)ceil($seconds / 60);

        return $minutes;
    }
}
