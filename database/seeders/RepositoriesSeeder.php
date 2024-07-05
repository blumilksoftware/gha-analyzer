<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Repository;

class RepositoriesSeeder extends Seeder
{
    public function run(): void
    {
        Repository::factory()->count(5)->create();
    }
}
