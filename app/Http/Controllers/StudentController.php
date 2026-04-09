<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $classFilter = $request->input('classroom_id');

        // 1. Ambil daftar Kelas yang diajar oleh guru ini (untuk dropdown filter)
        $classrooms = Classroom::whereHas('schedules', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();

        // 2. Query Data Siswa dengan JOIN untuk Pengurutan
        $students = Student::with('classrooms')
            // --- PERBAIKAN DI SINI: Tambahkan alias kolom kelas ke dalam select ---
            ->select('students.*', 'classrooms.name as class_name_sort') 
            ->join('classroom_student', 'students.id', '=', 'classroom_student.student_id')
            ->join('classrooms', 'classroom_student.classroom_id', '=', 'classrooms.id')
            ->whereHas('classrooms.schedules', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('students.name', 'like', "%{$search}%")
                      ->orWhere('students.nisn', 'like', "%{$search}%");
                });
            })
            ->when($classFilter, function ($query, $classFilter) {
                $query->where('classrooms.id', $classFilter);
            })
            // --- PERBAIKAN DI SINI: Gunakan alias tadi untuk urutan pertama ---
            ->orderBy('class_name_sort', 'asc') 
            ->orderBy('students.name', 'asc')
            ->distinct() 
            ->paginate(15)
            ->withQueryString();
            
        return Inertia::render('Student/Index', [
            'students' => $students,
            'classrooms' => $classrooms,
            'filters' => [
                'search' => $search,
                'classroom_id' => $classFilter,
            ]
        ]);
    }
}