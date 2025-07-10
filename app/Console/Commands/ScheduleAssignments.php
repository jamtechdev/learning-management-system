<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Question;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ScheduleAssignments extends Command
{
    protected $signature = 'assignments:schedule';
    protected $description = 'Schedule recurring assignments and add ad-hoc assignments';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $assignments = Assignment::all();

        foreach ($assignments as $assignment) {
            // Check if the assignment is recurring
            if ($assignment->is_recurring) {
                $this->scheduleRecurringAssignment($assignment);
            } else {
                $this->assignQuestionsToAssignment($assignment);
            }
        }

        $this->info('Assignments have been scheduled and questions assigned!');
    }

    // Method to assign questions to an assignment
    protected function assignQuestionsToAssignment(Assignment $assignment)
    {
        // Example: Fetch questions based on level and subject
        $questions = Question::where('level_id', $assignment->level_id)
            ->where('subject_id', $assignment->subject_id)
            ->inRandomOrder()
            ->take(5) // Assign 5 random questions
            ->get();

        // Assign questions to the current assignment
        $assignment->questions()->sync($questions->pluck('id'));
        $this->info("Questions assigned to assignment: {$assignment->title}");
    }

    // Method to handle recurring assignments (could be adding them weekly/monthly)
    protected function scheduleRecurringAssignment(Assignment $assignment)
    {
        // Example: Logic to handle recurrence rule (e.g., weekly)
        $recurrenceRule = $assignment->recurrence_rule;

        if ($recurrenceRule['frequency'] === 'weekly') {
            $nextDueDate = Carbon::parse($assignment->due_date)->addWeek();
            $assignment->update(['due_date' => $nextDueDate]);
        }

        $this->info("Recurring assignment scheduled: {$assignment->title}");
    }
}
