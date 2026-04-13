<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AcademicYearController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/AcademicYear/Index', [
            'academicYears' => AcademicYear::orderBy('name', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:20', // Contoh: 2025/2026
            'semester' => 'required|in:ganjil,genap',
        ]);

        AcademicYear::create($validated);

        return redirect()->back();
    }

    public function activate($id)
    {
        // 1. Matikan semua tahun ajaran yang sedang aktif
        AcademicYear::where('is_active', true)->update(['is_active' => false]);

        // 2. Aktifkan tahun ajaran yang dipilih
        $year = AcademicYear::findOrFail($id);
        $year->update(['is_active' => true]);

        return redirect()->back();
    }
}