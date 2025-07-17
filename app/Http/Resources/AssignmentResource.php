<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $dueDate = $this->due_date ? Carbon::parse($this->due_date)->toDateTimeString() : null;
        $createdTime = $this->created_at ? Carbon::parse($this->created_at)->diffForHumans() : null;
        $updatedTime = $this->updated_at ? Carbon::parse($this->updated_at)->diffForHumans() : null;

        // Count the number of attempted questions
        $attemptedCount = $this->questions->filter(function ($question) {
            return (bool) ($question->pivot->is_attempt ?? false);
        })->count();

        // Check the status based on the number of attempted questions
        $totalQuestions = $this->questions->count();
        $assignmentStatus = 'Started'; // Default status

        if ($attemptedCount === 0) {
            $assignmentStatus = 'Started'; // No questions attempted
        } elseif ($attemptedCount > 0 && $attemptedCount < $totalQuestions) {
            $assignmentStatus = 'In Progress'; // Some questions attempted
        } elseif ($attemptedCount === $totalQuestions) {
            $assignmentStatus = 'Completed'; // All questions attempted
        }

        // Update the assignment status if all questions are completed
        if ($attemptedCount === $totalQuestions) {
            $this->updateStatusToCompleted();
        }

        return [
            'id' => $this->id ?? null,
            'title' => $this->title ?? 'No title provided',
            'description' => $this->description ?? 'No description provided',
            'due_date' => $dueDate,
            'is_recurring' => $this->is_recurring ?? false,
            'recurrence_rule' => $this->recurrence_rule ? json_decode($this->recurrence_rule) : null,
            'student_id' => $this->student_id ?? null,
            'subject' => new \App\Http\Resources\SubjectResource($this->subject ?? []),
            'questions' => $this->questions->map(function ($question) {
                return [
                    'id' => $question->id ?? null,
                    'question' => $question->metadata ?? 'No question available',
                    'is_attempt' => (bool)($question->pivot->is_attempt ?? false), // Access pivot 'is_attempt'
                ];
            }),
            'assignment_status' => $assignmentStatus,
            'created_time' => $createdTime,
            'updated_time' => $updatedTime,
        ];
    }

    /**
     * Method to update the assignment status to 'completed'.
     */
    private function updateStatusToCompleted()
    {
        // Update the status of the assignment in the database to 'completed'
        $this->status = 'completed';
        $this->save();
    }
}
