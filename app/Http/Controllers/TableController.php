<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\WorkflowJob;
use Inertia\Inertia;
use Inertia\Response;

class TableController extends Controller
{
    public function __construct() {}

    public function show(): Response
    {
        $jobs = WorkflowJob::with("workflowRun.repository.organization")->with("workflowRun.workflowActor")->get();
        $data = [];

        foreach ($jobs as $job) {
            $data[] = [
                "id" => $job->workflowRun->id,
                "date" => $job->workflowRun->github_created_at,
                "organization" => $job->workflowRun->repository->organization->name,
                "repository" => $job->workflowRun->repository->name,
                "repository_id" => $job->workflowRun->repository->id,
                "minutes" => $job->minutes,
                "price_per_minute" => $job->price_per_unit,
                "total_price" => $job->price,
                "workflow" => $job->fullName,
                "os" => $job->runner_os,
                "actor" => $job->workflowRun->workflowActor,
            ];
        }

        return Inertia::render("Table", [
            "data" => $data,
        ]);
    }
}
