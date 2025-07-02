<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Topic;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TrueFalseQuestionImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Transform the row into your desired format
        $data = [
            'topic_id' => $row['topic_id'],
            'education_type' => $row['education_type'],
            'level_id' => $row['level_id'],
            'subject_id' => $row['subject_id'],
            'type' => 'true_false',
            'content' => $row['content'],
            'instruction' => $row['instruction'] ?? '',
            'true_false_answer' => $row['true_false_answer'],
            'explanation' => $row['explanation'] ?? null,
            'format' => $row['format'] ?? 'text',
        ];

        // Call the saveTrueFalseQuestion method
        return $this->saveTrueFalseQuestion($data);
    }

    private function saveTrueFalseQuestion($data)
    {
        $transformed = [
            'type' => 'true_false',
            'content' => $data['content'],
            'instruction' => $data['instruction'] ?? '',
            'options' => [
                ['value' => 'True'],
                ['value' => 'False'],
            ],
            'answer' => [
                'choice' => $data['true_false_answer'],
                'explanation' => $data['explanation'] ?? null,
                'format' => $data['format'] ?? 'text',
            ],
        ];

        $question = new Question();
        $question->topic_id = $data['topic_id'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();

        return $question;
    }
}
