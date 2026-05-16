<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\User;
use App\Models\Subject;
use App\Models\AcademicYear;

class DashboardController extends Controller
{
    public function index()
    {
        $total_siswa = Student::count();
        $total_kelas = Classroom::count();
        $total_guru = User::where('role', 'guru')->count();
        $total_mapel = class_exists(Subject::class) ? Subject::count() : 0;
        $reset_requests = User::where('is_requesting_password_reset', true)->count();
        
        $student_stats = [
            'aktif' => Student::where('status', 'aktif')->count(),
            'mutasi' => Student::where('status', 'mutasi')->count(),
            'keluar' => Student::where('status', 'keluar')->count(),
        ];

        $active_year = AcademicYear::where('is_active', true)->first();

        return Inertia::render('Admin/Dashboard', [
            'total_siswa' => $total_siswa,
            'total_kelas' => $total_kelas,
            'total_guru' => $total_guru,
            'total_mapel' => $total_mapel,
            'reset_requests' => $reset_requests,
            'student_stats' => $student_stats,
            'active_year' => $active_year,
        ]);
    }
}
