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
        $actors = WorkflowActor::with("workflowJobs")->get();
        $data = [];

        foreach ($actors as $actor) {
            $data[] = [
                "id" => $actor->id,
                "name" => $actor->name,
                "github_id" => $actor->github_id,
                "avatar_url" => $actor->avatar_url,
                "minutes" => $actor->totalMinutes,
                "price" => $actor->totalPrice,
            ];
        }

        return Inertia::render("Authors", [
            "data" => $data,
        ]);
    }
}
