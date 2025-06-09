<?php

namespace App\Enum;

final class QuestionTypes
{
    // public const TYPES = ['mcq', 'true_false', 'linking', 'rearranging', 'grammar_cloze_with_options', 'underlinecorrect', 'comprehension', 'editing'];

    public const MCQ = 'mcq';
    public const TRUE_FALSE = 'true_false';
    public const LINKING = 'linking';
    public const REARRANGING = 'rearranging';
    public const GRAMMAR_CLOZE_WITH_OPTIONS = 'grammar_cloze_with_options';
    public const UNDERLINECORRECT = 'underlinecorrect';
    public const COMPREHENSION = 'comprehension';
    public const EDITING = 'editing';

    public const TYPES = [
        self::MCQ,
        self::TRUE_FALSE,
        self::LINKING,
        self::REARRANGING,
        self::GRAMMAR_CLOZE_WITH_OPTIONS,
        self::UNDERLINECORRECT,
        self::COMPREHENSION,
        self::EDITING,
    ];


    public static function names(): array
    {
        return [
            self::MCQ => 'Mcq',
            self::TRUE_FALSE => 'TrueFalse',
            self::LINKING => 'Linking',
            self::REARRANGING => 'Rearranging',
            self::GRAMMAR_CLOZE_WITH_OPTIONS => 'Grammar Cloze with Options',
            self::UNDERLINECORRECT => 'Underline Correct',
            self::COMPREHENSION => 'Comprehension',
            self::EDITING => 'Editing',
        ];
    }



}
