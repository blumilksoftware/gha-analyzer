<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Repository;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowRunFactory extends Factory
{
    public function definition(): array
    {
        return [
            "github_id" => $this->faker->unique()->randomNumber(),
            "name" => $this->faker->word(),
            "repository_id" => Repository::factory(),
            "github_created_at" => $this->faker->iso8601(),
        ];
    }
}
