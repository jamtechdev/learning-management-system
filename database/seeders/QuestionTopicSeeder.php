<?php

namespace Database\Seeders;

use App\Models\QuestionLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 10) as $key => $value) {
            $level = QuestionLevel::with('subjects')->inRandomOrder()->first();
            $subject = $level->subjects()->inRandomOrder()->first();
            $topic = [
                'level_id' => $level->id,
                'subject_id' => $subject->id,
                'name' => fake()->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            \App\Models\QuestionTopic::create($topic);
        }
    }
}
