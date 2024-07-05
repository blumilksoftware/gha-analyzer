<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkflowRun;

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
