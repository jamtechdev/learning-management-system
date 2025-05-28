<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Step 1: Seed Levels (Primary 1–5, Secondary 6–12)
        $levelIds = [];

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
            }
        }

        // Step 2: Seed Subjects for each level
        $subjects = ['English', 'Math', 'Science', 'Chinese'];

        foreach ($levelIds as $levelId) {
            foreach ($subjects as $subjectName) {
                DB::table('question_subjects')->insert([
                    'level_id' => $levelId,
                    'name' => $subjectName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
