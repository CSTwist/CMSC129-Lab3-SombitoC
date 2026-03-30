<?php

use App\Http\Controllers\JournalController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SignupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes (Only accessible when not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/signup', [SignupController::class, 'create'])->name('signup');
    Route::post('/signup', [SignupController::class, 'store']);
});

// Protected Routes (Only accessible when authenticated)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Dashboard & Journal Entries
    Route::get('/dashboard', [JournalController::class, 'index'])->name('dashboard');
    Route::get('/journals/create', [JournalController::class, 'create'])->name('journals/create');
    Route::post('/journals', [JournalController::class, 'store'])->name('journals/store');

    // NEW: Route for the dedicated Edit page
    Route::get('/journals/{id}/edit', [JournalController::class, 'edit'])->name('journals/edit');
    Route::put('/journals/{id}', [JournalController::class, 'update'])->name('journals/update');
    Route::delete('/journals/{id}', [JournalController::class, 'destroy'])->name('journals/delete');

    // Profile
    Route::get('/profile', function () {
        return view('layouts.profile');
    })->name('profile');

    // Recently Deleted (Trash)
    Route::get('/recently-deleted', [JournalController::class, 'trash'])->name('recently-deleted');
    Route::post('/journals/{id}/restore', [JournalController::class, 'restore'])->name('journals/restore');
    Route::delete('/journals/{id}/force', [JournalController::class, 'forceDelete'])->name('journals/forceDelete');
    Route::delete('/trash/empty', [JournalController::class, 'emptyTrash'])->name('trash/empty');
});
