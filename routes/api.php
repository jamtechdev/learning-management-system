<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\QuestionController;

Route::group(['prefix' => 'v1'], function () {

    // Public routes
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);


    Route::middleware('auth:sanctum')->group(function () {
        // Protected routes
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });


    Route::prefix('questions')->group(function () {
        Route::get('all', [\App\Http\Controllers\API\QuestionController::class, 'getAllQuestions']);
    });
});
