<?php

namespace Database\Seeders;

use App\Models\QuestionSubject;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Subject;
use App\Models\QuestionLevel;
use App\Enum\QuestionTypes;

class GrammarClozeWithOptionsQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all subjects
        $subjects = QuestionSubject::all();

        foreach ($subjects as $subject) {
            for ($i = 1; $i <= 10; $i++) {
                $paragraph = "The quick brown fox ($i) ____ over the lazy dog. It ($i+1) ____ a sunny day.";
                $sharedOptions = ['jumps', 'jumped', 'was', 'is'];

                $metadata = [
                    'instruction' => 'Fill in the blanks with the correct words.',
                    'paragraph' => $paragraph,
                    'explanation' => 'This tests your understanding of verb tenses and word order.',
                    'question_type' => 'grammar_cloze',
                    'question_group' => [
                        'shared_options' => $sharedOptions,
                    ],
                    'questions' => [
                        [
                            'id' => 1,
                            'blank_number' => $i,
                            'correct_answer' => 'jumps',
                            'input_type' => 'input'
                        ],
                        [
                            'id' => 2,
                            'blank_number' => $i + 1,
                            'correct_answer' => 'was',
                            'input_type' => 'input'
                        ]
                    ]
                ];

                Question::create([
                    'type' => QuestionTypes::GRAMMAR_CLOZE_WITH_OPTIONS,
                    'content' => $paragraph,
                    'education_type' => 'primary', // or fetch dynamically
                    'level_id' => $subject->level_id,
                    'subject_id' => $subject->id,
                    'explanation' => $metadata['explanation'],
                    'metadata' => $metadata,
                ]);
            }
        }
    }
}
