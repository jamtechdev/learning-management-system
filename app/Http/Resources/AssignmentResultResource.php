<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResultResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'assignment_id' => $this->assignment_id,
            'user_id' => $this->user_id,
            'score' => $this->score,
            'gems' => $this->gems,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at->toDateTimeString(),
            'answers' => $this->answers,
            'progress' => $this->calculateProgress(),
            'feedback' => $this->getFeedback(),
        ];
    }

    private function calculateProgress()
    {
        $totalQuestions = count($this->answers);
        $correctAnswers = collect($this->answers)->where('is_correct', true)->count();
        return ($correctAnswers / $totalQuestions) * 100;
    }

    private function getFeedback()
    {
        if ($this->score >= 80) {
            return 'Excellent work!';
        } elseif ($this->score >= 50) {
            return 'Good effort, but there\'s room for improvement.';
        } else {
            return 'You might need to revisit some concepts.';
        }
    }
}
