<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        return [
            "name" => $this->faker->unique()->word(),
            "github_id" => $this->faker->unique()->randomNumber(),
            "avatar_url" => $this->faker->url(),
        ];
    }
}
