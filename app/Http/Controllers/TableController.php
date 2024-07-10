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
        foreach ($repositories as $repository) {
            $repositoryArray = $repository->toArray();
            $repositoryArray['workflowRuns'] = [];
        
            $workflowRuns = $repository->workflowRuns()->get();
        
            foreach ($workflowRuns as $run) {
                $runArray = $run->toArray();
                $runArray['workflowJobs'] = [];
        
                $workflowJobs = $run->workflowJobs()->get();
        
                foreach ($workflowJobs as $job) {
                    $runArray['workflowJobs'][] = $job->toArray();
                }
        
                $repositoryArray['workflowRuns'][] = $runArray;
            }
        
            $repositoriesArray[] = $repositoryArray;
        }
        
        return Inertia::render("Table", [
            "colors" => $this->colorsService->getColors(),
            "repositories" => $repositoriesArray,
        ]);
    }
}
