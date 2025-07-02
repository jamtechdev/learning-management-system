<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DropdownClozeQuestionImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Check for empty rows
        if (empty(array_filter($row))) {
            return null;
        }

        // Prepare the metadata structure
        $metadata = [
            'paragraph' => $row['paragraph'],
            'question_type' => 'open_cloze_with_dropdown_options',
            'question_group' => [
                'shared_options' => array_unique(array_merge(
                    explode(',', $row['options_1']),
                    explode(',', $row['options_2'])
                ))
            ],
            'questions' => [
                [
                    'id' => 1,
                    'blank_number' => 1,
                    'options' => explode(',', $row['options_1']),
                    'correct_answer' => $row['answer_1'],
                    'input_type' => 'dropdown'
                ],
                [
                    'id' => 2,
                    'blank_number' => 2,
                    'options' => explode(',', $row['options_2']),
                    'correct_answer' => $row['answer_2'],
                    'input_type' => 'dropdown'
                ]
            ],
            'instruction' => $row['instruction'] ?? ''
        ];

        // Create and save the question
        $question = new Question();
        $question->topic_id = $row['topic_id'];
        $question->type = $row['type'];
        $question->education_type = $row['education_type'];
        $question->level_id = $row['level_id'];
        $question->subject_id = $row['subject_id'];
        $question->content = $row['paragraph'];
        $question->explanation = null; // Optional if you have an explanation field
        $question->metadata = $metadata; // Save structured metadata
        $question->save();

        return $question;
    }
}
