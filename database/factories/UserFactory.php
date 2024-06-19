<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            "name" => fake()->name(),
            "email" => fake()->unique()->safeEmail(),
            "github_id" => rand(),
            "github_token" => Str::random(10),
            "github_refresh_token" => Str::random(10),
            "password" => Hash::make("password"),
            "remember_token" => Str::random(10),
        ];
    }
}
