<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionTopic;
use App\Models\QuestionLevel;
use App\Models\QuestionSubject;

class QuestionTopicSeeder extends Seeder
{
    public function run()
    {
        // Get some levels and subjects to associate with topics
        $levels = QuestionLevel::all();
        $subjects = QuestionSubject::all();

        if ($levels->isEmpty() || $subjects->isEmpty()) {
            $this->command->info('No levels or subjects found, skipping QuestionTopic seeding.');
            return;
        }

        // Create sample topics
        $topics = [
            [
                'name' => 'Algebra Basics',
                'description' => 'Introduction to algebraic concepts',
                'level_id' => $levels->random()->id,
                'subject_id' => $subjects->random()->id,
            ],
            [
                'name' => 'Geometry Fundamentals',
                'description' => 'Basic principles of geometry',
                'level_id' => $levels->random()->id,
                'subject_id' => $subjects->random()->id,
            ],
            [
                'name' => 'Grammar Rules',
                'description' => 'English grammar essentials',
                'level_id' => $levels->random()->id,
                'subject_id' => $subjects->random()->id,
            ],
            [
                'name' => 'World History',
                'description' => 'Overview of historical events',
                'level_id' => $levels->random()->id,
                'subject_id' => $subjects->random()->id,
            ],
        ];

        foreach ($topics as $topic) {
            QuestionTopic::create($topic);
        }

        $this->command->info('QuestionTopic seeder completed.');
    }
}
