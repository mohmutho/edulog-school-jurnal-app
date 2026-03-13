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
        // 1. Ambil ID guru yang sedang login
        $userId = Auth::id(); 

        // 2. Ambil hari ini dalam format angka (1 = Senin, ..., 7 = Minggu)
        // Catatan: Karena hari ini Kamis (4), dan seeder kita Senin (1) & Selasa (2), 
        // untuk sementara waktu kita ambil SEMUA jadwal guru ini agar UI Vue agan tidak kosong.
        // Nanti kalau sudah masuk production, kita tinggal tambahkan: ->where('day_of_week', Carbon::now()->dayOfWeekIso)
        
        $schedules = Schedule::with(['classroom', 'subject']) // Eager loading relasi
            ->where('user_id', $userId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // 3. Mapping data dari Database agar formatnya sesuai dengan yang dibaca oleh UI Vue agan
        $formattedSchedules = $schedules->map(function ($schedule) {
            $hariMap = [
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
                7 => 'Minggu'
            ];

            return [
                'id' => $schedule->id,
                'mataPelajaran' => $schedule->subject->name,
                'kelas' => $schedule->classroom->name,
                'waktuMulai' => Carbon::parse($schedule->start_time)->format('H:i'),
                'waktuSelesai' => Carbon::parse($schedule->end_time)->format('H:i'),
                'hari' => $hariMap[$schedule->day_of_week],
            ];
        });

        // 4. Kirim data ke Frontend (Inertia)
        return Inertia::render('Dashboard', [
            'jadwalHariIni' => $formattedSchedules, // Ini props yang ditangkap oleh Vue kemarin
            'userName' => Auth::user()->name,
            'userJabatan' => 'Guru Mata Pelajaran', // Nanti bisa dinamis
        ]);
    }
}
