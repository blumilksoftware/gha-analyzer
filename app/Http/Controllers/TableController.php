<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\WorkflowJobResource;
use App\Models\WorkflowJob;
use Inertia\Inertia;
use Inertia\Response;

class TableController extends Controller
{
    public function __construct() {}

    public function show(): Response
    {
        $jobs = WorkflowJob::with(["workflowRun.repository.organization", "workflowRun.workflowActor"])->get();
        $data = WorkflowJobResource::collection($jobs);

        return Inertia::render("Table", ["data" => $data]);
    }
}
