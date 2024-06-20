<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        return [
            "name" => fake()->name(),
            "github_id" => rand(),
            "avatar_url" => Str::random(10),
        ];
    }
}
