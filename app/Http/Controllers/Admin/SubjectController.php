<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Subject;
use App\Models\User;
use App\Imports\SubjectsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('users:id,name')->latest()->paginate(10);
        $teachers = User::where('role', 'guru')->get(['id', 'name']);

        return Inertia::render('Admin/MasterData/Subject/Index', [
            'subjects' => $subjects,
            'teachers' => $teachers,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $subject = Subject::withTrashed()->where('code', $request->code)->first();

        if ($subject) {
            if (!$subject->trashed()) {
                return back()->withErrors(['code' => 'Kode Mapel ini sudah digunakan dan masih aktif.'])->withInput();
            }
            // Jika terhapus, kita restore dan update
            $subject->restore();
            $subject->update($request->except('code'));
            return back()->with('success', 'Mata Pelajaran yang sebelumnya terhapus berhasil dipulihkan (Restore).');
        }

        Subject::create($request->all());

        return back()->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'code' => 'required|string|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $subject->update($request->all());

        return back()->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        // Membersihkan relasi di tabel pivot subject_user sebelum soft-delete
        $subject->users()->detach();
        
        $subject->delete();

        return back()->with('success', 'Mata Pelajaran berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new SubjectsImport, $request->file('file'));
            return back()->with('success', 'Data Mata Pelajaran berhasil diimpor.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $fileName = 'template_import_mapel.xlsx';

        $response = new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            
            // Generate basic XLSX structure using Maatwebsite Excel or simply export array
            // It's easier to just use Maatwebsite/Excel export inline
        });

        // However, a simpler way is using a quick array export
        return Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function array(): array {
                return [];
            }
            public function headings(): array {
                return ['kode_mapel', 'nama_mapel'];
            }
        }, $fileName);
    }

    public function assignTeachers(Request $request, Subject $subject)
    {
        $request->validate([
            'teacher_ids' => 'array',
            'teacher_ids.*' => 'exists:users,id',
        ]);

        $subject->users()->sync($request->teacher_ids ?? []);

        return back()->with('success', 'Guru pengampu berhasil diperbarui.');
    }
}
