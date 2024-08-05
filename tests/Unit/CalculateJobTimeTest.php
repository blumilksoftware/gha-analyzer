<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\CalculateJobTimeService;
use Tests\TestCase;

class CalculateJobTimeTest extends TestCase
{
    public function testCalculateJobTime(): void
    {
        $calculateJobTimeService = new CalculateJobTimeService();

        $jobTime = $calculateJobTimeService->calculate("2024-06-26T12:37:06Z", "2024-06-26T12:41:07Z");

        $this->assertSame($jobTime, 5);
    }
}
