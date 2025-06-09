<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\QuestionLevel;

class LinkingQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Linking (Matching) questions...');

        foreach (QuestionLevel::with('subjects')->get() as $level) {
            if ($level->subjects->isEmpty()) {
                continue;
            }

            foreach (range(1, 10) as $i) {
                // Generate 4 pairs of matching options
                $pairs = [];
                for ($j = 0; $j < 4; $j++) {
                    $pairs[] = [
                        'left' => [
                            'word' => fake()->word(),
                            'image_uri' => null,
                            'match_type' => 'text',
                        ],
                        'right' => [
                            'word' => fake()->word(),
                            'image_uri' => null,
                            'match_type' => 'text',
                        ],
                    ];
                }

                $questionText = fake()->sentence();
                $subject = $level->subjects->random();

                $metadata = [
                    'type' => 'linking',
                    'content' => $questionText,
                    'instruction' => 'Match the items on the left with those on the right.',
                    'explanation' => 'These are sample linking answers.',
                    'format' => 'mapping',
                    'answer' => $pairs,
                ];

                Question::create([
                    'type' => 'linking',
                    'content' => $questionText,
                    'education_type' => $level->education_type,
                    'subject_id' => $subject->id,
                    'level_id' => $level->id,
                    'explanation' => $metadata['explanation'],
                    'metadata' => $metadata,
                ]);
            }
        }

        $this->command->info('Linking questions seeded successfully!');
    }
}
