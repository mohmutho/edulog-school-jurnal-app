<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Journal;
use App\Models\Attendance;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. MASTER DATA: Tahun Ajaran
        $academicYear = AcademicYear::create([
            'name' => '2025/2026',
            'semester' => 'genap',
            'is_active' => true,
        ]);

        // 2. MASTER DATA: Akun Guru Utama (Untuk agan login nanti)
        $guru1 = User::create([
            'name' => 'Mohammad Mutho, S.T.',
            'email' => 'mutho@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'L',
            'whatsapp_number' => '085875824963',
            'email_verified_at' => now(),
        ]);
        $guru2 = User::create([
            'name' => 'Nur Sholihin, S.Kom., M.Si.',
            'email' => 'sholihin@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'L',
            'whatsapp_number' => '082136866556',
            'email_verified_at' => now(),
        ]);
        $guru3 = User::create([
            'name' => 'Rumiyati, M.Pd',
            'email' => 'rumiyati@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'P',
            'whatsapp_number' => '08122579635',
            'email_verified_at' => now(),
        ]);
        $guru4 = User::create([
            'name' => 'Fitria Meilina Kartikasari, S.Pd',
            'email' => 'sari@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'P',
            'email_verified_at' => now(),
        ]);
        $guru5 = User::create([
            'name' => 'Sutiyono, M.Pd',
            'email' => 'sutiyono@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'L',
            'email_verified_at' => now(),
        ]);
        $guru6 = User::create([
            'name' => 'Rini Rakhmawati, M.Pd',
            'email' => 'rini@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'P',
            'email_verified_at' => now(),
        ]);
        $guru7 = User::create([
            'name' => 'Madichah, S.Pd',
            'email' => 'madichah@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'P',
            'email_verified_at' => now(),
        ]);
        $guru8 = User::create([
            'name' => 'Dwi Aris Sutiyono, S.Pd',
            'email' => 'aris@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'L',
            'email_verified_at' => now(),
        ]);
        $guru9 = User::create([
            'name' => 'M. Fakhrudin F, S.Pd',
            'email' => 'fakhrudin@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'L',
            'email_verified_at' => now(),
        ]);
        $guru10 = User::create([
            'name' => 'Ariyati, S.Pd',
            'email' => 'ari@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'P',
            'email_verified_at' => now(),
        ]);
        $guru11 = User::create([
            'name' => 'Endang Sri Lestari, S.Pd., M.Pd.',
            'email' => 'endang@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'P',
            'email_verified_at' => now(),
        ]);

        // 3. MASTER DATA: Akun Admin Kurikulum
        $adminKurikulum = User::create([
            'name' => 'Bapak Admin Kurikulum, M.Pd.',
            'email' => 'kurikulum@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'admin_kurikulum',
            'gender' => 'L',
            'email_verified_at' => now(),
        ]);

        // 4. MASTER DATA: Kelas
        $kelas1 = Classroom::create(['name' => 'X-1']);
        $kelas2 = Classroom::create(['name' => 'X-2']);
        $kelas3 = Classroom::create(['name' => 'X-3']);
        $kelas4 = Classroom::create(['name' => 'X-4']);
        $kelas5 = Classroom::create(['name' => 'X-5']);
        $kelas6 = Classroom::create(['name' => 'XI-1']);
        $kelas7 = Classroom::create(['name' => 'XI-2']);
        $kelas8 = Classroom::create(['name' => 'XI-3']);
        $kelas9 = Classroom::create(['name' => 'XI-4']);
        $kelas10 = Classroom::create(['name' => 'XI-5']);
        $kelas11 = Classroom::create(['name' => 'XII-1']);
        $kelas12 = Classroom::create(['name' => 'XII-2']);
        $kelas13 = Classroom::create(['name' => 'XII-3']);
        $kelas14 = Classroom::create(['name' => 'XII-4']);
        $kelas15 = Classroom::create(['name' => 'XII-5']);

        // 4. MASTER DATA: Mata Pelajaran
        $mapel1 = Subject::create(['name' => 'Matematika']);
        $mapel2 = Subject::create(['name' => 'Informatika']);
        $mapel3 = Subject::create(['name' => 'Bahasa Indonesia']);
        $mapel4 = Subject::create(['name' => 'Bahasa Inggris']);
        $mapel5 = Subject::create(['name' => 'Fisika']);
        $mapel6 = Subject::create(['name' => 'Kimia']);
        $mapel7 = Subject::create(['name' => 'Biologi']);
        $mapel8 = Subject::create(['name' => 'Sejarah']);
        $mapel9 = Subject::create(['name' => 'Geografi']);
        $mapel10 = Subject::create(['name' => 'Seni Budaya']);
        $mapel11 = Subject::create(['name' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan']);
        $mapel12 = Subject::create(['name' => 'Pendidikan Agama']);
        $mapel13 = Subject::create(['name' => 'Pendidikan Pancasila dan Kewarganegaraan']);
        $mapel14 = Subject::create(['name' => 'Ekonomi']);
        $mapel15 = Subject::create(['name' => 'Sosiologi']);
        $mapel16 = Subject::create(['name' => 'Prakarya dan Kewirausahaan']);
        $mapel17 = Subject::create(['name' => 'Bahasa Jawa']);
        $mapel18 = Subject::create(['name' => 'Bahasa Inggris Tingkat Lanjut']);
        $mapel19 = Subject::create(['name' => 'Matematika Tingkat Lanjut']);
        $mapel20 = Subject::create(['name' => 'Bimbingan Konseling']);
        $mapel21 = Subject::create(['name' => 'Koding dan Kecerdasan Artificial']);

        // 5. MASTER DATA: Jadwal Pelajaran
        // Jadwal 1: Hari Senin
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru6->id,
            'classroom_id' => $kelas1->id,
            'subject_id' => $mapel1->id,
            'day_of_week' => 1,
            'start_time' => '07:00:00',
            'end_time' => '09:15:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru1->id,
            'classroom_id' => $kelas2->id,
            'subject_id' => $mapel2->id,
            'day_of_week' => 1, 
            'start_time' => '12:30:00',
            'end_time' => '14:00:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru7->id,
            'classroom_id' => $kelas3->id,
            'subject_id' => $mapel14->id,
            'day_of_week' => 1, 
            'start_time' => '09:15:00',
            'end_time' => '11:45:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru4->id,
            'classroom_id' => $kelas4->id,
            'subject_id' => $mapel16->id,
            'day_of_week' => 1, 
            'start_time' => '12:30:00',
            'end_time' => '14:00:00',
        ]);

        // Jadwal 2: Hari Selasa
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru5->id,
            'classroom_id' => $kelas5->id,
            'subject_id' => $mapel3->id,
            'day_of_week' => 2,
            'start_time' => '07:00:00',
            'end_time' => '09:15:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru4->id,
            'classroom_id' => $kelas6->id,
            'subject_id' => $mapel6->id,
            'day_of_week' => 2,
            'start_time' => '10:00:00',
            'end_time' => '11:45:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru2->id,
            'classroom_id' => $kelas7->id,
            'subject_id' => $mapel21->id,
            'day_of_week' => 2,
            'start_time' => '12:30:00',
            'end_time' => '14:00:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru8->id,
            'classroom_id' => $kelas8->id,
            'subject_id' => $mapel20->id,
            'day_of_week' => 2,
            'start_time' => '14:00:00',
            'end_time' => '15:30:00',
        ]);

        // Jadwal 3: Hari Rabu
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru10->id,
            'classroom_id' => $kelas9->id,
            'subject_id' => $mapel18->id,
            'day_of_week' => 3,
            'start_time' => '07:00:00',
            'end_time' => '09:15:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru3->id,
            'classroom_id' => $kelas10->id,
            'subject_id' => $mapel7->id,
            'day_of_week' => 3,
            'start_time' => '09:15:00',
            'end_time' => '11:45:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru9->id,
            'classroom_id' => $kelas11->id,
            'subject_id' => $mapel11->id,
            'day_of_week' => 3,
            'start_time' => '12:30:00',
            'end_time' => '14:00:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru9->id,
            'classroom_id' => $kelas12->id,
            'subject_id' => $mapel11->id,
            'day_of_week' => 3,
            'start_time' => '14:00:00',
            'end_time' => '15:30:00',
        ]);

        // Jadwal 4: Hari Kamis 
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru8->id,
            'classroom_id' => $kelas13->id,
            'subject_id' => $mapel20->id,
            'day_of_week' => 4,
            'start_time' => '07:00:00',
            'end_time' => '08:30:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru1->id,
            'classroom_id' => $kelas14->id,
            'subject_id' => $mapel2->id,
            'day_of_week' => 4,
            'start_time' => '08:30:00',
            'end_time' => '10:00:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru7->id,
            'classroom_id' => $kelas15->id,
            'subject_id' => $mapel15->id,
            'day_of_week' => 4,
            'start_time' => '10:15:00',
            'end_time' => '11:45:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru10->id,
            'classroom_id' => $kelas1->id,
            'subject_id' => $mapel4->id,
            'day_of_week' => 4,
            'start_time' => '12:30:00',
            'end_time' => '14:00:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru6->id,
            'classroom_id' => $kelas2->id,
            'subject_id' => $mapel19->id,
            'day_of_week' => 4,
            'start_time' => '14:00:00',
            'end_time' => '15:30:00',
        ]);

        // Jadwal 5: Hari Jumat 
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru8->id,
            'classroom_id' => $kelas3->id,
            'subject_id' => $mapel20->id,
            'day_of_week' => 5,
            'start_time' => '07:00:00',
            'end_time' => '08:30:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru3->id,
            'classroom_id' => $kelas4->id,
            'subject_id' => $mapel7->id,
            'day_of_week' => 5,
            'start_time' => '08:30:00',
            'end_time' => '10:00:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru11->id,
            'classroom_id' => $kelas5->id,
            'subject_id' => $mapel5->id,
            'day_of_week' => 5,
            'start_time' => '10:15:00',
            'end_time' => '11:45:00',
        ]);
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru11->id,
            'classroom_id' => $kelas2->id,
            'subject_id' => $mapel5->id,
            'day_of_week' => 5,
            'start_time' => '12:30:00',
            'end_time' => '14:00:00',
        ]);

        // 6. MASTER DATA: Siswa
        $siswa1 = Student::create([
            'name' => 'Budi Santoso',
            'nisn' => '1001',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa2 = Student::create([
            'name' => 'Siti Aminah',
            'nisn' => '1002',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa3 = Student::create([
            'name' => 'Ahmad Fauzi',
            'nisn' => '1003',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa4 = Student::create([
            'name' => 'Dewi Lestari',
            'nisn' => '1004',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa5 = Student::create([
            'name' => 'Rina Wijaya',
            'nisn' => '1005',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa6 = Student::create([
            'name' => 'Andi Pratama',
            'nisn' => '1006',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa7 = Student::create([
            'name' => 'Fitri Handayani',
            'nisn' => '1007',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa8 = Student::create([
            'name' => 'Rizky Ramadhan',
            'nisn' => '1008',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa9 = Student::create([
            'name' => 'Maya Sari',
            'nisn' => '1009',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa10 = Student::create([
            'name' => 'Agus Setiawan',
            'nisn' => '1010',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa11 = Student::create([
            'name' => 'Rahmat Hidayat',
            'nisn' => '1011',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa12 = Student::create([
            'name' => 'Sari Puspita',
            'nisn' => '1012',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa13 = Student::create([
            'name' => 'Eko Prasetyo',
            'nisn' => '1013',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa14 = Student::create([
            'name' => 'Lina Marlina',
            'nisn' => '1014',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa15 = Student::create([
            'name' => 'Dedi Kurniawan',
            'nisn' => '1015',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa16 = Student::create([
            'name' => 'Yulia Sari',
            'nisn' => '1016',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa17 = Student::create([
            'name' => 'Hendra Gunawan',
            'nisn' => '1017',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa18 = Student::create([
            'name' => 'Nina Kurnia',
            'nisn' => '1018',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa19 = Student::create([
            'name' => 'Slamet Riyadi',
            'nisn' => '1019',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa20 = Student::create([
            'name' => 'Dina Permata',
            'nisn' => '1020',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa21 = Student::create([
            'name' => 'Bambang Sutrisno',
            'nisn' => '1021',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa22 = Student::create([
            'name' => 'Siti Nurhalima',
            'nisn' => '1022',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa23 = Student::create([
            'name' => 'Agus Salim',
            'nisn' => '1023',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa24 = Student::create([
            'name' => 'Rina Marlina',
            'nisn' => '1024',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa25 = Student::create([
            'name' => 'Dedi Prasetyo',
            'nisn' => '1025',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa26 = Student::create([
            'name' => 'Yulia Handayani',
            'nisn' => '1026',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa27 = Student::create([
            'name' => 'Hendra Ramadhan',
            'nisn' => '1027',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa28 = Student::create([
            'name' => 'Nina Sari',
            'nisn' => '1028',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa29 = Student::create([
            'name' => 'Slamet Hidayat',
            'nisn' => '1029',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa30 = Student::create([
            'name' => 'Dina Kurnia',
            'nisn' => '1030',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa31 = Student::create([
            'name' => 'Bambang Permata',
            'nisn' => '1031',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa32 = Student::create([
            'name' => 'Siti Nurhalimah',
            'nisn' => '1032',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa33 = Student::create([
            'name' => 'Agus Salim',
            'nisn' => '1033',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa34 = Student::create([
            'name' => 'Rina Marlina',
            'nisn' => '1034',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa35 = Student::create([
            'name' => 'Dedi Prasetyo',
            'nisn' => '1035',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa36 = Student::create([
            'name' => 'Yulia Handayani',
            'nisn' => '1036',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa37 = Student::create([
            'name' => 'Hendra Ramadhan',
            'nisn' => '1037',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa38 = Student::create([
            'name' => 'Nina Sari',
            'nisn' => '1038',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa39 = Student::create([
            'name' => 'Slamet Hidayat',
            'nisn' => '1039',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa40 = Student::create([
            'name' => 'Dina Kurnia',
            'nisn' => '1040',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa41 = Student::create([
            'name' => 'Bambang Permata',
            'nisn' => '1041',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa42 = Student::create([
            'name' => 'Siti Nurhalimah',
            'nisn' => '1042',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa43 = Student::create([
            'name' => 'Agus Salim',
            'nisn' => '1043',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa44 = Student::create([
            'name' => 'Rina Marlina',
            'nisn' => '1044',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa45 = Student::create([
            'name' => 'Dedi Prasetyo',
            'nisn' => '1045',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa46 = Student::create([
            'name' => 'Yulia Handayani',
            'nisn' => '1046',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa47 = Student::create([
            'name' => 'Hendra Ramadhan',
            'nisn' => '1047',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa48 = Student::create([
            'name' => 'Nina Sari',
            'nisn' => '1048',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa49 = Student::create([
            'name' => 'Slamet Hidayat',
            'nisn' => '1049',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa50 = Student::create([
            'name' => 'Dina Kurnia',
            'nisn' => '1050',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa51 = Student::create([
            'name' => 'Bambang Permata',
            'nisn' => '1051',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa52 = Student::create([
            'name' => 'Siti Nurhalimah',
            'nisn' => '1052',
            'gender' => 'P',
            'status' => 'aktif'
        ]);
        $siswa53 = Student::create([
            'name' => 'Agus Salim',
            'nisn' => '1053',
            'gender' => 'L',
            'status' => 'aktif'
        ]);
        $siswa54 = Student::create([
            'name' => 'Rina Marlina',
            'nisn' => '1054',
            'gender' => 'P',
            'status' => 'aktif'
        ]);

        // 7. TRANSAKSI PIVOT: Memasukkan Siswa ke Kelas (Tabel classroom_student)
        $kelas1->students()->attach([
            $siswa1->id => ['academic_year_id' => $academicYear->id],
            $siswa2->id => ['academic_year_id' => $academicYear->id],
            $siswa3->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas2->students()->attach([
            $siswa4->id => ['academic_year_id' => $academicYear->id],
            $siswa5->id => ['academic_year_id' => $academicYear->id],
            $siswa6->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas3->students()->attach([
            $siswa7->id => ['academic_year_id' => $academicYear->id],
            $siswa8->id => ['academic_year_id' => $academicYear->id],
            $siswa9->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas4->students()->attach([
            $siswa10->id => ['academic_year_id' => $academicYear->id],
            $siswa11->id => ['academic_year_id' => $academicYear->id],
            $siswa12->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas5->students()->attach([
            $siswa13->id => ['academic_year_id' => $academicYear->id],
            $siswa14->id => ['academic_year_id' => $academicYear->id],
            $siswa15->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas6->students()->attach([
            $siswa16->id => ['academic_year_id' => $academicYear->id],
            $siswa17->id => ['academic_year_id' => $academicYear->id],
            $siswa18->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas7->students()->attach([
            $siswa19->id => ['academic_year_id' => $academicYear->id],
            $siswa20->id => ['academic_year_id' => $academicYear->id],
            $siswa21->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas8->students()->attach([
            $siswa22->id => ['academic_year_id' => $academicYear->id],
            $siswa23->id => ['academic_year_id' => $academicYear->id],
            $siswa24->id => ['academic_year_id' => $academicYear->id],
            $siswa25->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas9->students()->attach([
            $siswa26->id => ['academic_year_id' => $academicYear->id],
            $siswa27->id => ['academic_year_id' => $academicYear->id],
            $siswa28->id => ['academic_year_id' => $academicYear->id],
            $siswa29->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas10->students()->attach([
            $siswa30->id => ['academic_year_id' => $academicYear->id],
            $siswa31->id => ['academic_year_id' => $academicYear->id],
            $siswa32->id => ['academic_year_id' => $academicYear->id],
            $siswa33->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas11->students()->attach([
            $siswa34->id => ['academic_year_id' => $academicYear->id],
            $siswa35->id => ['academic_year_id' => $academicYear->id],
            $siswa36->id => ['academic_year_id' => $academicYear->id],
            $siswa37->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas12->students()->attach([
            $siswa38->id => ['academic_year_id' => $academicYear->id],
            $siswa39->id => ['academic_year_id' => $academicYear->id],
            $siswa40->id => ['academic_year_id' => $academicYear->id],
            $siswa41->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas13->students()->attach([
            $siswa42->id => ['academic_year_id' => $academicYear->id],
            $siswa43->id => ['academic_year_id' => $academicYear->id],
            $siswa44->id => ['academic_year_id' => $academicYear->id],
            $siswa45->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas14->students()->attach([
            $siswa46->id => ['academic_year_id' => $academicYear->id],
            $siswa47->id => ['academic_year_id' => $academicYear->id],
            $siswa48->id => ['academic_year_id' => $academicYear->id],
            $siswa49->id => ['academic_year_id' => $academicYear->id],
        ]);
        $kelas15->students()->attach([ 
            $siswa50->id => ['academic_year_id' => $academicYear->id],
            $siswa51->id => ['academic_year_id' => $academicYear->id],
            $siswa52->id => ['academic_year_id' => $academicYear->id],
            $siswa53->id => ['academic_year_id' => $academicYear->id],
            $siswa54->id => ['academic_year_id' => $academicYear->id],
        ]);
        
    }
}
