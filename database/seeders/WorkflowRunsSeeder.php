<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\WorkflowRun;
use Illuminate\Database\Seeder;

class WorkflowRunsSeeder extends Seeder
{
    public function run(): void
    {
        WorkflowRun::factory()
            ->hasJobs(5)
            ->count(10)
            ->create();
    }
}
