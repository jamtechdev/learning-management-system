<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EditingQuestionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip blank row
        if (empty(array_filter($row))) {
            return null;
        }

        // Build questions array from flat structure
        $questions = [];
        for ($i = 1; $i <= 10; $i++) {
            $wrongKey = "wrong_$i";
            $correctKey = "correct_$i";

            if (!empty($row[$wrongKey]) && !empty($row[$correctKey])) {
                $questions[] = [
                    'box' => $i,
                    'wrong' => trim($row[$wrongKey]),
                    'correct' => trim($row[$correctKey]),
                ];
            }
        }

        // Inject <strong>wrong</strong> tags for each wrong word
        $cleanedParagraph = $row['paragraph'];
        foreach ($questions as $q) {
            // Remove any nested <strong> around the word (if exists), then wrap cleanly
            $pattern = '/<strong>+<\/strong>*/i';
            $cleanedParagraph = preg_replace_callback(
                '/<strong>+([^<]+)<\/strong>+/i',
                fn($matches) => $matches[1],
                $cleanedParagraph
            );

            // Then wrap only the raw word occurrences
            $cleanedParagraph = preg_replace(
                '/\b' . preg_quote($q['wrong'], '/') . '\b/i',
                '<strong>' . $q['wrong'] . '</strong>',
                $cleanedParagraph
            );
        }

        // Final metadata format
        $metadata = [
            'instruction' => $row['instruction'] ?? '',
            'education_type' => $row['education_type'],
            'level_id' => $row['level_id'],
            'subject_id' => $row['subject_id'],
            'type' => $row['type'],
            'paragraph' => $cleanedParagraph,
            'questions' => $questions,
        ];

        return new Question([
            'education_type' => $row['education_type'],
            'level_id' => $row['level_id'],
            'subject_id' => $row['subject_id'],
            'topic_id' => $row['topic_id'],
            'type' => $row['type'],
            'instruction' => $row['instruction'],
            'content' => $cleanedParagraph,
            'explanation' => $row['explanation'] ?? null,
            'metadata' => $metadata,
        ]);
    }
}
