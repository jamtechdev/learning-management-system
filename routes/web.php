<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\QuestionController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified'])->name('dashboard');

    // Question CRUD routes
    Route::prefix('questions')->name('admin.questions.')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('index');          // List all
        Route::get('/create', [QuestionController::class, 'create'])->name('create');   // Show create form
        Route::post('/', [QuestionController::class, 'store'])->name('store');          // Save new question
        Route::get('/{id}', [QuestionController::class, 'show'])->name('show');         // Show one
        Route::get('/{id}/edit', [QuestionController::class, 'edit'])->name('edit');    // Show edit form
        Route::put('/{id}', [QuestionController::class, 'update'])->name('update');     // Update question
        Route::delete('/{id}', [QuestionController::class, 'destroy'])->name('destroy');// Delete
    });
});

require __DIR__ . '/auth.php';
