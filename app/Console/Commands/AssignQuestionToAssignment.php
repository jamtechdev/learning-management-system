<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AssignmentPendingReminder;
use App\Mail\NewAssignmentCreated;

class AssignQuestionToAssignment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:questions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create recurring assignments every Monday for all parents and their children based on their student level';


    public function handle()
    {
        $parents = User::role('parent')->get();
        if ($parents->isEmpty()) {
            $this->error('No parent users found.');
            return;
        }

        foreach ($parents as $parent) {
            $children = User::where('parent_id', $parent->id)->get();

            if ($children->isEmpty()) {
                $this->info("No children found for parent with ID: {$parent->id}");
                continue;
            }

            foreach ($children as $child) {
                $pendingAssignment = Assignment::where('student_id', $child->id)
                    ->where('status', 'pending')
                    ->first();

                if ($pendingAssignment) {
                    Mail::to($parent->email)->send(new AssignmentPendingReminder($pendingAssignment, $child, $parent));
                    $this->info("Pending assignment reminder sent to parent with ID: {$parent->id} for child ID: {$child->id}");
                } else {
                    $this->createNewAssignment($parent, $child);
                }
            }
        }

        $this->info('Recurring assignments created and questions assigned successfully for all parents and their children!');
    }


    protected function createNewAssignment($parent, $child)
    {

        $levelId = $child->student_level;

        if (!$levelId) {
            $this->error("No valid level found for child with ID: {$child->id}");
            return;
        }

        $level = \App\Models\QuestionLevel::find($levelId);

        if (!$level) {
            $this->error("No level found for level ID: {$levelId}.");
            return;
        }

        $questions = Question::where('level_id', $level->id)->take(10)->get();

        if ($questions->isEmpty()) {
            $this->error("No questions found for level: {$level->name}.");
            return;
        }

        $today = Carbon::now();
        $dueDate = $today->isMonday() ? $today : $today->next(Carbon::MONDAY);

        $assignment = Assignment::create([
            'title' => 'English Paper 1',
            'description' => 'This is an English assignment with multiple questions.',
            'due_date' => $dueDate,
            'is_recurring' => true,
            'recurrence_rule' => json_encode(['frequency' => 'weekly', 'day_of_week' => 'monday']),
            'student_id' => $child->id,
            'created_by' => $parent->id,
        ]);

        $assignment->questions()->attach($questions->pluck('id'));

        Mail::to($parent->email)->send(new NewAssignmentCreated($assignment, $child, $parent));

        $this->info("Recurring assignment created for child with ID: {$child->id} and questions assigned for Monday, {$dueDate->toDateString()}.");
    }
}
