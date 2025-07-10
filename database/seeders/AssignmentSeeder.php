<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AssignmentSeeder extends Seeder
{
    public function run()
    {
        $parent = User::role('parent')->first();
        if (!$parent) {
            $this->command->error('No parent user found in the database!');
            return;
        }

        $child = User::where('parent_id', $parent->id)->first(); // Fetch child (student) based on parent_id

        if (!$child) {
            $this->command->error('No child found for the selected parent!');
            return;
        }

        $questions = Question::take(5)->get();

        if ($questions->isEmpty()) {
            $this->command->error('No questions found in the database!');
            return;
        }

        $assignment = Assignment::create([
            'title' => 'English Paper 1',
            'description' => 'This is an English assignment with multiple questions.',
            'due_date' => Carbon::now()->next(Carbon::MONDAY),
            'is_recurring' => true, // Set to true for recurring assignments, set false for ad-hoc
            'recurrence_rule' => json_encode(['frequency' => 'weekly', 'day_of_week' => 'monday']),
            'student_id' => $child->id, // Link to the child (student)
            'created_by' => $parent->id, // Parent who created the assignment
        ]);

        // Attach all the questions to this single assignment
        $assignment->questions()->attach($questions->pluck('id'));

        // Output success message for seeding
        $this->command->info('Single assignment with multiple questions seeded successfully!');
    }
}
