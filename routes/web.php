<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\TeachingScheduleController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Middleware\RoleAdmin;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\UserResetController;
use App\Http\Controllers\Admin\SubjectController;


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

Route::get('/jurnal/{journal}/edit', [JournalController::class, 'edit'])
    ->middleware(['auth', 'verified'])
    ->name('journal.edit');

Route::put('/jurnal/{journal}', [JournalController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('journal.update');

Route::get('/jurnal', [JournalController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('journal.index');

Route::get('/jurnal/export', [JournalController::class, 'exportPdf'])
    ->middleware(['auth', 'verified'])
    ->name('jurnal.export');

Route::get('/data-siswa', [StudentController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('student.index');

Route::get('/kalender-pendidikan', [CalendarController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('calendar.index');
    
Route::get('/jadwal-mengajar', [TeachingScheduleController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('schedule.index');

Route::get('/jadwal-mengajar/export', [App\Http\Controllers\TeachingScheduleController::class, 'exportPdf'])
    ->middleware(['auth', 'verified'])
    ->name('schedule.export');

Route::middleware(['auth', RoleAdmin::class.':admin_kurikulum,administrator'])->prefix('kurikulum')->name('kurikulum.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::patch('/academic-years/{id}/activate', [AcademicYearController::class, 'activate'])->name('academic-years.activate');

    Route::get('/students', [StudentManagementController::class, 'index'])->name('students.index');
    Route::post('/students/assign', [StudentManagementController::class, 'assignClass'])->name('students.assign');
    Route::post('/students/status', [StudentManagementController::class, 'updateStatus'])->name('students.status');
    Route::post('/students/import', [StudentManagementController::class, 'import'])->name('students.import');
    Route::get('/students/template', [StudentManagementController::class, 'downloadTemplate'])->name('students.template');

    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

    Route::get('/master-data/users-reset', [UserResetController::class, 'index'])->name('users-reset.index');
    Route::post('/master-data/users/{user}/reset', [UserResetController::class, 'reset'])->name('users-reset.reset');

    Route::post('/subjects/import', [SubjectController::class, 'import'])->name('subjects.import');
    Route::get('/subjects/template', [SubjectController::class, 'downloadTemplate'])->name('subjects.template');
    Route::post('/subjects/{subject}/assign', [SubjectController::class, 'assignTeachers'])->name('subjects.assign');
    Route::resource('subjects', SubjectController::class)->except(['create', 'show', 'edit']);
});

Route::middleware(['auth', RoleAdmin::class.':administrator'])->prefix('system')->name('system.')->group(function () {
    // Route Manajemen User, Tambah Role, dan Impersonate masuk sini
});

require __DIR__.'/auth.php';
