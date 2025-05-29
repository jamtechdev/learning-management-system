<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\QuestionController;

Route::group(['prefix' => 'v1'], function () {

    Route::prefix('auth')->group(function () {
        Route::post('/register', [\App\Http\Controllers\API\AuthController::class, 'register']);
        Route::post('/login', [\App\Http\Controllers\API\AuthController::class, 'login']);
        Route::post('/student-login', [\App\Http\Controllers\API\AuthController::class, 'studentLogin']);
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [\App\Http\Controllers\API\AuthController::class, 'logout']);

        Route::prefix('questions')->group(function () {
            Route::get('levels', [\App\Http\Controllers\API\QuestionController::class, 'getAllLevels']);
            Route::get('subjects', [\App\Http\Controllers\API\QuestionController::class, 'getAllSubjects']);
            Route::post('getTypeBasedQuestions', [\App\Http\Controllers\API\QuestionController::class, 'getTypeBasedQuestions']);
            Route::get('all', [\App\Http\Controllers\API\QuestionController::class, 'getAllQuestions']);
        });




    });
});
