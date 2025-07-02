<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FillBlankQuestionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip empty rows
        if (empty(array_filter($row))) {
            return null;
        }

        // Build blanks array from answer columns (e.g., answer_1, answer_2, etc.)
        $blanks = [];
        foreach ($row as $key => $value) {
            if (strpos($key, 'answer_') === 0 && !empty($value)) {
                $blankNumber = str_replace('answer_', '', $key);
                $blanks[] = [
                    'blank_number' => (string) $blankNumber,
                    'correct_answer' => trim($value),
                    'input_type' => 'input'
                ];
            }
        }

        // Construct the $metadata object
        $metadata = [
            'instruction' => $row['instruction'] ?? '',
            'type' => $row['type'],
            'question_text' => $row['question_text'],
            'blanks' => $blanks
        ];

        // Create and return the Question model
        return new Question([
            'education_type' => $row['education_type'],
            'level_id' => $row['level_id'],
            'subject_id' => $row['subject_id'],
            'topic_id' => $row['topic_id'],
            'type' => $row['type'],
            'content' => $row['question_text'],
            'explanation' => $row['explanation'] ?? null,
            'metadata' => $metadata,
        ]);
    }
}
