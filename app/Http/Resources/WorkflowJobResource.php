<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowJobResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->workflowRun->id,
            "date" => $this->workflowRun->github_created_at,
            "organization" => $this->workflowRun->repository->organization->name,
            "repository" => $this->workflowRun->repository->name,
            "repository_id" => $this->workflowRun->repository->id,
            "minutes" => $this->minutes,
            "price_per_minute" => $this->price_per_unit,
            "total_price" => $this->price,
            "workflow" => $this->fullName,
            "os" => $this->runner_os,
            "actor" => $this->workflowRun->workflowActor,
        ];
    }
}
