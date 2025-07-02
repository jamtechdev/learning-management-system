<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ComprehensionQuestionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip blank rows
        if (empty(array_filter($row))) {
            return null;
        }

        // Build subquestions array
        $subquestions = [];

        for ($i = 1; $i <= 8; $i++) {
            $typeKey = "sub_type_$i";
            $questionKey = "question_$i";
            $answerKey = "answer_$i";

            if (!isset($row[$typeKey]) || !isset($row[$questionKey])) {
                continue;
            }

            $subType = strtolower(trim($row[$typeKey]));
            $question = trim($row[$questionKey]);

            $sub = [
                'type' => $subType,
                'question' => $question,
                'answer' => $row[$answerKey] ?? ''
            ];

            // If MCQ, collect up to 4 options
            if ($subType === 'mcq') {
                $options = [];
                for ($j = 1; $j <= 4; $j++) {
                    $optKey = "option_{$i}_{$j}";
                    if (!empty($row[$optKey])) {
                        $options[] = trim($row[$optKey]);
                    }
                }
                $sub['options'] = $options;
            }

            $subquestions[] = $sub;
        }

        // Build the metadata object
        $metadata = [
            'education_type' => $row['education_type'],
            'level_id' => $row['level_id'],
            'subject_id' => $row['subject_id'],
            'type' => $row['type'],
            'instruction' => $row['instruction'],
            'passage' => $row['passage'],
            'subquestions' => $subquestions,
        ];

        // Save the question
        return new Question([
            'education_type' => $row['education_type'],
            'level_id' => $row['level_id'],
            'subject_id' => $row['subject_id'],
            'topic_id' => $row['topic_id'],
            'type' => $row['type'],
            'instruction' => $row['instruction'],
            'content' => $row['passage'],
            'explanation' => $row['explanation'] ?? null,
            'metadata' => $metadata,
        ]);
    }
}
