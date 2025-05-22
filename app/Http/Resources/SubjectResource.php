<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this?->id,
            'education_type' => $this?->education_type,
            'subject_name' => $this?->name,
            'level_id' => $this?->level?->id ?? null,
            'level_name' => $this?->level?->name ?? null,
        ];
    }
}
