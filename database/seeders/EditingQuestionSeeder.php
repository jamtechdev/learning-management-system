<?php

namespace Database\Seeders;

use App\Models\QuestionSubject;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Subject;
use App\Models\QuestionLevel;
use App\Enum\QuestionTypes;

class EditingQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = QuestionSubject::all();

        foreach ($subjects as $subject) {
            for ($i = 1; $i <= 10; $i++) {
                $level = $subject->level;
                $educationType = 'primary'; // Customize as needed

                $paragraph = "This is an example paragraf with several erors to be corrected in subject '{$subject->name}'.";

                // Create 2–3 editing mistakes per question
                $questions = [];
                $mistakes = [
                    ['wrong' => 'paragraf', 'correct' => 'paragraph'],
                    ['wrong' => 'erors', 'correct' => 'errors'],
                    ['wrong' => 'be', 'correct' => 'to be']
                ];

                $usedBoxes = [];

                foreach (array_slice($mistakes, 0, rand(2, 3)) as $index => $error) {
                    $box = $index + 1;
                    $usedBoxes[] = $box;

                    $questions[] = [
                        'box' => $box,
                        'wrong' => $error['wrong'],
                        'correct' => $error['correct']
                    ];
                }

                $metadata = [
                    'type' => QuestionTypes::EDITING, // Ensure this exists in your enum
                    'paragraph' => $paragraph,
                    'questions' => $questions
                ];

                Question::create([
                    'type' => QuestionTypes::EDITING, // Ensure this exists in your enum
                    'education_type' => $educationType,
                    'level_id' => $level->id,
                    'subject_id' => $subject->id,
                    'content' => $paragraph,
                    'explanation' => 'Identify and correct the mistakes in the passage.',
                    'metadata' => $metadata
                ]);
            }
        }
    }
}
