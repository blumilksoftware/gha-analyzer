<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "github_id" => $this->github_id,
            "avatar_url" => $this->avatar_url,
            "minutes" => $this->totalMinutes,
            "price" => $this->totalPrice,
        ];
    }
}
