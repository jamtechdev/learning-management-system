<?php

namespace App\Enum;

final class EductionType
{
    public const PRIMARY = 'primary';
    public const SECONDARY = 'secondary';


    public static function all(): array
    {
        return [
            self::PRIMARY => [1, 2, 3, 4, 5],
            self::SECONDARY => [6, 7, 8, 9, 10, 11, 12],
        ];
    }


    public static function names(): array
    {
        return [
            self::PRIMARY => 'Primary',
            self::SECONDARY => 'Secondary',
        ];
    }
    public static function subjects(): array
    {
        return ['English', 'Math', 'Science', 'Chinese'];
    }
}
