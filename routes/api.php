<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ParentController;

Route::group(['prefix' => 'v1'], function () {

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/student-login', [AuthController::class, 'studentLogin']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('email.verify');
        Route::post('/resend-verification-email', [AuthController::class, 'resendVerificationEmail']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
        Route::post('verify-email-otp', [AuthController::class, 'verifyOtpToken']);
        Route::post('update-password', [AuthController::class, 'updatePassword']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/password-reset', [AuthController::class, 'resetPassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::prefix('questions')->group(function () {
            Route::get('levels', [QuestionController::class, 'getAllLevels']);
            Route::get('subjects', [QuestionController::class, 'getAllSubjects']);
            Route::get('topics', [QuestionController::class, 'getAllTopics']);
            Route::post('getTypeBasedQuestions', [QuestionController::class, 'getTypeBasedQuestions']);
            Route::post('user-answer', [QuestionController::class, 'userAnswer']);
            Route::get('all', [QuestionController::class, 'getAllQuestions']);
        });
    });

    Route::middleware(['auth:sanctum', 'role:parent'])->prefix('parent')->group(function () {
        Route::apiResource('student', \App\Http\Controllers\API\StudentController::class)->except('update');
        Route::post('student/{student}/lock-code', [\App\Http\Controllers\API\StudentController::class, 'lockCode']);
        Route::post('student/{student}', [\App\Http\Controllers\API\StudentController::class, 'update']);
        Route::get('get-student-level', [\App\Http\Controllers\API\StudentController::class, 'getStudentLevel']);

         Route::get('my-students', [ParentController::class, 'getStudents']);
    });
});
