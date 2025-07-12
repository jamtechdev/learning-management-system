<?php

use App\Console\Commands\AssignQuestionToAssignment;
use App\Enum\QuestionTypes;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    // $this->comment(Inspiring::quote());
    foreach (QuestionTypes::names() as $key => $value) {
        $value = str($value)->studly();
        Artisan::call("make:seeder {$value}QuestionSeeder");
    }
})->purpose('Display an inspiring quote');


// Artisan::command('assign:questions', function () {
//     $this->call(AssignQuestionToAssignment::class);
// })->describe('Assign questions based on student level');

Schedule::command('assign:questions')->weekly()->mondays()->at('00:00');
