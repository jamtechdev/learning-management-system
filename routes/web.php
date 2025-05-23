<?php

use App\Http\Controllers\Admin\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ParentController;
use App\Http\Controllers\Admin\StudentController;

// Redirect root to login
Route::get('/', function () {
    return redirect('login');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/users', [ProfileController::class, 'index'])->name('users.index');
});

// Admin routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'dashboard'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    // Question Management: Questions
    Route::prefix('questions')->name('admin.questions.')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('index');
        Route::get('/create', [QuestionController::class, 'create'])->name('create');
        Route::post('/', [QuestionController::class, 'store'])->name('store');
        Route::get('/{id}', [QuestionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [QuestionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [QuestionController::class, 'update'])->name('update');
        Route::delete('/{id}', [QuestionController::class, 'destroy'])->name('destroy');
    });

    // Question Management: Levels
    Route::prefix('levels')->name('admin.levels.')->group(function () {
        Route::get('/', [LevelController::class, 'index'])->name('index');
        Route::get('/create', [LevelController::class, 'create'])->name('create');
        Route::post('/', [LevelController::class, 'store'])->name('store');
        // For edit and update, use educationType as URL parameter (Primary or Secondary)
        Route::get('levels/{educationType}/edit', [LevelController::class, 'edit'])->name('edit');
        Route::post('levels/{educationType}', [LevelController::class, 'update'])->name('update');
        Route::delete('/{id}', [LevelController::class, 'destroy'])->name('destroy');
    });

    // Question Management: Subjects
    Route::prefix('subjects')->name('admin.subjects.')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::get('/create', [SubjectController::class, 'create'])->name('create');
        Route::post('/', [SubjectController::class, 'store'])->name('store');
        Route::get('/{id}', [SubjectController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SubjectController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SubjectController::class, 'update'])->name('update');
        Route::delete('/{id}', [SubjectController::class, 'destroy'])->name('destroy');
    });
    Route::resource('parents', ParentController::class)->names('admin.parents');

    Route::get('parents/{parent}/students', [StudentController::class, 'studentsByParent'])->name('admin.parents.students');
    Route::get('parents/{parent}/students', [ParentController::class, 'viewStudents'])->name('admin.parents.students');
    Route::get('/admin/students/{student}/edit', [StudentController::class, 'edit'])->name('admin.student.edit');
    Route::put('/admin/students/{student}', [StudentController::class, 'update'])->name('admin.student.update');

    Route::resource('student', StudentController::class)->names('admin.student');
});

require __DIR__ . '/auth.php';
