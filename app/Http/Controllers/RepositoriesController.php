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
            $price = 0;
            $minutes = 0;

            foreach ($repo->workflowJobs as $job) {
                $minutes += $job->minutes;
                $price += $job->minutes * $job->price_per_unit;
            }

            $data[] = [
                "id" => $repo->id,
                "name" => $repo->name,
                "organization" => $repo->organization->name,
                "avatar_url" => $repo->organization->avatar_url,
                "minutes" => $minutes,
                "price" => $price,
            ];
        }

        return Inertia::render("Repositories", ["data" => $data]);
    }
}
