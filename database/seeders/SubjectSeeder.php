<?php

namespace Database\Seeders;

use App\Enum\EductionType;
use App\Models\QuestionLevel;
use App\Models\QuestionSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        foreach (QuestionLevel::all() as $key => $value) {
            foreach (EductionType::subjects() as $subject) {
                $data[] = [
                    'level_id' => $value->id,
                    'name' => $subject,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        QuestionSubject::insert($data);
    }
}
