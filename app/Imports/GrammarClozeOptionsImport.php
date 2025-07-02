<?php

namespace App\Imports;

use App\Models\Question;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GrammarClozeOptionsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty(array_filter($row))) {
            return null;
        }

        // Step 1: Parse shared options from comma-separated string
        $sharedOptions = array_map('trim', explode(',', $row['shared_options'] ?? ''));

        // Step 2: Parse answers from pipe-separated string
        $answers = array_map('trim', explode('|', $row['answers'] ?? ''));
        $questions = [];

        foreach ($answers as $index => $answer) {
            $questions[] = [
                'id' => $index + 1,
                'blank_number' => $index + 1,
                'correct_answer' => $answer,
                'input_type' => 'input',
            ];
        }

        // Step 3: Create the meta structure
        $meta = [
            'paragraph' => $row['paragraph'],
            'question_type' => 'open_cloze_with_options',
            'question_group' => [
                'shared_options' => $sharedOptions
            ],
            'questions' => $questions,
            'instruction' => $row['instruction'] ?? '',
        ];

        // Step 4: Save the question
        return new Question([
            'education_type' => $row['education_type'],
            'level_id' => $row['level_id'],
            'subject_id' => $row['subject_id'],
            'topic_id' => $row['topic_id'],
            'type' => $row['type'],
            'instruction' => $row['instruction'],
            'content' => $row['paragraph'],
            'explanation' => null,
            'metadata' => $meta,
        ]);
    }
}
