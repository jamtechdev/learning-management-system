<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RearrangingQuestionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip row if all values are empty
        if (empty(array_filter($row))) {
            return null;
        }

        // Skip row if question_text is missing
        if (empty($row['question_text'])) {
            return null;
        }

        $questionText = trim($row['question_text']);
        $orderedAnswer = preg_split('/\s+/', $questionText);
        $shuffled = $orderedAnswer;
        shuffle($shuffled);

        $options = collect($shuffled)->map(fn($word) => [
            'value' => $word,
            'is_correct' => false,
        ])->values()->toArray();

        $transformed = [
            'type' => 'rearranging',
            'content' => $row['content'] ?? '',
            'instruction' => $row['instruction'] ?? '',
            'options' => $options,
            'answer' => [
                'answer' => $orderedAnswer,
                'format' => 'ordered',
            ],
        ];

        return new Question([
            'education_type' => $row['education_type'],
            'level_id' => $row['level_id'],
            'subject_id' => $row['subject_id'],
            'topic_id' => $row['topic_id'],
            'type' => $row['type'],
            'instruction' => $row['instruction'],
            'content' => $row['content'],
            'explanation' => null,
            'metadata' => $transformed,
        ]);
    }
}
