<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Create some Question Groups (for Comprehension)
        $groupIds = [];
        for ($i = 0; $i < 3; $i++) {
            $groupIds[] = DB::table('question_groups')->insertGetId([
                'title' => 'Passage ' . ($i + 1),
                'passage' => $faker->text(500),
                'metadata' => json_encode(['source' => 'textbook', 'difficulty' => 'medium']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create some Questions
        $questionIds = [];
        for ($i = 0; $i < 10; $i++) {
            $type = collect([
                'mcq',
                'fill_blank',
                'spelling',
                'rearrange',
                'linking',
                'true_false',
                'image_mcq',
                'math',
                'grouped',
                'comprehension'
            ])->random();
            $questionIds[] = DB::table('questions')->insertGetId([
                'title' => $faker->sentence(),
                'type' => $type,
                'content' => $faker->paragraph(),
                'explanation' => $faker->text(200),
                'metadata' => json_encode(['difficulty' => 'medium']),
                'group_id' => $type === 'comprehension' ? $groupIds[array_rand($groupIds)] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create Question Options for each Question (Only for MCQs)
        foreach ($questionIds as $questionId) {
            if (DB::table('questions')->where('id', $questionId)->value('type') === 'mcq') {
                DB::table('question_options')->insert([
                    [
                        'question_id' => $questionId,
                        'label' => 'A',
                        'value' => $faker->word(),
                        'is_correct' => rand(0, 1),

                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'question_id' => $questionId,
                        'label' => 'B',
                        'value' => $faker->word(),
                        'is_correct' => rand(0, 1),

                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'question_id' => $questionId,
                        'label' => 'C',
                        'value' => $faker->word(),
                        'is_correct' => rand(0, 1),

                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }
        }

        // Create Question Answers
        foreach ($questionIds as $questionId) {
            DB::table('question_answers')->insert([
                'question_id' => $questionId,
                'answer' => json_encode(['answer' => $faker->sentence()]),
                'format' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tagging Questions
        foreach ($questionIds as $questionId) {
            DB::table('question_tags')->insert([
                [
                    'question_id' => $questionId,
                    'tag_type' => 'subject',
                    'value' => 'Math',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'question_id' => $questionId,
                    'tag_type' => 'level',
                    'value' => 'Intermediate',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // Difficulty Levels for Questions
        foreach ($questionIds as $questionId) {
            DB::table('question_difficulties')->insert([
                'question_id' => $questionId,
                'difficulty' => collect(['easy', 'medium', 'hard'])->random(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
