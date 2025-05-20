<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Step 1: Seed Levels
        $levelIds = [];
        foreach (['primary', 'secondary'] as $eduType) {
            for ($i = 1; $i <= 2; $i++) {
                $levelIds[] = DB::table('question_levels')->insertGetId([
                    'education_type' => $eduType,
                    'name' => ucfirst($eduType) . " Level $i",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Step 2: Seed Subjects
        $subjectIds = [];
        foreach ($levelIds as $levelId) {
            foreach (['Math', 'English', 'Science'] as $subjectName) {
                $subjectIds[] = DB::table('question_subjects')->insertGetId([
                    'level_id' => $levelId,
                    'name' => $subjectName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Step 3: Create some Question Groups (for Comprehension)
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

        // Step 4: Create Questions
        $questionIds = [];
        for ($i = 0; $i < 10; $i++) {
            $type = collect([
                'mcq', 'fill_blank', 'spelling', 'rearrange', 'linking',
                'true_false', 'image_mcq', 'math', 'grouped', 'comprehension'
            ])->random();

            $levelId = $levelIds[array_rand($levelIds)];
            $subjectId = collect($subjectIds)->random(); // Optional: Filter subject for that level if needed

            $questionIds[] = DB::table('questions')->insertGetId([
                'type' => $type,
                'content' => $faker->paragraph(),
                'explanation' => $faker->text(200),
                'metadata' => json_encode(['difficulty' => 'medium']),
                'group_id' => $type === 'comprehension' ? $groupIds[array_rand($groupIds)] : null,
                'level_id' => $levelId,
                'subject_id' => $subjectId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Step 5: Create Options for MCQs
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

        // Step 6: Answers
        foreach ($questionIds as $questionId) {
            DB::table('question_answers')->insert([
                'question_id' => $questionId,
                'answer' => json_encode(['answer' => $faker->sentence()]),
                'format' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Step 7: Tags
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

        // Step 8: Difficulties
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
