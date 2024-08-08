<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\OrganizationResource;
use App\Jobs\FetchRepositoriesJob;
use App\Models\Organization;
use App\Services\AssignUserToOrganizationsService;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class OrganizationController extends Controller
{
    public function __construct(
        protected AssignUserToOrganizationsService $assignUserService,
    ) {}

    public function show(Request $request)
    {
        $user = $request->user();
        $organizations = $user->organizations()->with(["repositories.workflowRuns.workflowJobs", "repositories.workflowRuns.workflowActor", "users"])->get();
        $data = OrganizationResource::collection($organizations);
        $status = [];

        foreach ($data as $organization) {
            $status[$organization->id] = $this->getProgress($organization->id);
        }

        return Inertia::render("Organization", ["data" => $data, "progress" => $status]);
    }

    public function fetchData(int $organizationId, Request $request): JsonResponse
    {
        $organization = Organization::query()->findOrFail($organizationId);
        $batch = $this->findBatch($organizationId);

        if ($this->isFetching($batch)) {
            return response()->json(["message" => "please wait"], Response::HTTP_CONFLICT);
        }

        $user = $request->user();
        $jobs = [new FetchRepositoriesJob($organizationId, $user->id)];

        $organization->fetch_at = Carbon::now();
        $organization->save();

        $batch = Bus::batch($jobs)->finally(fn(): bool => Cache::delete("fetch/" . $organizationId))->dispatch();
        Cache::set("fetch/" . $organizationId, $batch->id);

        return response()->json(["message" => "fetching started"], Response::HTTP_OK);
    }

    protected function isFetching(?Batch $batch): bool
    {
        return $batch !== null && !$batch->finished();
    }

    protected function getProgress(int $organizationId): ?array
    {
        $batch = $this->findBatch($organizationId);

        if ($batch !== null) {
            return [
                "all" => $batch->totalJobs,
                "done" => $batch->processedJobs(),
                "finished" => $batch->finished(),
            ];
        }

        return null;
    }

    protected function findBatch(int $organizationId): ?Batch
    {
        $batchId = Cache::get("fetch/" . $organizationId);

        if ($batchId === null) {
            return null;
        }

        return Bus::findBatch($batchId);
    }
}
