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

        // Step 1: Seed Levels (Primary 1–5, Secondary 6–12)
        $levelIds = [];
        $levelMap = [];

        foreach (['primary' => range(1, 5), 'secondary' => range(6, 12)] as $eduType => $levels) {
            foreach ($levels as $levelNumber) {
                $levelName = ucfirst($eduType) . " Level $levelNumber";
                $id = DB::table('question_levels')->insertGetId([
                    'education_type' => $eduType,
                    'name' => $levelName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $levelIds[] = $id;
                $levelMap[$id] = $levelName;
            }
        }

        // Step 2: Seed Subjects for each level
        $subjectIds = [];
        $subjects = ['English', 'Math', 'Science', 'Chinese'];

        foreach ($levelIds as $levelId) {
            foreach ($subjects as $subjectName) {
                $subjectIds[] = DB::table('question_subjects')->insertGetId([
                    'level_id' => $levelId,
                    'name' => $subjectName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Step 3: Create Questions (allowed types)
        $allowedTypes = ['mcq', 'fill_blank', 'true_false', 'linking'];
        $questionIds = [];

        for ($i = 0; $i < 20; $i++) {
            $type = collect($allowedTypes)->random();
            $levelId = collect($levelIds)->random();
            $subjectId = collect($subjectIds)->random();

            $questionIds[] = DB::table('questions')->insertGetId([
                'type' => $type,
                'content' => $faker->sentence(),
                'explanation' => $faker->text(150),
                'metadata' => json_encode(['difficulty' => 'medium']),
                'group_id' => null,
                'level_id' => $levelId,
                'subject_id' => $subjectId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Step 4: MCQ Options
        foreach ($questionIds as $questionId) {
            $question = DB::table('questions')->where('id', $questionId)->first();

            if ($question->type === 'mcq') {
                $labels = ['A', 'B', 'C', 'D'];
                foreach ($labels as $label) {
                    DB::table('question_options')->insert([
                        'question_id' => $questionId,
                        'label' => $label,
                        'value' => $faker->word(),
                        'is_correct' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Mark one option as correct
                DB::table('question_options')
                    ->where('question_id', $questionId)
                    ->inRandomOrder()
                    ->limit(1)
                    ->update(['is_correct' => 1]);
            }
        }

        // Step 5: Answers
        foreach ($questionIds as $questionId) {
            DB::table('question_answers')->insert([
                'question_id' => $questionId,
                'answer' => json_encode(['answer' => $faker->word()]),
                'format' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Step 6: Tags
        foreach ($questionIds as $questionId) {
            DB::table('question_tags')->insert([
                [
                    'question_id' => $questionId,
                    'tag_type' => 'subject',
                    'value' => $faker->randomElement($subjects),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'question_id' => $questionId,
                    'tag_type' => 'level',
                    'value' => $levelMap[DB::table('questions')->where('id', $questionId)->value('level_id')],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // Step 7: Difficulty
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
