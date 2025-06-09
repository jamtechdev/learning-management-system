<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            McqQuestionSeeder::class,
            TrueFalseQuestionSeeder::class,
            LinkingQuestionSeeder::class,
            RearrangingQuestionSeeder::class,
            GrammarClozeWithOptionsQuestionSeeder::class,
            ComprehensionQuestionSeeder::class,
            UnderlineCorrectQuestionSeeder::class,
            EditingQuestionSeeder::class
        ]);
    }
}
