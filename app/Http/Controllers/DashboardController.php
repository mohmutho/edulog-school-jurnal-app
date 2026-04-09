<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $now = Carbon::now();
        
        $awalMinggu = $now->copy()->startOfWeek(Carbon::MONDAY);
        $akhirMinggu = $now->copy()->endOfWeek(Carbon::SUNDAY);

        // 1. Ambil jadwal beserta jurnalnya (hanya di rentang minggu ini)
        $schedules = Schedule::with(['classroom', 'subject', 'journals' => function ($query) use ($awalMinggu, $akhirMinggu) {
                $query->whereBetween('date', [$awalMinggu->toDateString(), $akhirMinggu->toDateString()]);
            }])
            ->where('user_id', $userId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $hariMap = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
            4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
        ];

        // 2. Mapping data mentah untuk dikonsumsi Vue
        $formattedSchedules = $schedules->map(function ($schedule) use ($hariMap) {
            return [
                'id' => $schedule->id,
                'mataPelajaran' => $schedule->subject->name,
                'kelas' => $schedule->classroom->name,
                'waktuMulai' => Carbon::parse($schedule->start_time)->format('H:i'),
                'waktuSelesai' => Carbon::parse($schedule->end_time)->format('H:i'),
                'hari' => $hariMap[$schedule->day_of_week],
                'hari_angka' => $schedule->day_of_week,
                'is_journal_filled' => $schedule->journals->isNotEmpty(),
                // is_auto_filled kita hapus karena sudah tidak ada logika auto-fill di sini
            ];
        });

        return Inertia::render('Dashboard', [
            'jadwalMingguan' => $formattedSchedules, 
            'userName' => Auth::user()->name,
            'userJabatan' => 'Guru Informatika', // Disesuaikan dengan role Anda
        ]);
    }
}