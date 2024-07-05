<?php

namespace Database\Factories;

use App\Models\WorkflowJob;
use App\Models\WorkflowRun;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowJobFactory extends Factory
{
    protected $model = WorkflowJob::class;

    public function definition()
    {
        return [
            'github_id' => $this->faker->unique()->randomNumber(),
            'name' => $this->faker->word(),
            'workflow_run_id' => WorkflowRun::factory(),
            'runner_os' => $this->faker->randomElement(['linux', 'windows', 'macOS']),
            'runner_type' => $this->faker->randomElement(['X64', 'ARM64']),
            'minutes' => $this->faker->numberBetween(1, 120),
            'multiplier' => $this->faker->randomNumber(2, 0, 1),
            'price_per_unit' => $this->faker->randomFloat(2, 0.01, 10.00),
        ];
    }
}