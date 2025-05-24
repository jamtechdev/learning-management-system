<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ParentController;
use App\Http\Controllers\Admin\StudentController;

// Redirect root to login
Route::get('/', fn() => redirect('login'));

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/users', [ProfileController::class, 'index'])->name('users.index');
});

// Admin routes
Route::prefix('admin')->middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->middleware('verified')->name('dashboard');

    // Questions CRUD
    Route::prefix('questions')->name('admin.questions.')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('index');
        Route::get('/create', [QuestionController::class, 'create'])->name('create');
        Route::post('/', [QuestionController::class, 'store'])->name('store');
        Route::get('/{id}', [QuestionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [QuestionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [QuestionController::class, 'update'])->name('update');
        Route::delete('/{id}', [QuestionController::class, 'destroy'])->name('destroy');
    });

    // Levels CRUD
    Route::prefix('levels')->name('admin.levels.')->group(function () {
        Route::get('/', [LevelController::class, 'index'])->name('index');
        Route::get('/create', [LevelController::class, 'create'])->name('create');
        Route::post('/', [LevelController::class, 'store'])->name('store');
        Route::get('/{educationType}/edit', [LevelController::class, 'edit'])->name('edit');
        Route::post('/{educationType}', [LevelController::class, 'update'])->name('update');
        Route::delete('/{id}', [LevelController::class, 'destroy'])->name('destroy');
    });

    // Subjects CRUD
    Route::prefix('subjects')->name('admin.subjects.')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::get('/create', [SubjectController::class, 'create'])->name('create');
        Route::post('/', [SubjectController::class, 'store'])->name('store');
        Route::get('/{id}', [SubjectController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SubjectController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SubjectController::class, 'update'])->name('update');
        Route::delete('/{id}', [SubjectController::class, 'destroy'])->name('destroy');
    });

    // Parents CRUD
    Route::resource('parents', ParentController::class)->names('admin.parents');

    // Show students under a parent
    Route::get('parents/{parent}/students', [ParentController::class, 'viewStudents'])->name('admin.parents.students');

    // Student Custom CRUD (fully manual)
    Route::prefix('student')->name('admin.student.')->group(function () {
        Route::get('/{id}', [StudentController::class, 'index'])->name('index');
        Route::get('/create/{parent}', [StudentController::class, 'create'])->name('create');
        Route::post('/{id}', [StudentController::class, 'store'])->name('store');
        Route::get('/{student}', [StudentController::class, 'show'])->name('show');
        Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('edit');
        Route::put('/{student}', [StudentController::class, 'update'])->name('update');
        Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
