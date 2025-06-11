<?php

namespace Database\Seeders;

use App\Models\QuestionSubject;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Subject;
use App\Models\QuestionLevel;
use App\Enum\QuestionTypes;

class ComprehensionQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = QuestionSubject::all();
        $this->command->info('Creating comprehension questions...');
        foreach ($subjects as $subject) {
            for ($i = 1; $i <= 5; $i++) {
                $level = $subject->level;
                $educationType = 'primary'; // or assign based on level/subject

                $passage = "This is a sample comprehension passage #$i for subject '{$subject->name}'. It talks about various interesting facts to read and understand.";

                // Create 2–3 subquestions
                $subquestions = [];
                for ($j = 1; $j <= rand(2, 3); $j++) {
                    $subquestions[] = [
                        "question" => "What is the main idea in sentence $j?",
                        "answer" => "Sample answer $j"
                    ];
                }

                $metadata = [
                    'education_type' => $educationType,
                    'level_id' => $level->id,
                    'subject_id' => $subject->id,
                    'type' => QuestionTypes::COMPREHENSION, // ensure enum is correct
                    'instruction' => 'Read the passage carefully and answer the questions.',
                    'passage' => $passage,
                    'subquestions' => $subquestions
                ];

                Question::create([
                    'type' => QuestionTypes::COMPREHENSION,
                    'education_type' => $educationType,
                    'level_id' => $level->id,
                    'subject_id' => $subject->id,
                    'content' => $passage,
                    'explanation' => 'Comprehension test question with multiple subquestions.',
                    'metadata' => $metadata
                ]);
            }
        }

        $this->command->info('Comprehension questions created successfully.');
    }
}
