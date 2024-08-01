<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\WorkflowRun;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowJobFactory extends Factory
{
    public function definition(): array
    {
        $osArray = ["ubuntu", "windows", "macos"];
        $typeArray = [
            ["standard", "4", "8", "16", "32", "64"],
            ["standard", "4", "8", "16", "32", "64"],
            ["standard", "12"],
        ];
        $runnerOsKey = array_rand($osArray, 1);
        $runnerTypeKey = array_rand($typeArray[$runnerOsKey], 1);
        $runnerOs = $osArray[$runnerOsKey];
        $runnerType = $typeArray[$runnerOsKey][$runnerTypeKey];

        return [
            "github_id" => $this->faker->unique()->randomNumber(),
            "name" => $this->faker->word(),
            "workflow_run_id" => WorkflowRun::factory(),
            "runner_os" => $runnerOs,
            "runner_type" => $runnerType,
            "minutes" => $this->faker->randomNumber(),
            "multiplier" => config("services.runners.multiplier." . $runnerOs),
            "price_per_unit" => config("services.runners.pricing." . $runnerOs . "." . $runnerType),
        ];
    }
}
