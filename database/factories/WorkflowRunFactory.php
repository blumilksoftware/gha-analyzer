<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\WorkflowRun;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Repository;

class WorkflowRunFactory extends Factory
{
    public function definition(): array
    {
        return [
            "github_id" => $this->faker->unique()->randomNumber(),
            "name" => $this->faker->word(),
            "repository_id" => Repository::factory(),
            "created_at" => $this->faker->iso8601()
        ];
    }
}