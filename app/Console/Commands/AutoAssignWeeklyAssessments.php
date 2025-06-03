<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Assessment;
use App\Models\Question;
use Carbon\Carbon;

class AssignWeeklyAssessments extends Command
{
    protected $signature = 'weekly:assign';
    protected $description = 'Assign a new weekly assessment to all subscribed students';

    public function handle()
    {
        $today = Carbon::today();

        $students = User::where('role', 'student')->get(); // adjust if needed

        foreach ($students as $student) {
            $subjects = $student->subscriptionPlan?->subjects ?? [];

            foreach ($subjects as $subject) {
                // Create new assessment
                $assessment = Assessment::create([
                    'student_id' => $student->id,
                    'question_subject_id' => $subject->id,
                    'status' => 'not_started',
                    'assigned_date' => $today,
                ]);

                // Pick N random questions
                $questions = Question::where('question_subject_id', $subject->id)->inRandomOrder()->limit(5)->get();

                foreach ($questions as $question) {
                    $assessment->questions()->create([
                        'question_id' => $question->id,
                        'status' => 'not_started',
                    ]);
                }

                $this->info("Assessment assigned to student {$student->id} for subject {$subject->name}");
            }
        }

        $this->info('Weekly assessments assigned!');
    }
}
