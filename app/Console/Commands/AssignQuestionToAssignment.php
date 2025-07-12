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
    protected $description = 'Create recurring English Paper assignments every Monday for all children based on their student level';

    public function handle()
    {
        // Log the start of the command execution
        Log::info('Assigning questions to assignments command started.');

        $parents = User::role('parent')->get();
        if ($parents->isEmpty()) {
            $this->error('No parent users found.');
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
                $this->createEnglishAssignmentForMonday($parent, $child);
            }
        }

        // Log the successful completion of the command
        Log::info('Recurring English Paper assignments successfully created for all parents and their children!');
        $this->info('Recurring English Paper assignments successfully created for all parents and their children!');
    }

    protected function createEnglishAssignmentForMonday($parent, $child)
    {
        $levelId = $child->student_level;

        if (!$levelId) {
            $this->error("No valid level found for child with ID: {$child->id}");
            return;
        }

        $englishSubjectId = QuestionSubject::where('name', 'English')->value('id');
        if (!$englishSubjectId) {
            $this->error("No English subject found in the database.");
            return;
        }

        $questions = Question::where(['level_id' => $levelId, 'subject_id' => $englishSubjectId])->pluck('id');

        if ($questions->isEmpty()) {
            $this->error("No English questions found for child with ID: {$child->id}.");
            return;
        }

        $today = Carbon::now();
        $dueDate = $today->next(Carbon::MONDAY);

        $assignment = Assignment::create([
            'title' => 'English Paper 1',
            'description' => 'This is an English assignment with multiple questions.',
            'is_recurring' => true,
            'recurrence_type' => 'every_monday',
            'student_id' => $child->id,
            'created_by' => $parent->id,
            'status' => 'pending',
            'due_date' => $dueDate,
            'subject_id' => $englishSubjectId,
        ]);

        $assignment->questions()->attach($questions);

        // Send the email notification
        try {
            Mail::to($parent->email)->send(new NewAssignmentCreated($assignment, $child, $parent));
        } catch (\Exception $e) {
            $this->error("Failed to send email to parent with ID: {$parent->id} for child with ID: {$child->id}. Error: " . $e->getMessage());
        }
    }
}
