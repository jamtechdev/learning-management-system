<?php

namespace App\Enum;

final class QuestionTypes
{
    // public const TYPES = ['mcq', 'true_false', 'linking', 'rearranging', 'grammar_cloze_with_options', 'underlinecorrect', 'comprehension', 'editing'];

    public const MCQ = 'mcq';
    public const TRUE_FALSE = 'true_false';
    public const LINKING = 'linking';
    public const REARRANGING = 'rearranging';
    public const FILL_IN_THE_BLANK = 'fill_in_the_blank';

    public const GRAMMAR_CLOZE_WITH_OPTIONS = 'grammar_cloze_with_options';

    //Open Cloze with Options
    public const OPEN_CLOZE_WITH_OPTIONS = 'open_cloze_with_options';
    public const UNDERLINECORRECT = 'underlinecorrect';
    // Open Cloze with dropdown Options
    public const OPEN_CLOZE_WITH_DROPDOWN_OPTIONS = 'open_cloze_with_dropdown_options';
    public const COMPREHENSION = 'comprehension';
    public const EDITING = 'editing';

    public const TYPES = [
        self::MCQ,
        self::TRUE_FALSE,
        self::LINKING,
        self::REARRANGING,
            // self::GRAMMAR_CLOZE_WITH_OPTIONS,
            // self::UNDERLINECORRECT,
        self::OPEN_CLOZE_WITH_OPTIONS,
        self::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS,
        self::COMPREHENSION,
        self::EDITING,
        self::FILL_IN_THE_BLANK
    ];


    public static function names(): array
    {
        return [
            self::MCQ => 'Mcq',
            self::TRUE_FALSE => 'True False',
            self::LINKING => 'Linking',
            self::REARRANGING => 'Rearranging',
                // self::GRAMMAR_CLOZE_WITH_OPTIONS => 'Grammar Cloze with Options',
                // self::UNDERLINECORRECT => 'Underline Correct',
            self::OPEN_CLOZE_WITH_OPTIONS => 'Open Cloze with Options',
            self::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS => 'Open Cloze with Dropdown Options',
            self::COMPREHENSION => 'Comprehension',
            self::EDITING => 'Editing',
            self::FILL_IN_THE_BLANK => 'Fill in the Blank'
        ];
    }



}
