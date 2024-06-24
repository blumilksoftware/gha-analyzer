<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            "name" => $this->faker->userName(),
            "email" => $this->faker->unique()->safeEmail(),
            "github_id" => $this->faker->unique()->randomNumber(),
            "github_token" => $this->faker->asciify('********************'),
            "github_refresh_token" => $this->faker->asciify('********************'),
            "password" => $this->faker->password(),
            "remember_token" => $this->faker->asciify('********************')
        ];
    }
}
