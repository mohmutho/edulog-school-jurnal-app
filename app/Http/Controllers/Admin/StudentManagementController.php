<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class StudentManagementController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $classrooms = Classroom::orderBy('name', 'asc')->get();
        
        $defaultClassroomId = $classrooms->first() ? $classrooms->first()->id : 'unassigned';
        $filterClassroom = $request->input('classroom_id', $defaultClassroomId);
        $search = $request->input('search');

        $query = Student::select('students.*')
            ->where('students.status', 'aktif') 
            ->leftJoin('classroom_student', function($join) use ($activeYear) {
                $join->on('students.id', '=', 'classroom_student.student_id');
                if ($activeYear) {
                    $join->where('classroom_student.academic_year_id', '=', $activeYear->id);
                }
            })
            ->with(['classrooms' => function ($query) use ($activeYear) {
                if ($activeYear) {
                    $query->where('academic_year_id', $activeYear->id);
                }
            }]);

        if ($filterClassroom === 'unassigned') {
            $query->whereNull('classroom_student.classroom_id');
        } else {
            $query->where('classroom_student.classroom_id', $filterClassroom);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('students.name', 'like', "%{$search}%")
                  ->orWhere('students.nisn', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('students.name', 'asc')->get();

        return Inertia::render('Admin/Student/Index', [
            'students' => $students,
            'classrooms' => $classrooms,
            'activeYear' => $activeYear,
            'filters' => [
                'search' => $search,
                'classroom_id' => $filterClassroom
            ]
        ]);
    }

    public function assignClass(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->student_ids as $studentId) {
                // Logika Mutation: Update data lama jika ada, atau Insert jika baru
                DB::table('classroom_student')->updateOrInsert(
                    [
                        'student_id' => $studentId, 
                        'academic_year_id' => $request->academic_year_id
                    ],
                    [
                        'classroom_id' => $request->classroom_id, 
                        'updated_at' => now(),
                    ]
                );
            }
        });

        return redirect()->back();
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'status' => 'required|in:mutasi,keluar',
        ]);

        // Karena model Student sudah menggunakan $guarded = ['id'], mass update ini aman
        Student::whereIn('id', $request->student_ids)->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        Excel::import(
            new StudentsImport($request->academic_year_id), 
            $request->file('file')
        );

        return redirect()->back();
    }

    public function downloadTemplate()
    {
        return Excel::download(new class implements WithHeadings, WithEvents {
            public function headings(): array
            {
                return ['nisn', 'nama_siswa', 'gender', 'kelas'];
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function(AfterSheet $event) {
                        $validation = $event->sheet->getCell('C2')->getDataValidation();
                        $validation->setType(DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                        $validation->setAllowBlank(true);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Input error');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pick from list');
                        $validation->setPrompt('Please pick a value from the drop-down list.');
                        $validation->setFormula1('"L,P"');
                        
                        $event->sheet->setDataValidation('C2:C1000', $validation);
                    },
                ];
            }
        }, 'template_import_siswa.xlsx');
    }
}