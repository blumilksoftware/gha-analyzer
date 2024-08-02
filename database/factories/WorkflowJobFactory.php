<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\WorkflowRun;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowJobFactory extends Factory
{
    public function definition(): array
    {
        $osOptions = config("services.runners.os");
        $runnerPricing = config("services.runners.pricing");

        $selectedOs = $osOptions[array_rand($osOptions, 1)];
        $availableRunnerTypes = $runnerPricing[$selectedOs];
        $selectedRunnerType = array_rand($availableRunnerTypes, 1);

        $multiplier = config("services.runners.multiplier." . $selectedOs);
        $pricePerUnit = config("services.runners.pricing." . $selectedOs . "." . $selectedRunnerType);

        return [
            "github_id" => $this->faker->unique()->randomNumber(),
            "name" => $this->faker->word(),
            "workflow_run_id" => WorkflowRun::factory(),
            "runner_os" => $selectedOs,
            "runner_type" => $selectedRunnerType,
            "minutes" => $this->faker->randomNumber(),
            "multiplier" => $multiplier,
            "price_per_unit" => $pricePerUnit,
        ];
    }
}
