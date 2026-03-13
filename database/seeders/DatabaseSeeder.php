<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\Schedule;

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
            'name' => 'Mohammad Mutho',
            'email' => 'mutho@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'L',
        ]);
        $guru2 = User::create([
            'name' => 'Nur Sholihin',
            'email' => 'sholihin@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'L',
        ]);
        $guru3 = User::create([
            'name' => 'Siti Rumiyati',
            'email' => 'rumiyati@sekolah.com', // Gunakan email ini untuk login Vue nanti
            'password' => Hash::make('password'), // Password default: password
            'role' => 'guru',
            'gender' => 'P',
        ]);

        // 3. MASTER DATA: Kelas
        $kelas1 = Classroom::create(['name' => 'X-1']);
        $kelas2 = Classroom::create(['name' => 'X-2']);
        $kelas3 = Classroom::create(['name' => 'X-3']);
        $kelas4 = Classroom::create(['name' => 'XI-1']);
        $kelas5 = Classroom::create(['name' => 'XI-2']);
        $kelas6 = Classroom::create(['name' => 'XI-3']);
        $kelas7 = Classroom::create(['name' => 'XII-1']);
        $kelas8 = Classroom::create(['name' => 'XII-2']);
        $kelas9 = Classroom::create(['name' => 'XII-3']);

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
        $mapel11 = Subject::create(['name' => 'Pendidikan Jasmani']);
        $mapel12 = Subject::create(['name' => 'Pendidikan Agama']);

        // Jadwal 1: Hari Senin
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru1->id,
            'classroom_id' => $kelas1->id,
            'subject_id' => $mapel1->id,
            'day_of_week' => 1, // 1 = Senin
            'start_time' => '07:00:00',
            'end_time' => '09:30:00',
        ]);

        // Jadwal 1: Hari Senin
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru2->id,
            'classroom_id' => $kelas1->id,
            'subject_id' => $mapel4->id,
            'day_of_week' => 1, // 1 = Senin
            'start_time' => '10:00:00',
            'end_time' => '12:30:00',
        ]);

        // Jadwal 2: Hari Selasa
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru3->id,
            'classroom_id' => $kelas2->id,
            'subject_id' => $mapel7->id,
            'day_of_week' => 2, // 2 = Selasa
            'start_time' => '07:00:00',
            'end_time' => '09:30:00',
        ]);

        // Jadwal 2: Hari Selasa
        Schedule::create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $guru1->id,
            'classroom_id' => $kelas2->id,
            'subject_id' => $mapel2->id,
            'day_of_week' => 3, // 3 = Rabu
            'start_time' => '10:00:00',
            'end_time' => '12:30:00',
        ]);
    }
}
