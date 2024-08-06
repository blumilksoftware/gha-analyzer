<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Repository;
use Inertia\Inertia;
use Inertia\Response;

class RepositoriesController extends Controller
{
    public function __construct() {}

    public function show(): Response
    {
        $repositories = Repository::with("workflowJobs")->with("organization")->get();
        $data = [];

        foreach ($repositories as $repo) {
            $data[] = [
                "id" => $repo->id,
                "name" => $repo->name,
                "organization" => $repo->organization->name,
                "avatar_url" => $repo->organization->avatar_url,
                "minutes" => $repo->totalMinutes,
                "price" => $repo->totalPrice,
            ];
        }

        return Inertia::render("Repositories", ["data" => $data]);
    }
}
