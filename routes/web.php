<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JournalController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/jurnal/create/{schedule}', [JournalController::class, 'create'])
    ->middleware('auth', 'verified')
    ->name('jurnal.create');

Route::post('/jurnal/{schedule}', [JournalController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('journal.store');

Route::get('/jurnal/{schedule}/show', [JournalController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('journal.show');

require __DIR__.'/auth.php';
