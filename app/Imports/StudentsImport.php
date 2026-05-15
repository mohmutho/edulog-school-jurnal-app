<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    protected $academicYearId;

    public function __construct($academicYearId)
    {
        $this->academicYearId = $academicYearId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Abaikan jika nama_siswa kosong
            if (empty($row['nama_siswa'])) {
                continue;
            }

            $namaSiswa = mb_convert_case(trim($row['nama_siswa']), MB_CASE_TITLE, "UTF-8");

            // Menentukan atribut pencarian (berdasarkan nisn, jika kosong gunakan name)
            $matchAttributes = [];
            if (!empty($row['nisn'])) {
                $matchAttributes['nisn'] = $row['nisn'];
            } else {
                $matchAttributes['name'] = $namaSiswa;
            }

            // Logika Siswa
            $student = Student::updateOrCreate(
                $matchAttributes,
                [
                    'name' => $namaSiswa,
                    'gender' => !empty($row['gender']) ? strtoupper($row['gender']) : null,
                    'status' => 'aktif',
                    'nisn' => !empty($row['nisn']) ? $row['nisn'] : null,
                ]
            );

            // Logika Kelas
            if (!empty($row['kelas'])) {
                $classroom = Classroom::firstOrCreate(
                    ['name' => strtoupper($row['kelas'])],
                    ['is_active' => true]
                );

                // Logika Pivot
                if ($this->academicYearId) {
                    DB::table('classroom_student')->updateOrInsert(
                        [
                            'student_id' => $student->id,
                            'academic_year_id' => $this->academicYearId
                        ],
                        [
                            'classroom_id' => $classroom->id,
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
    }
}
