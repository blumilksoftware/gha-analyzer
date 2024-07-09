<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\WorkflowJob;
use Illuminate\Database\Seeder;

class WorkflowJobsSeeder extends Seeder
{
    public function run(): void
    {
        WorkflowJob::factory()->count(5)->create();
    }
}
