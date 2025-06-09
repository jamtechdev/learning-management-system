<?php

namespace Database\Seeders;

use App\Enum\EductionType;
use App\Models\QuestionLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // QuestionLevel::truncate();

        $data = [];
        foreach (EductionType::all() as $eduType => $levels) {
            foreach ($levels as $level) {
                $data[] = [
                    'education_type' => $eduType,
                    'name' => $level,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        QuestionLevel::insert($data);
    }
}
