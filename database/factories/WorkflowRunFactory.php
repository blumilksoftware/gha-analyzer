<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Repository;
use App\Models\WorkflowRun;
use App\Models\WorkflowJob;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowRunFactory extends Factory
{
    public function definition(): array
    {
        return [
            "github_id" => $this->faker->unique()->randomNumber(),
            "name" => $this->faker->word(),
            "repository_id" => Repository::factory(),
            "created_at" => $this->faker->iso8601(),
        ];
    }

    public function hasJobs(int $count)
    {
        return $this->afterCreating(function (WorkflowRun $workflowRun) use ($count) {
            WorkflowJob::factory()->count($count)->create(['workflow_run_id' => $workflowRun->id]);
        });
    }
}
