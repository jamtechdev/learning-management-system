<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AssignmentResource extends JsonResource
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
            'title' => $this?->title,
            'description' => $this?->description,
            'due_date' => Carbon::parse($this->due_date)->toDateTimeString(),
            'is_recurring' => $this?->is_recurring,
            'recurrence_rule' => $this?->recurrence_rule
                ? json_decode($this->recurrence_rule)
                : null,
            'user_id' => $this?->user_id,
            'created_at' => $this?->created_at?->toDateTimeString(),
            'updated_at' => $this?->updated_at?->toDateTimeString(),
        ];
    }
}
