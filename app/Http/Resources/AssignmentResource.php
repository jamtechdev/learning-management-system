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
            'student_id' => $this?->student_id,
            // 'student' => new \App\Http\Resources\AuthResource($this->student),
            'subject' => new \App\Http\Resources\SubjectResource($this->subject),
            'questions' => QuestionResource::collection($this->questions),
            'created_time' => $this?->created_at ? Carbon::parse($this->created_at)->diffForHumans() : null,
            'updated_time' => $this?->updated_at ? Carbon::parse($this->updated_at)->diffForHumans() : null,
        ];
    }
}
