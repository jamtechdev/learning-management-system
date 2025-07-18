<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\{
    HomeController,
    QuestionController,
    QuestionImportController,
    LevelController,
    SubjectController,
    TopicController,
    ParentController,
    StudentController,
    AssignmentQuestionController,
    SubscriptionController
};

// Redirect root to login
Route::get('/', fn() => redirect('login'));

// Authenticated user routes
Route::middleware('auth')->group(function () {
    // User profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User listing (admin only)
    Route::get('/users', [ProfileController::class, 'index'])->name('users.index');
});

require __DIR__.'/auth.php';

// -----------------------------
// Admin Panel Routes (prefix: /admin)
// -----------------------------
Route::prefix('admin')->middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->middleware('verified')->name('dashboard');

    // --------------------
    // Question Management
    // --------------------
    Route::prefix('questions')->name('admin.questions.')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('index');
        Route::get('/create', [QuestionController::class, 'create'])->name('create');
        Route::post('/', [QuestionController::class, 'store'])->name('store');
        Route::get('/{id}', [QuestionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [QuestionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [QuestionController::class, 'update'])->name('update');
        Route::delete('/{id}', [QuestionController::class, 'destroy'])->name('destroy');

        Route::post('/import-question', [QuestionImportController::class, '__invoke'])->name('import');
        Route::post('/sample-download', [QuestionImportController::class, 'downloadSample'])->name('download');
    });

    // --------------------
    // Levels
    // --------------------
    Route::prefix('levels')->name('admin.levels.')->group(function () {
        Route::get('/', [LevelController::class, 'index'])->name('index');
        Route::get('/create', [LevelController::class, 'create'])->name('create');
        Route::post('/', [LevelController::class, 'store'])->name('store');
        Route::get('/{educationType}/edit', [LevelController::class, 'edit'])->name('edit');
        Route::post('/{educationType}', [LevelController::class, 'update'])->name('update');
        Route::delete('/{id}', [LevelController::class, 'destroy'])->name('destroy');
    });

    // --------------------
    // Subjects
    // --------------------
    Route::prefix('subjects')->name('admin.subjects.')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::get('/create', [SubjectController::class, 'create'])->name('create');
        Route::post('/', [SubjectController::class, 'store'])->name('store');
        Route::get('/{id}', [SubjectController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SubjectController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SubjectController::class, 'update'])->name('update');
        Route::delete('/{id}', [SubjectController::class, 'destroy'])->name('destroy');
    });

    // --------------------
    // Topics
    // --------------------
    Route::resource('topics', TopicController::class)->names('admin.topics');

    // --------------------
    // Parents & Students
    // --------------------
    Route::resource('parents', ParentController::class)->names('admin.parents');

    Route::prefix('student')->name('admin.student.')->group(function () {
        Route::get('/{id}', [StudentController::class, 'index'])->name('index');
        Route::get('/create/{parent}', [StudentController::class, 'create'])->name('create');
        Route::post('/{id}', [StudentController::class, 'store'])->name('store');
        Route::get('/{student}', [StudentController::class, 'show'])->name('show');
        Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('edit');
        Route::put('/{student}', [StudentController::class, 'update'])->name('update');
        Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');
    });

    // --------------------
    // Assignments (Admin)
    // --------------------
    Route::prefix('assignments')->name('admin.assignments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AssignmentController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\AssignmentController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\AssignmentController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [\App\Http\Controllers\Admin\AssignmentController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [\App\Http\Controllers\Admin\AssignmentController::class, 'delete'])->name('delete');

        // Route to get questions for an assignment (for modal)
        Route::get('/{id}/questions', [\App\Http\Controllers\Admin\AssignmentController::class, 'questions'])->name('questions');

        // Alpine.js admin UI for assignments
        Route::get('/alpine', function () {
            return view('admin.assignments.alpine');
        })->name('alpine');
    });

    // --------------------
    // Subscriptions
    // --------------------
    Route::prefix('subscriptions')->name('admin.subscriptions.')->group(function () {
        Route::get('/plans', [SubscriptionController::class, 'plans'])->name('index');
        Route::get('/plans/create', [SubscriptionController::class, 'create'])->name('create');
        Route::post('/plans', [SubscriptionController::class, 'store'])->name('store');
        Route::get('/plans/{plan}/edit', [SubscriptionController::class, 'edit'])->name('edit');
        Route::put('/plans/{plan}', [SubscriptionController::class, 'update'])->name('update');
        Route::delete('/plans/{plan}', [SubscriptionController::class, 'destroy'])->name('destroy');

        // Assign subjects to plan
        Route::get('/plans/{plan}/assign-subjects', [SubscriptionController::class, 'showAssignSubjectsForm'])->name('assignSubjects');
        Route::post('/plans/{plan}/assign-subjects', [SubscriptionController::class, 'assignSubjects'])->name('assignSubjects.store');
    });
});
