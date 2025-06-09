<?php

namespace Database\Seeders;
// TrueFalseQuestionSeeder.php

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\QuestionLevel;

class TrueFalseQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding True/False questions...');

        foreach (QuestionLevel::with('subjects')->get() as $level) {
            if ($level->subjects->isEmpty()) {
                continue;
            }

            foreach (range(1, 10) as $i) {
                $answer = fake()->boolean() ? 'True' : 'False';
                $questionText = fake()->sentence();

                $metadata = [
                    'type' => 'true_false',
                    'content' => $questionText,
                    'instruction' => 'Select whether the statement is true or false.',
                    'options' => [
                        ['value' => 'True'],
                        ['value' => 'False'],
                    ],
                    'answer' => [
                        'choice' => $answer,
                        'explanation' => 'This is a sample explanation.',
                        'format' => 'text',
                    ],
                ];

                $subject = $level->subjects->random();

                Question::create([
                    'type' => 'true_false',
                    'content' => $questionText,
                    'education_type' => $level->education_type,
                    'subject_id' => $subject->id,
                    'level_id' => $level->id,
                    'explanation' => $metadata['answer']['explanation'],
                    'metadata' => $metadata,
                ]);
            }
        }

        $this->command->info('True/False questions seeded successfully!');
    }
}
