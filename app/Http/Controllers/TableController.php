<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Organization;
use Inertia\Inertia;
use Inertia\Response;

class TableController extends Controller
{
    public function __construct() {}

    public function show(): Response
    {
        $organizations = Organization::with("repositories.workflowRuns.workflowJobs")->with("repositories.workflowRuns.workflowActor")->get();
        $data = [];

        foreach ($organizations as $org) {
            foreach ($org->repositories as $repo) {
                foreach ($repo->workflowRuns as $run) {
                    foreach ($run->workflowJobs as $job) {
                        $data[] = [
                            "id" => $run->job,
                            "date" => $run->github_created_at,
                            "organization" => $org->name,
                            "repository" => $repo->name,
                            "repository_id" => $repo->id,
                            "minutes" => $job->minutes,
                            "price_per_minute" => $job->price_per_unit,
                            "total_price" => $job->minutes * $job->price_per_unit,
                            "workflow" => $run->name . " - " . $job->name,
                            "os" => $job->runner_os,
                            "actor" => $run->workflowActor,
                        ];
                    }
                }
            }
        }

        return Inertia::render("Table", [
            "data" => $data,
        ]);
    }
}
