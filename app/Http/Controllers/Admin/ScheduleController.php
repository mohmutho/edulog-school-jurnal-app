<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $filterClassroom = $request->input('classroom_id');
        $filterTeacher = $request->input('user_id');

        // Ambil data jadwal berdasarkan tahun ajaran aktif dan filter kelas
        $schedules = Schedule::with(['user', 'classroom', 'subject'])
            ->when($activeYear, function ($query) use ($activeYear) {
                return $query->where('academic_year_id', $activeYear->id);
            })
            ->when($filterClassroom, function ($query) use ($filterClassroom) {
                return $query->where('classroom_id', $filterClassroom);
            })
            ->when($filterTeacher, function ($query) use ($filterTeacher) { // Logika filter guru
                return $query->where('user_id', $filterTeacher);
            })
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return Inertia::render('Admin/Schedule/Index', [
            'schedules' => $schedules,
            'classrooms' => Classroom::orderBy('name', 'asc')->get(),
            'subjects' => Subject::orderBy('name', 'asc')->get(),
            // Asumsi role guru disimpan di tabel users (sesuaikan jika logic role Bapak berbeda)
            'teachers' => User::where('role', 'guru')->orderBy('name', 'asc')->get(), 
            'activeYear' => $activeYear,
            'filters' => [
                'classroom_id' => $filterClassroom,
                'user_id' => $filterTeacher
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'user_id' => 'required|exists:users,id', // ID Guru
            'day_of_week' => 'required|integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // 1. ALGORITMA DETEKSI BENTROK GURU
        // Memastikan Guru A tidak mengajar di 2 kelas berbeda di jam yang bersinggungan
        $guruBentrok = Schedule::where('academic_year_id', $request->academic_year_id)
            ->where('user_id', $request->user_id)
            ->where('day_of_week', $request->day_of_week)
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>', $request->start_time);
            })->exists();

        if ($guruBentrok) {
            return back()->withErrors(['user_id' => 'Guru ini sudah memiliki jadwal mengajar di jam tersebut!']);
        }

        // 2. ALGORITMA DETEKSI BENTROK KELAS
        // Memastikan Kelas A tidak diisi oleh 2 Guru berbeda di jam yang bersinggungan
        $kelasBentrok = Schedule::where('academic_year_id', $request->academic_year_id)
            ->where('classroom_id', $request->classroom_id)
            ->where('day_of_week', $request->day_of_week)
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>', $request->start_time);
            })->exists();

        if ($kelasBentrok) {
            return back()->withErrors(['classroom_id' => 'Kelas ini sudah memiliki jadwal mata pelajaran lain di jam tersebut!']);
        }

        // Jika lolos kedua validasi, simpan jadwal
        Schedule::create($request->all());

        return back()->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}