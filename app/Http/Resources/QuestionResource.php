<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'type'               => $this->type,
            'content'            => $this->content,
            'explanation'        => $this->explanation,
            'metadata'           => $this->metadata,
            'parent_question_id' => $this->parent_question_id,
            'created_at'         => $this->created_at?->toDateTimeString(),
            'updated_at'         => $this->updated_at?->toDateTimeString(),
        ];
    }
}
