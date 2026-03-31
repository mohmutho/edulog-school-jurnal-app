<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Schedule;
use App\Models\Journal;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class JournalController extends Controller
{
    public function create(Schedule $schedule)
    {
        // 1. Pastikan jadwal yang diminta milik guru yang sedang login
        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki akses ke jadwal ini.');
        }

        $schedule->load(['subject', 'classroom', 'academicYear']);

        $students = $schedule->classroom->students()
            ->wherePivot('academic_year_id', $schedule->academic_year_id)
            ->where('status', 'aktif')
            ->orderBy('name', 'asc')
            ->get();
        
        // return $students; // Debug: Pastikan data siswa muncul di log

        return Inertia::render('Journal/Create', [
            'jadwal' => $schedule,
            'siswa' => $students
        ]);
    }

    public function store(Request $request, Schedule $schedule)
    {

        // 1. Pastikan jadwal yang diminta milik guru yang sedang login
        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki akses ke jadwal ini.');
        }

        // Validasi input
        $validated = $request->validate([
            'kegiatan'    => 'required|string',
            'materi'      => 'nullable|string',
            'catatan'     => 'nullable|string',
            'attendances' => 'required|array',
        ]);

        try{
            DB::transaction(function () use ($validated, $schedule) {
                // A. Simpan ke tabel jurnal
                $journal = Journal::create([
                    'schedule_id'   => $schedule->id,
                    'date'          => now()->toDateString(),
                    'activity_type' => $validated['kegiatan'],
                    'description'   => $validated['materi'],
                    'notes'         => $validated['catatan'],
                    'is_locked'     => false, // Default belum dikunci
                ]);

                // B. Siapkan array presensi untuk insert massal (agar query lebih cepat)
                $attendanceData = [];
                foreach ($validated['attendances'] as $studentId => $status) {
                    $attendanceData[] = [
                        'journal_id' => $journal->id,
                        'student_id' => $studentId,
                        'status'     => $status,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // C. Insert data presensi secara massal
                Attendance::insert($attendanceData);
            });

            return redirect()->route('dashboard')->with('success', 'Jurnal dan Presensi Kelas berhasil disimpan!');
        } catch (\Exception $e) {
            // 5. GAGAL: Kembalikan ke form dengan pesan error
            Log::error('Gagal menyimpan jurnal: ' . $e->getMessage(), [
                'schedule_id' => $schedule->id,
                'user_id' => Auth::id(),
            ]);
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function show(Schedule $schedule)
    {
        // 1. Keamanan: Pastikan jadwal milik guru yang login
        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki akses ke jadwal ini.');
        }

        // 2. Load data relasi jadwal (Mata Pelajaran & Kelas)
        $schedule->load(['subject', 'classroom']);

        // 3. Ambil Jurnal HARI INI beserta data absensi dan detail siswanya
        $awalMinggu = now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $akhirMinggu = now()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $journal = Journal::with(['attendances.student' => function ($query) {
                $query->orderBy('name', 'asc');
            }])
            ->where('schedule_id', $schedule->id)
            ->whereBetween('date', [$awalMinggu, $akhirMinggu]) // Gunakan whereBetween, bukan whereDate
            ->firstOrFail();

        // 4. Lempar data ke Vue
        return Inertia::render('Journal/Show', [
            'jadwal' => $schedule,
            'jurnal' => $journal,
            'presensi' => $journal->attendances
        ]);
    }
}