<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(RepositoriesSeeder::class);
        $this->call(WorkflowRunsSeeder::class);
        $this->call(OrganizationsSeeder::class);
    }
}
