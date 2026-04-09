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
use Barryvdh\DomPDF\Facade\Pdf;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil semua jurnal milik guru yang login, urutkan dari yang terbaru
        // Kita tambahkan relasi 'attendances' agar bisa menghitung jumlah siswa hadir
        $journalsRaw = Journal::with(['schedule.subject', 'schedule.classroom', 'attendances'])
            ->whereHas('schedule', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // 2. Kelompokkan berdasarkan Bulan & Tahun menggunakan Carbon
        $groupedJournals = $journalsRaw->groupBy(function ($journal) {
            return Carbon::parse($journal->date)->translatedFormat('F Y');
        });

        // 3. Hitung rekap kehadiran untuk ditampilkan di tabel
        $groupedJournals->transform(function ($monthGroup) {
            return $monthGroup->map(function ($journal) {
                // Berapa yang hadir?
                $journal->hadir_count = $journal->attendances->where('status', 'hadir')->count();
                
                // Berapa total siswa di kelas tersebut? (Total baris presensi)
                $journal->total_students = $journal->attendances->count();
                
                // Berapa yang tidak hadir? (Total - Hadir)
                $journal->tidak_hadir_count = $journal->total_students - $journal->hadir_count;
                
                return $journal;
            });
        });

        // 4. Lempar data (yang kini berbentuk Object per bulan) ke Vue
        return Inertia::render('Journal/Index', [
            'journals' => $groupedJournals,
        ]);
    }

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

    public function edit(Journal $journal)
    {
        // 1. Load relasi jadwal untuk pengecekan keamanan
        $journal->load(['schedule.subject', 'schedule.classroom']);
        
        // 2. Keamanan: Pastikan jurnal ini milik guru yang sedang login
        if ($journal->schedule->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki akses untuk mengedit jurnal ini.');
        }

        // 3. LOGIKA GEMBOK MINGGUAN: Pastikan jurnal berada di minggu yang sedang berjalan
        $awalMinggu = now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $akhirMinggu = now()->endOfWeek(Carbon::SUNDAY)->toDateString();

        if ($journal->date < $awalMinggu || $journal->date > $akhirMinggu) {
            return redirect()->route('journal.show', $journal->schedule_id)
                ->with('error', 'Masa edit untuk jurnal ini sudah ditutup karena telah berganti minggu.');
        }

        // 4. Load data presensi dan urutkan nama siswa sesuai abjad
        $journal->load(['attendances.student' => function ($query) {
            $query->orderBy('name', 'asc');
        }]);

        // 5. Lempar data ke halaman Edit.vue
        return Inertia::render('Journal/Edit', [
            'jadwal' => $journal->schedule,
            'jurnal' => $journal,
            'presensi' => $journal->attendances
        ]);
    }

    public function update(Request $request, Journal $journal)
    {
        // 1. Validasi Keamanan Lapis Dua (Mencegah bypass via API/Postman)
        $journal->load('schedule');
        if ($journal->schedule->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak.');
        }

        $awalMinggu = now()->startOfWeek(Carbon::MONDAY)->toDateString();
        if ($journal->date < $awalMinggu) {
            abort(403, 'Jurnal ini sudah terkunci secara permanen.');
        }

        // 2. Validasi Input dari Form
        $validated = $request->validate([
            'activity_type' => 'required|string|max:255',
            'description'   => 'nullable|string',
            'notes'         => 'nullable|string',
            'attendances'   => 'required|array',
            'attendances.*' => 'required|in:hadir,izin,sakit,alpa',
        ]);

        // 3. Proses Update dengan Database Transaction
        DB::transaction(function () use ($validated, $journal) {
            // Update Jurnal & BUKA GEMBOK (is_locked menjadi false)
            // Karena data ini sekarang adalah hasil koreksi valid dari guru, bukan lagi auto-fill
            $journal->update([
                'activity_type' => $validated['activity_type'],
                'description'   => $validated['description'],
                'notes'         => $validated['notes'],
                'is_locked'     => false, 
            ]);

            // Update status presensi siswa
            foreach ($validated['attendances'] as $studentId => $status) {
                Attendance::where('journal_id', $journal->id)
                    ->where('student_id', $studentId)
                    ->update(['status' => $status]);
            }
        });

        // 4. Kembalikan ke halaman Lihat Presensi
        return redirect()->route('journal.show', $journal->schedule_id)
            ->with('success', 'Jurnal dan Presensi berhasil diperbarui!');
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // 1. Ambil Data Jurnal Berdasarkan Rentang Tanggal
        $journalsRaw = Journal::with(['schedule.subject', 'schedule.classroom', 'attendances.student'])
            ->whereHas('schedule', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc') // Urutkan dari tanggal paling lama ke baru
            ->orderBy('schedule_id', 'asc')
            ->get();

        // 2. Kelompokkan Data Berdasarkan Tanggal (Untuk Pemisah Hari di PDF)
        $groupedJournals = $journalsRaw->groupBy('date');

        // 3. Render ke PDF dengan format Landscape
        $pdf = Pdf::loadView('pdf.journal-export', [
            'user' => $user,
            'groupedJournals' => $groupedJournals,
            'startDate' => Carbon::parse($startDate)->translatedFormat('d F Y'),
            'endDate' => Carbon::parse($endDate)->translatedFormat('d F Y')
        ])->setPaper('A4', 'landscape');

        // 4. Download File
        $fileName = 'Jurnal_Guru_' . str_replace(' ', '_', $user->name) . '_' . $startDate . '_sd_' . $endDate . '.pdf';
        return $pdf->download($fileName);
    }
}