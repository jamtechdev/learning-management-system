<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LinkingQuestionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        dd($row);
        $options = json_decode($row['options'], true);
        $answer = [];

        foreach ($options as $option) {
            $answer[] = [
                'left' => [
                    'word' => $option['label_text'] ?? '',
                    'image_uri' => null,
                    'match_type' => $option['label_type'] ?? 'text',
                ],
                'right' => [
                    'word' => $option['value_text'] ?? '',
                    'image_uri' => null,
                    'match_type' => $option['value_type'] ?? 'text',
                ],
            ];
        }

        $transformed = [
            'type' => 'linking',
            'content' => $row['content'] ?? '',
            'explanation' => $row['explanation'] ?? '',
            'instruction' => $row['instruction'] ?? '',
            'format' => 'mapping',
            'answer' => $answer,
        ];



        return new Question([
            'education_type' => $row['education_type'],
            'level_id' => $row['level_id'],
            'subject_id' => $row['subject_id'],
            'topic_id' => $row['topic_id'],
            'type' => $row['type'],
            'instruction' => $row['instruction'],
            'content' => $row['content'],
            'explanation' => $row['explanation'] ?? null,
            'metadata' => $transformed,
        ]);
    }
}
