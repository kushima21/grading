<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\AllGrades;
use Illuminate\Http\Request;

class StudentsGradeController extends Controller
{
    public function show(Request $request)
    {
        $studentID = Auth::user()->studentID; // Get logged-in user ID

        // Fetch grades for the logged-in user
        $query = AllGrades::where('studentID', $studentID)
            ->orderBy('academic_year', 'desc')
            ->orderBy('academic_period');

        // Apply filters if provided
        if ($request->has('academic_year') && $request->academic_year != '') {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->has('academic_period') && $request->academic_period != '') {
            $query->where('academic_period', $request->academic_period);
        }

        // Get grades
        $grades = $query->get()->groupBy(['academic_year', 'academic_period']);

        // Get distinct academic years and periods for filtering
        $academicYears = AllGrades::select('academic_year')->distinct()->pluck('academic_year');
        $academicPeriods = AllGrades::select('academic_period')->distinct()->pluck('academic_period');

        return view('my_grades', compact('grades', 'academicYears', 'academicPeriods'));
    }
}
