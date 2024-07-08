<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;

class CalculateJobTimeService
{
    public function calculate(string $startedAt, string $completedAt): int
    {
        $startedAt = Carbon::create($startedAt); 
        $completedAt = Carbon::create($completedAt);

        $diffInSeconds = $startedAt->diffInSeconds($completedAt);

        $diffInMinutes = (int)ceil($diffInSeconds / 60);

        return $diffInMinutes;
    }
}
