<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Schedule;
use App\Models\Journal;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $now = now();
        $currentDayOfWeek = $now->dayOfWeekIso; 
        $isPast1800 = $now->format('H:i') >= '18:00';

        $awalMinggu = $now->copy()->startOfWeek(Carbon::MONDAY);
        $akhirMinggu = $now->copy()->endOfWeek(Carbon::SUNDAY);

        // 1. Ubah withExists menjadi with('journals') untuk mengambil datanya
        $schedules = Schedule::with(['classroom.students', 'subject', 'journals' => function ($query) use ($awalMinggu, $akhirMinggu) {
                $query->whereBetween('date', [$awalMinggu->toDateString(), $akhirMinggu->toDateString()]);
            }])
            ->where('user_id', $userId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // 2. SISTEM AUTO-FILL & PENGECEKAN STATUS
        foreach ($schedules as $schedule) {
            $shouldBeAutoFilled = false;
            $scheduleDate = $awalMinggu->copy()->addDays($schedule->day_of_week - 1);

            // Cek apakah jurnal sudah ada (Manual atau Auto)
            $existingJournal = $schedule->journals->first();
            $schedule->is_journal_filled = $existingJournal !== null;
            // Jika ada jurnalnya, cek apakah dia terkunci (Auto-fill)
            $schedule->is_auto_filled = $existingJournal ? $existingJournal->is_locked : false;

            if (!$schedule->is_journal_filled) {
                if ($schedule->day_of_week < $currentDayOfWeek) {
                    $shouldBeAutoFilled = true; 
                } elseif ($schedule->day_of_week == $currentDayOfWeek && $isPast1800) {
                    $shouldBeAutoFilled = true; 
                }
            }

            if ($shouldBeAutoFilled) {
                DB::transaction(function () use ($schedule, $scheduleDate) {
                    $journal = Journal::create([
                        'schedule_id'   => $schedule->id,
                        'date'          => $scheduleDate->toDateString(),
                        'activity_type' => 'Pembelajaran Mandiri', 
                        'description'   => null,
                        'notes'         => null,
                        'is_locked'     => true, // Kunci sebagai penanda Auto-Fill
                    ]);

                    $students = $schedule->classroom->students;
                    $attendances = [];
                    foreach ($students as $student) {
                        $attendances[] = [
                            'journal_id' => $journal->id,
                            'student_id' => $student->id,
                            'status'     => 'hadir',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if (!empty($attendances)) {
                        Attendance::insert($attendances);
                    }
                });
                
                // Update status di memory agar dikenali Vue
                $schedule->is_journal_filled = true; 
                $schedule->is_auto_filled = true; 
            }
        }

        $hariMap = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
            4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
        ];

        // 3. Mapping data untuk Vue (Tambahkan operan is_auto_filled)
        $formattedSchedules = $schedules->map(function ($schedule) use ($hariMap) {
            return [
                'id' => $schedule->id,
                'mataPelajaran' => $schedule->subject->name,
                'kelas' => $schedule->classroom->name,
                'waktuMulai' => Carbon::parse($schedule->start_time)->format('H:i'),
                'waktuSelesai' => Carbon::parse($schedule->end_time)->format('H:i'),
                'hari' => $hariMap[$schedule->day_of_week],
                'hari_angka' => $schedule->day_of_week,
                'is_journal_filled' => $schedule->is_journal_filled,
                'is_auto_filled' => $schedule->is_auto_filled, // --- OPERAN BARU ---
            ];
        });

        return Inertia::render('Dashboard', [
            'jadwalMingguan' => $formattedSchedules, 
            'userName' => Auth::user()->name,
            'userJabatan' => 'Guru Mata Pelajaran', 
        ]);
    }
}