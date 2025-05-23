<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\QuestionController;

Route::group(['prefix' => 'v1'], function () {

    //public authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
        Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    });

    //protected authentication routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
    Route::prefix('questions')->group(function () {
        Route::get('levels', [\App\Http\Controllers\API\QuestionController::class, 'getAllLevels']);
        Route::get('subjects', [\App\Http\Controllers\API\QuestionController::class, 'getAllSubjects']);
        Route::post('getTypeBasedQuestions', [\App\Http\Controllers\API\QuestionController::class, 'getTypeBasedQuestions']);
        Route::get('all', [\App\Http\Controllers\API\QuestionController::class, 'getAllQuestions']);
    });
});
