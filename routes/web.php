<?php

use App\Http\Controllers\JournalController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SignupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts/login');
});

// login
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// sign up
Route::get('/signup', [SignupController::class, 'create'])->name('signup');
Route::post('/signup', [SignupController::class, 'store']);

// dashboard
Route::get('/dashboard', [JournalController::class, 'index'])->name('dashboard');
Route::get('/journals/create', [JournalController::class, 'create'])->name('journals/create');
Route::post('/journals', [JournalController::class, 'store'])->name('journals/store');
Route::put('/journals/{id}', [JournalController::class, 'update'])->name('journals/update');
Route::delete('/journals/{id}', [JournalController::class, 'destroy'])->name('journals/delete');

// profile
Route::get('/profile', function () {
    return view('layouts.profile');
})->name('profile');

// recently deleted
Route::get('/recently-deleted', function () {
    return view('layouts.recently-deleted');
})->name('recently-deleted');
