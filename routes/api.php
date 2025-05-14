<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\QuestionController;

Route::group(['prefix' => 'v1'], function () {
    Route::prefix('questions')->group(function () {
        Route::get('all', [\App\Http\Controllers\API\QuestionController::class, 'getAllQuestions']);
    });
});
