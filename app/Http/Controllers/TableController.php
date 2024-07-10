<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Services\ColorsService;
use Inertia\Inertia;

class TableController extends Controller
{
    public function __construct(
        protected ColorsService $colorsService,
    ) {}

    public function show($organizationId = 1)
    {
        $organization = Organization::query()->where("id", (int)$organizationId)->first();
        $repositories = $organization->repositories()->get();
        $repositoriesArray = $organization->repositories()->get()->toArray();

        $runs = [];
        $runsArray = [];

        foreach ($repositories as $repository) {
            array_push($runs, $repository->workflowRuns()->get());
            array_push($runsArray, $repository->workflowRuns()->get()->toArray());
        }

        $jobs = [];
        $jobsArray = [];

        foreach ($runs as $runsCollection) {
            foreach ($runsCollection as $run) {
                array_push($jobs, $run->workflowJobs()->get());
                array_push($jobsArray, $run->workflowJobs()->get()->toArray());
            }
        }

        return Inertia::render("Table", [
            "colors" => $this->colorsService->getColors(),
            "repositories" => $repositoriesArray,
            "runs" => $runsArray,
            "jobs" => $jobsArray,
        ]);
    }
}
