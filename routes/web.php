<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        // Simulasi data yang dikirim dari Backend
        'auth' => [
            'user' => [
                'name' => 'Mohammad Mutho',
                'email' => 'mutho@smansa.sch.id',
                'role' => 'Guru Informatika',
                'gender' => 'L', // 'L' untuk Laki-laki (Bapak), 'P' untuk Perempuan (Ibu)
            ]
        ]
    ]);
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/jurnal/create', function () {
    return Inertia::render('Journal/Create'); 
})->name('jurnal.create');

require __DIR__.'/auth.php';
