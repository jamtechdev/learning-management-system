<?php

use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\AssignmentQuestionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\{
    HomeController,
    QuestionController,
    LevelController,
    SubjectController,
    ParentController,
    StudentController,
    SubscriptionController,
    TopicController
};
use App\Models\Subscription;

// Redirect root to login
Route::get('/', fn() => redirect('login'));

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/users', [ProfileController::class, 'index'])->name('users.index');
});
require __DIR__ . '/auth.php';
// Admin routes
Route::prefix('admin')->middleware(['auth'])->group(function () {

    Route::get('/dashboard', [HomeController::class, 'dashboard'])->middleware('verified')->name('dashboard');

    Route::prefix('questions')->name('admin.questions.')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('index');
        Route::get('/create', [QuestionController::class, 'create'])->name('create');
        Route::post('/', [QuestionController::class, 'store'])->name('store');
        Route::get('/{id}', [QuestionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [QuestionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [QuestionController::class, 'update'])->name('update');
        Route::delete('/{id}', [QuestionController::class, 'destroy'])->name('destroy');
        Route::post('/import-question', [\App\Http\Controllers\Admin\QuestionImportController::class, '__invoke'])->name('import');
    });

    Route::prefix('levels')->name('admin.levels.')->group(function () {
        Route::get('/', [LevelController::class, 'index'])->name('index');
        Route::get('/create', [LevelController::class, 'create'])->name('create');
        Route::post('/', [LevelController::class, 'store'])->name('store');
        Route::get('/{educationType}/edit', [LevelController::class, 'edit'])->name('edit');
        Route::post('/{educationType}', [LevelController::class, 'update'])->name('update');
        Route::delete('/{id}', [LevelController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('subjects')->name('admin.subjects.')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::get('/create', [SubjectController::class, 'create'])->name('create');
        Route::post('/', [SubjectController::class, 'store'])->name('store');
        Route::get('/{id}', [SubjectController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SubjectController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SubjectController::class, 'update'])->name('update');
        Route::delete('/{id}', [SubjectController::class, 'destroy'])->name('destroy');
    });

    Route::resource('topics', TopicController::class)->names('admin.topics');


    Route::resource('parents', ParentController::class)->names('admin.parents');

    Route::get('parents/{parent}/students', [ParentController::class, 'viewStudents'])->name('admin.parents.students');

    Route::prefix('student')->name('admin.student.')->group(function () {
        Route::get('/{id}', [StudentController::class, 'index'])->name('index');
        Route::get('/create/{parent}', [StudentController::class, 'create'])->name('create');
        Route::post('/{id}', [StudentController::class, 'store'])->name('store');
        Route::get('/{student}', [StudentController::class, 'show'])->name('show');
        Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('edit');
        Route::put('/{student}', [StudentController::class, 'update'])->name('update');
        Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('assignments')->name('admin.assignments.')->group(function () {
        Route::get('/', [AssignmentController::class, 'index'])->name('index');
        Route::get('/create', [AssignmentController::class, 'create'])->name('create');
        Route::post('/index', [AssignmentController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AssignmentController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [AssignmentController::class, 'update'])->name('update');
        Route::delete('/assignments/{id}/delete', [AssignmentController::class, 'delete'])->name('delete');

        // Assessment Questions

        Route::get('/questions/{assessment_id}', [AssignmentQuestionController::class, 'index'])->name('question');
        Route::get('/questions/create/{assessment_id}', [AssignmentQuestionController::class, 'create'])->name('questioncreate');
        Route::post('/questions/store', [AssignmentQuestionController::class, 'store'])->name('questionstore');

        Route::get('assignments/questions/{id}/edit', [AssignmentQuestionController::class, 'edit'])->name('questionedit');
        Route::put('assignments/questions/{id}', [AssignmentQuestionController::class, 'update'])->name('questionupdate');

        Route::delete('assignments/questions/{id}', [AssignmentQuestionController::class, 'destroy'])->name('questiondelete');
    });



    Route::prefix('subscriptions')->name('admin.subscriptions.')->group(function () {

        Route::get('/plans', [SubscriptionController::class, 'plans'])->name('index');
        Route::get('/plans/create', [SubscriptionController::class, 'create'])->name('create');
        Route::post('/plans', [SubscriptionController::class, 'store'])->name('store');
        Route::get('/plans/{plan}/edit', [SubscriptionController::class, 'edit'])->name('edit');
        Route::put('/plans/{plan}', [SubscriptionController::class, 'update'])->name('update');
        Route::delete('/plans/{plan}', [SubscriptionController::class, 'destroy'])->name('destroy');

        // Assign Subjects routes
        Route::get('/plans/{plan}/assign-subjects', [SubscriptionController::class, 'showAssignSubjectsForm'])->name('assignSubjects');
        Route::post('/plans/{plan}/assign-subjects', [SubscriptionController::class, 'assignSubjects'])->name('assignSubjects.store');
    });
});
