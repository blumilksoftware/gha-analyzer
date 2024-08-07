<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\RepositoryResource;
use App\Models\Repository;
use Inertia\Inertia;
use Inertia\Response;

class RepositoriesController extends Controller
{
    public function __construct() {}

    public function show(): Response
    {
        $repositories = Repository::with(["workflowJobs", "organization"])->get();
        $data = RepositoryResource::collection($repositories);

        return Inertia::render("Repositories", ["data" => $data]);
    }
}
