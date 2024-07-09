<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Repository;
use Illuminate\Database\Seeder;

class RepositoriesSeeder extends Seeder
{
    public function run(): void
    {
        Repository::factory()->count(5)->create();
    }
}
