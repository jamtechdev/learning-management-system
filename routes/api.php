<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\QuestionController;

Route::group(['prefix' => 'v1'], function () {

    Route::prefix('auth')->group(function () {
        Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
        Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
        Route::post('/student-login', [\App\Http\Controllers\Api\AuthController::class, 'studentLogin']);
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

        Route::prefix('questions')->group(function () {
            Route::get('levels', [QuestionController::class, 'getAllLevels']);
            Route::get('subjects', [QuestionController::class, 'getAllSubjects']);
            Route::post('getTypeBasedQuestions', [QuestionController::class, 'getTypeBasedQuestions']);
            Route::get('all', [QuestionController::class, 'getAllQuestions']);
        });




    });
});
