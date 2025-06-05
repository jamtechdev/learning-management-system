<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE questions MODIFY COLUMN type ENUM(
            'mcq',
            'fill_blank',
            'rearranging',
            'linking',
            'true_false',
            'grouped',
            'comprehension',
            'grammar_cloze_with_options',
            'grammar_cloze'
        )");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE questions MODIFY COLUMN type ENUM(
            'mcq',
            'fill_blank',
            'rearranging',
            'linking',
            'true_false',
            'grouped',
            'comprehension'
        )");
    }
};
