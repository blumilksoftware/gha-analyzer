<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\AuthorResource;
use App\Models\WorkflowActor;
use Inertia\Inertia;
use Inertia\Response;

class AuthorsController extends Controller
{
    public function __construct() {}

    public function show(): Response
    {
        $actors = WorkflowActor::with("workflowJobs")->get();
        $data = AuthorResource::collection($actors);

        return Inertia::render("Authors", ["data" => $data]);
    }
}
