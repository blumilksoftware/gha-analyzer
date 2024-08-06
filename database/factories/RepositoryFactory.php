<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepositoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            "github_id" => $this->faker->unique()->randomNumber(),
            "name" => $this->faker->word(),
            "organization_id" => Organization::factory(),
            "is_private" => $this->faker->boolean(),
        ];
    }
}
