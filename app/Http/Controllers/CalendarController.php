<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AcademicCalendar;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        $calendar = null;

        if ($activeYear) {
            $calendar = AcademicCalendar::where('academic_year_id', $activeYear->id)->first();
            
            if ($calendar) {
                $calendar->file_url = asset('storage/' . $calendar->file_path);
                $calendar->academic_year_name = $activeYear->name; 
            }
        }

        return Inertia::render('Calendar/Index', [
            'calendar' => $calendar
        ]);
    }
}