<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class McqQuestionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['question'])) {
            return null;
        }

        $data = [
            'content' => $row['question'],
            'options' => [
                ['value' => $row['option_a'], 'explanation' => $row['explanation_a']],
                ['value' => $row['option_b'], 'explanation' => $row['explanation_b']],
                ['value' => $row['option_c'], 'explanation' => $row['explanation_c']],
                ['value' => $row['option_d'], 'explanation' => $row['explanation_d']],
            ],
            'correct_option' => $row['answer'],
            'explanation' => null,
            'education_type' => $row['education_type'] ?? 'primary',
            'subject_id' => $row['subject_id'],
            'level_id' => $row['level_id'],
            'topic_id' => $row['topic_id'],
            'type' => 'mcq',
            'instruction' => $row['instruction'] ?? 'This is mcq',  // Default instruction if missing
        ];


        return $this->saveMcqQuestion($data);
    }

    public function saveMcqQuestion(array $data)
    {
        $correctValue = trim($data['correct_option']); // The correct option is a value like "did"

        $structuredOptions = array_map(function ($option) use ($correctValue) {
            return [
                'value' => $option['value'],
                'explanation' => $option['explanation'],
                'is_correct' => trim($option['value']) === $correctValue, // Match by value
            ];
        }, $data['options']);

        // Find the actual correct answer value
        $correctAnswer = collect($structuredOptions)->firstWhere('is_correct', true);

        $answer = [
            'answer' => $correctAnswer['value'] ?? null,
            'format' => 'text',
        ];

        $payload = $data;
        $payload['options'] = $structuredOptions;
        $payload['answer'] = $answer;
        $payload['instruction'] = $data['instruction'] ?? '';
        unset($payload['correct_option']); // Remove raw value
        // dd($payload);
        $question = new Question();
        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->education_type = $data['education_type'];
        $question->subject_id = $data['subject_id'];
        $question->level_id = $data['level_id'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $payload; // Save structured data in metadata
        $question->save();

        return $question;
    }
}
