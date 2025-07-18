<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Question;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewAssignmentCreated;
use App\Models\QuestionSubject;
use Illuminate\Support\Facades\Log;

class AssignQuestionToAssignment extends Command
{
    protected $signature = 'assign:questions';
    protected $description = 'Create recurring assignments every Monday for all children based on their student level and subjects';

    public function handle()
    {
        Log::info('Assigning questions to assignments command started.');

        $parents = User::role('parent')->get();

        if ($parents->isEmpty()) {
             Log::info('No parent users found.');
            Log::error('No parent users found.');
            return;
        }

        foreach ($parents as $parent) {
            $children = User::where('parent_id', $parent->id)->get();

            if ($children->isEmpty()) {
                $this->info("No children found for parent with ID: {$parent->id}");
                continue;
            }

            foreach ($children as $child) {
                $this->createAssignmentsForChild($parent, $child);
            }
        }

        Log::info('Assignments successfully created for all parents and their children!');
        $this->info('Assignments successfully created for all parents and their children!');
    }

    protected function createAssignmentsForChild($parent, $child)
    {
        $levelId = $child->student_level;

        if (!$levelId) {
             Log::info("No valid level found for child with ID: {$child->id}");
            return;
        }

        $subjects = QuestionSubject::where('level_id', $levelId)->get();

        if ($subjects->isEmpty()) {
             Log::info("No subjects found for child with ID: {$child->id}. Level ID: {$levelId}");
            return;
        }

        foreach ($subjects as $subject) {
            $this->createAssignmentForSubject($parent, $child, $subject);
        }
    }

    protected function createAssignmentForSubject($parent, $child, $subject)
    {
        $questions = Question::where(['level_id' => $child->student_level, 'subject_id' => $subject->id])->pluck('id');

        if ($questions->isEmpty()) {
             Log::info("No questions found for child with ID: {$child->id} for subject: {$subject->name}");
            return;
        }

        $today = Carbon::now();
        $dueDate = $today->next(Carbon::MONDAY);

        // Check if assignment already exists for this child, subject, and due date
        $existingAssignment = Assignment::where('student_id', $child->id)
            ->where('subject_id', $subject->id)
            ->whereDate('due_date', $dueDate->toDateString())
            ->first();

        if ($existingAssignment) {
            Log::info("Assignment already exists for child ID: {$child->id}, subject: {$subject->name}, due date: {$dueDate->toDateString()}");
            return;
        }

        $assignment = Assignment::create([
            'title' => "{$subject->name} Paper",
            'description' => "This is a {$subject->name} assignment with multiple questions.",
            'is_recurring' => true,
            'recurrence_type' => 'every_monday',
            'student_id' => $child->id,
            'created_by' => $parent->id,
            'status' => 'pending',
            'due_date' => $dueDate,
            'subject_id' => $subject->id,
        ]);

        $assignment->questions()->attach($questions);

        try {
            Mail::to($parent->email)->send(new NewAssignmentCreated($assignment, $child, $parent));
        } catch (\Exception $e) {
             Log::info("Failed to send email to parent with ID: {$parent->id} for child with ID: {$child->id}. Error: " . $e->getMessage());
        }
    }
}
