<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class TeachingScheduleController extends Controller
{
    public function index(Request $request)
    {
        $searchDay = $request->input('search_day');
        $searchClass = $request->input('search_class');

        $hariMap = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
            4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
        ];

        $searchResults = [];
        
        // Hanya lakukan pencarian jika user sudah memilih filter
        if ($searchDay || $searchClass) {
            $searchResults = Schedule::with(['user', 'subject', 'classroom'])
                ->when($searchDay, function ($query, $searchDay) {
                    $query->where('day_of_week', $searchDay);
                })
                ->when($searchClass, function ($query, $searchClass) {
                    $query->where('classroom_id', $searchClass);
                })
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
                ->map(function ($sch) use ($hariMap) {
                    $sch->day_name = $hariMap[$sch->day_of_week];
                    // Format jam agar lebih rapi (HH:MM - HH:MM)
                    $sch->formatted_time = substr($sch->start_time, 0, 5) . ' - ' . substr($sch->end_time, 0, 5);
                    return $sch;
                });
        }

        $classrooms = Classroom::orderBy('name')->get();

        return Inertia::render('TeachingSchedule/Index', [
            'searchResults' => $searchResults,
            'classrooms' => $classrooms,
            'filters' => [
                'search_day' => $searchDay,
                'search_class' => $searchClass,
            ]
        ]);
    }

    public function exportPdf()
    {
        $user = Auth::user();
        
        $hariMap = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat'];
        
        // Mapping persis 1-10 sesuai standar sekolah
        $periods = [
            1 => ['start' => '07:00', 'end' => '07:45'],
            2 => ['start' => '07:45', 'end' => '08:30'],
            3 => ['start' => '08:30', 'end' => '09:15'],
            4 => ['start' => '09:15', 'end' => '10:00'],
            5 => ['start' => '10:15', 'end' => '11:00'],
            6 => ['start' => '11:00', 'end' => '11:45'],
            7 => ['start' => '12:30', 'end' => '13:15'],
            8 => ['start' => '13:15', 'end' => '14:00'],
            9 => ['start' => '14:00', 'end' => '14:45'],
            10 => ['start' => '14:45', 'end' => '15:30']
        ];

        // 1. Buat Peta Grid Kosong
        $grid = [];
        foreach ($hariMap as $dayKey => $dayName) {
            foreach ($periods as $pKey => $pData) {
                $grid[$dayKey][$pKey] = null; 
            }
        }

        // 2. Ambil Jadwal
        $schedulesRaw = Schedule::with(['subject', 'classroom'])
            ->where('user_id', $user->id)
            ->orderBy('start_time')
            ->get();

        // 3. Hitung rentang jam (Colspan)
        foreach ($schedulesRaw as $sch) {
            $start = substr($sch->start_time, 0, 5);
            $end = substr($sch->end_time, 0, 5);
            
            $startPeriod = null;
            $endPeriod = null;

            // Cari jadwal ini menyentuh jam ke berapa saja
            foreach ($periods as $pKey => $pData) {
                if ($start >= $pData['start'] && $start < $pData['end']) {
                    $startPeriod = $pKey;
                }
                if ($end > $pData['start'] && $end <= $pData['end']) {
                    $endPeriod = $pKey;
                }
            }

            // Jika valid, masukkan ke array dan hitung panjang kotaknya
            if ($startPeriod && $endPeriod) {
                $colspan = $endPeriod - $startPeriod + 1;
                
                $grid[$sch->day_of_week][$startPeriod] = [
                    'schedule' => $sch,
                    'colspan' => $colspan
                ];

                // KUNCI: Tandai kotak selanjutnya dengan 'skip' agar HTML tidak mencetak <td> berlebih
                for ($i = $startPeriod + 1; $i <= $endPeriod; $i++) {
                    $grid[$sch->day_of_week][$i] = 'skip';
                }
            }
        }

        $pdf = Pdf::loadView('pdf.teaching-schedule', [
            'user' => $user,
            'hariMap' => $hariMap,
            'periods' => $periods,
            'grid' => $grid
        ])->setPaper('A4', 'landscape');

        $fileName = 'Jadwal_Mengajar_' . str_replace(' ', '_', $user->name) . '.pdf';
        return $pdf->download($fileName);
    }
}