<?php

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


Artisan::command('weekly:assign', function (string $user) {
    $this->info("Sending email to: {$user}!");
});


Schedule::command('assign:questions')->weeklyOn(1, '00:00');
