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
            Route::get('levels', [QuestionController::class, 'getAllLevels']);
            Route::get('subjects', [QuestionController::class, 'getAllSubjects']);
            Route::post('getTypeBasedQuestions', [QuestionController::class, 'getTypeBasedQuestions']);
            Route::post('user-answer', [QuestionController::class, 'userAnswer']);
            Route::get('all', [QuestionController::class, 'getAllQuestions']);
        });
    });
    Route::middleware(['auth:sanctum', 'role:parent'])
        ->prefix('parent')
        ->group(function () {
            Route::apiResource('student', \App\Http\Controllers\API\StudentController::class);
            Route::post('student/{student}/lock-code', [\App\Http\Controllers\API\StudentController::class, 'lockCode']);
        });
});
