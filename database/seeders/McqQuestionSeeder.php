<?php

namespace Database\Seeders;

use App\Models\QuestionLevel;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\QuestionOption;

class McqQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [];
        $this->command->info('Seeding MCQ questions...');
        $this->command->info('Generating MCQ questions...');
        foreach (QuestionLevel::all() as $key => $level) {
            if ($level->subjects->count() <= 0) {
                continue;
            }
            foreach (range(1, 10) as $i) {
                $options = collect(range(1, 4))->map(fn($num) => fake()->word())->toArray();
                $questions[] = [
                    'type' => 'mcq',
                    'content' => fake()->sentence(),
                    'education_type' => $level->education_type,
                    'subject_id' => $level->subjects()->inRandomOrder()->first()->id, // adjust to match your subject ID
                    'level_id' => $level->id,   // adjust to match your level ID
                    'instruction' => 'Choose the correct answer.',
                    'options' => $options,
                    'correct_option' => rand(1, 4),
                    'explanation' => 'Paris is the capital of France.'
                ];
            }
        }


        $this->command->info('Seeding MCQ questions...');

        foreach ($questions as $data) {
            $correctIndex = (int) $data['correct_option'];
            $structuredOptions = array_map(function ($option, $index) use ($correctIndex) {
                return [
                    'value' => $option,
                    'is_correct' => ($index === $correctIndex),
                ];
            }, $data['options'], array_keys($data['options']));

            $answer = [
                'answer' => $structuredOptions[$correctIndex]['value'] ?? null,
                'format' => 'text',
            ];

            $payload = $data;
            $payload['options'] = $structuredOptions;
            $payload['answer'] = $answer;
            $payload['instruction'] = $data['instruction'] ?? '';
            unset($payload['correct_option']);

            $question = new Question();
            $question->type = $data['type'];
            $question->content = $data['content'];
            $question->education_type = $data['education_type'];
            $question->subject_id = $data['subject_id'];
            $question->level_id = $data['level_id'];
            $question->explanation = $data['explanation'] ?? null;
            $question->metadata = $payload;
            $question->save();

            foreach ($structuredOptions as $index => $option) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'label' => chr(65 + $index),
                    'value' => $option['value'],
                    'is_correct' => $option['is_correct'],
                ]);
            }
        }

        $this->command->info('MCQ questions seeded successfully.');
    }
}
