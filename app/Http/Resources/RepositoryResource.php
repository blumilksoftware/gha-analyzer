<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepositoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "organization" => $this->organization->name,
            "avatar_url" => $this->organization->avatar_url,
            "minutes" => $this->totalMinutes,
            "price" => $this->totalPrice,
        ];
    }
}
