<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "github_id" => $this->github_id,
            "name" => $this->name,
            "avatar_url" => $this->avatar_url,
            "fetched_at" => $this->fetch_at,
            "users" => $this->users->count(),
            "repos" => $this->repositories->count(),
            "runs" => $this->workflowRuns->count(),
            "jobs" => $this->jobCount,
            "actors" => $this->actorCount,
            "minutes" => $this->totalMinutes,
            "price" => $this->totalPrice,
        ];
    }
}
