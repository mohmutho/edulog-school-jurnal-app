<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Journal;
use App\Models\Attendance;
use Carbon\Carbon;

class JournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cari akun Anda (Pak Mutho)
        $guru = User::where('name', 'like', '%Mutho%')->first();
        
        if (!$guru) {
            echo "Akun guru tidak ditemukan. Pastikan Anda sudah menjalankan DatabaseSeeder utama.\n";
            return;
        }

        // 2. Ambil jadwal Anda beserta siswa di kelas tersebut
        $schedules = Schedule::with('classroom.students')->where('user_id', $guru->id)->get();

        if ($schedules->isEmpty()) {
            echo "Jadwal mengajar kosong. Tambahkan jadwal dulu di DatabaseSeeder utama.\n";
            return;
        }

        // 3. Atur rentang waktu (Mundur 3 bulan: 1 Februari 2026 s/d 30 April 2026)
        $startDate = Carbon::create(2026, 2, 1);
        $endDate = Carbon::create(2026, 4, 30);

        // Beberapa variasi kegiatan agar terlihat realistis
        $kegiatanList = [
            'Penyampaian Materi Dasar',
            'Praktikum di Laboratorium',
            'Latihan Soal dan Diskusi',
            'Presentasi Kelompok',
            'Evaluasi / Ulangan Harian'
        ];

        echo "Mulai men-generate ratusan data jurnal & presensi untuk Pak Mutho...\n";

        foreach ($schedules as $schedule) {
            $currentDate = $startDate->copy();

            // Looping hari demi hari selama 3 bulan
            while ($currentDate <= $endDate) {
                // Jika hari pada tanggal ini SAMA dengan hari jadwal Anda (1 = Senin, 2 = Selasa, dst)
                if ($currentDate->format('N') == $schedule->day_of_week) {
                    
                    // Buat Jurnal untuk hari itu
                    $journal = Journal::create([
                        'schedule_id'   => $schedule->id,
                        'date'          => $currentDate->toDateString(),
                        'activity_type' => $kegiatanList[array_rand($kegiatanList)],
                        'description'   => 'Membahas topik Informatika sesuai silabus minggu ke-' . $currentDate->weekOfMonth,
                        'notes'         => 'Pembelajaran berjalan lancar',
                        'is_locked'     => false,
                    ]);

                    // Buat Presensi untuk seluruh siswa di kelas tersebut
                    $attendances = [];
                    foreach ($schedule->classroom->students as $student) {
                        // Logika random: 90% hadir, sisanya sakit/izin/alpa agar data terlihat alami
                        $rand = rand(1, 100);
                        $status = 'hadir';
                        if ($rand > 90 && $rand <= 95) $status = 'sakit';
                        elseif ($rand > 95 && $rand <= 98) $status = 'izin';
                        elseif ($rand > 98) $status = 'alpha';

                        $attendances[] = [
                            'journal_id' => $journal->id,
                            'student_id' => $student->id,
                            'status'     => $status,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    // Insert massal agar proses seeding cepat
                    Attendance::insert($attendances);
                }
                // Lanjut ke hari berikutnya
                $currentDate->addDay();
            }
        }

        echo "Selesai! Data jurnal dari Februari - April 2026 berhasil ditambahkan.\n";
    }
}
