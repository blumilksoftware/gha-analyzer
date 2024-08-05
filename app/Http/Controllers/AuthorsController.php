<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\WorkflowActor;
use Inertia\Inertia;
use Inertia\Response;

class AuthorsController extends Controller
{
    public function __construct() {}

    public function show(): Response
    {
        $actors = WorkflowActor::with("workflowRuns.workflowJobs")->get();
        $data = [];

        foreach ($actors as $actor) {
            $price = 0;
            $minutes = 0;

            foreach ($actor->workflowRuns as $run) {
                foreach ($run->workflowJobs as $job) {
                    $minutes += $job->minutes;
                    $price += $job->minutes * $job->price_per_unit;
                }
            }

            $data[] = [
                "id" => $actor->id,
                "name" => $actor->name,
                "github_id" => $actor->github_id,
                "avatar_url" => $actor->avatar_url,
                "minutes" => $minutes,
                "price" => $price,
            ];
        }

        return Inertia::render("Authors", [
            "data" => $data,
        ]);
    }
}
