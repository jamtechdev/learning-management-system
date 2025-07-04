<?php

namespace Database\Seeders;

use App\Models\QuestionLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        QuestionLevel::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [];

        // Primary education levels 1 to 6
        for ($i = 1; $i <= 6; $i++) {
            $data[] = [
                'education_type' => 'primary',
                'name' => (string)$i,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Secondary education levels 1 to 5
        for ($i = 1; $i <= 5; $i++) {
            $data[] = [
                'education_type' => 'secondary',
                'name' => (string)$i,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        QuestionLevel::insert($data);
    }
}
