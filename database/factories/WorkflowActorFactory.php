<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowActorFactory extends Factory
{
    public function definition(): array
    {
        return [
            "github_id" => $this->faker->unique()->randomNumber(),
            "name" => $this->faker->word(),
            "avatar_url" => $this->faker->url(),
        ];
    }
}
