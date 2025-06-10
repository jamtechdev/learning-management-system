<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\QuestionLevel;

class RearrangingQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Rearranging questions...');

        foreach (QuestionLevel::with('subjects')->get() as $level) {
            if ($level->subjects->isEmpty()) {
                continue;
            }

            foreach (range(1, 5) as $i) {
                $sentence = fake()->sentence(6); // fixed-length sentence
                $orderedAnswer = preg_split('/\s+/', trim($sentence));
                $shuffled = $orderedAnswer;
                shuffle($shuffled);

                $options = collect($shuffled)->map(fn($word) => [
                    'value' => $word,
                    'is_correct' => false, // correctness is handled via ordering
                ])->values()->toArray();

                $subject = $level->subjects->random();

                $metadata = [
                    'type' => 'rearranging',
                    'content' => $sentence,
                    'instruction' => 'Rearrange the words to form a meaningful sentence.',
                    'options' => $options,
                    'answer' => [
                        'answer' => $orderedAnswer,
                        'format' => 'ordered',
                    ],
                ];

                Question::create([
                    'type' => 'rearranging',
                    'content' => $sentence,
                    'education_type' => $level->education_type,
                    'subject_id' => $subject->id,
                    'level_id' => $level->id,
                    'explanation' => 'Correct sentence structure.',
                    'metadata' => $metadata,
                ]);
            }
        }

        $this->command->info('Rearranging questions seeded successfully!');
    }
}
