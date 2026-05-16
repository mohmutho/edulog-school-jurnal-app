<?php

namespace App\Imports;

use App\Models\Subject;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectsImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Validasi baris kosong
            if (!isset($row['kode_mapel']) || !isset($row['nama_mapel'])) {
                continue;
            }

            $subject = Subject::withTrashed()->where('code', $row['kode_mapel'])->first();

            if ($subject) {
                // Jika data ternyata pernah dihapus (soft delete), kita kembalikan (restore)
                if ($subject->trashed()) {
                    $subject->restore();
                }
                $subject->update(['name' => $row['nama_mapel']]);
            } else {
                Subject::create([
                    'code' => $row['kode_mapel'],
                    'name' => $row['nama_mapel']
                ]);
            }
        }
    }
}
