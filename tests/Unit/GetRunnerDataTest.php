<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\GetRunnerDataService;
use InvalidArgumentException;
use Tests\TestCase;

class GetRunnerDataTest extends TestCase
{
    protected GetRunnerDataService $getRunnerDataService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->getRunnerDataService = new GetRunnerDataService();
    }

    public function testGetRunnerDataWithStandardRunner(): void
    {
        $runnerLabels = [
            "ubuntu-latest",
            "foo",
            "bar",
        ];

        $runnerData = $this->getRunnerDataService->getRunnerData($runnerLabels);

        $expectedData = [
            "os" => "ubuntu",
            "cores" => "standard",
            "multiplier" => config("services.runners.multiplier.ubuntu"),
            "pricing" => config("services.runners.pricing.ubuntu.standard"),
        ];

        $this->assertSame($runnerData, $expectedData);
    }

    public function testGetRunnerDataWithLargeRunner(): void
    {
        $runnerLabels = [
            "windows-latest-16core",
            "foo",
            "bar",
        ];

        $runnerData = $this->getRunnerDataService->getRunnerData($runnerLabels);

        $expectedData = [
            "os" => "windows",
            "cores" => "16",
            "multiplier" => config("services.runners.multiplier.windows"),
            "pricing" => config("services.runners.pricing.windows.16"),
        ];

        $this->assertSame($runnerData, $expectedData);
    }

    public function testGetRunnerDataWithoutMatchingLabel(): void
    {
        $runnerLabels = [
            "foo",
            "bar",
        ];

        $this->expectException(InvalidArgumentException::class);

        $runnerData = $this->getRunnerDataService->getRunnerData($runnerLabels);
    }
}
