<?php

namespace Database\Seeders;

use App\Models\QuestionSubject;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Subject;
use App\Models\QuestionLevel;
use App\Enum\QuestionTypes;

class UnderlineCorrectQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $this->command->info('Creating underline correct questions...');

        $subjects = QuestionSubject::all();

        foreach ($subjects as $subject) {
            for ($i = 1; $i <= 5; $i++) {
                $level = $subject->level;
                $educationType = $subject->education_type;
                $paragraph = "Select the correct option in each blank. This is a sample paragraph for question $i in subject '{$subject->name}'.";
                $questions = [];
                $blankCount = rand(2, 3);
                for ($b = 1; $b <= $blankCount; $b++) {
                    $options = ['option1', 'option2', 'option3'];
                    $correct = $options[array_rand($options)];

                    $questions[] = [
                        'id' => $b,
                        'blank_number' => $b,
                        'options' => $options,
                        'correct_answer' => $correct,
                        'input_type' => 'radio'
                    ];
                }

                $metadata = [
                    'instruction' => 'Underline the correct option in each blank.',
                    'education_type' => $educationType,
                    'level_id' => $level->id,
                    'subject_id' => $subject->id,
                    'type' => QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS, // make sure this constant exists
                    'paragraph' => $paragraph,
                    'questions' => $questions
                ];

                Question::create([
                    'type' => QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS,
                    'education_type' => $educationType,
                    'level_id' => $level->id,
                    'subject_id' => $subject->id,
                    'content' => $paragraph,
                    'explanation' => 'Underline the correct answers in context.',
                    'metadata' => $metadata
                ]);
            }
        }

        $this->command->info('Underline correct questions seeded successfully.');
    }
}
