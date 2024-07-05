<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkflowJob;

class WorkflowJobsSeeder extends Seeder
{
    public function run(): void
    {
        WorkflowJob::factory()->count(5)->create();
    }
}
