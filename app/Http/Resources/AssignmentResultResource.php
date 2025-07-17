<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResultResource extends JsonResource
{
    public function toArray($request)
    {
        // Safely access assignment properties using optional() to avoid null errors
        return [
            'assignment_id' => optional($this->assignment)->id,
            'assignment_name' => optional($this->assignment)->title,
            'assignment_description' => optional($this->assignment)->description,
            'assignment_due_date' => optional($this->assignment)->due_date,
            'assignment_status' => optional($this->assignment)->status,
            'assignment_subject' => optional($this->assignment->subject)->name, // Safely access the subject name

            'user_id' => $this->user_id,
            'score' => $this->score,
            'gems' => $this->gems,
            'status' => $this->status,
            'submitted_at' => optional($this->submitted_at)->toDateTimeString(), // Safely access submitted_at

            'answers' => $this->answers,
            'progress' => $this->calculateProgress(),
            'feedback' => $this->getFeedback(),

            'is_attempt' => $this->assignment->questions->map(function ($question) {
                return [
                    'question_id' => $question->id,
                    'question_name' => $question->metadata,
                    'is_attempt' => $question->pivot->is_attempt,
                ];
            }),
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
