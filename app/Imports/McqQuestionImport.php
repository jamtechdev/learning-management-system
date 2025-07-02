<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class McqQuestionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip invalid rows
        if (empty($row['question'])) {
            return null;
        }

        // Structure the data for the question
        $data = [
            'content' => $row['question'],  // Ensure 'question' column is used
            'options' => [
                ['value' => $row['option_a'], 'explanation' => $row['explanation_a']],
                ['value' => $row['option_b'], 'explanation' => $row['explanation_b']],
                ['value' => $row['option_c'], 'explanation' => $row['explanation_c']],
                ['value' => $row['option_d'], 'explanation' => $row['explanation_d']],
            ],
            'correct_option' => (int) $row['answer'],  // Use the column 'answer' for the correct option index
            'explanation' => null,  // You can add a general explanation if required here
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
        $correctIndex = (int) $data['correct_option'];

        // Structure the options with correctness flag and their explanations
        $structuredOptions = array_map(function ($option, $index) use ($correctIndex) {
            return [
                'value' => $option['value'],
                'explanation' => $option['explanation'],
                'is_correct' => $index === $correctIndex, // Mark the correct option
            ];
        }, $data['options'], array_keys($data['options']));

        // Prepare the answer (using the correct option)
        $answer = [
            'answer' => $structuredOptions[$correctIndex]['value'] ?? null,
            'format' => 'text',
        ];

        // Prepare the payload for metadata
        $payload = $data;
        $payload['options'] = $structuredOptions;  // Store the structured options
        $payload['answer'] = $answer;
        $payload['instruction'] = $data['instruction'];
        unset($payload['correct_option']);  // Remove the raw correct option index

        // Create and save the question
        $question = new Question();
        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->education_type = $data['education_type'];
        $question->subject_id = $data['subject_id'];
        $question->level_id = $data['level_id'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $payload;  // Save all structured data in metadata
        $question->save();

        return $question;
    }
}
