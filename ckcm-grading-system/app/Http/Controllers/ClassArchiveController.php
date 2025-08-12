<?php

namespace App\Http\Controllers;

use App\Models\ClassArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth
use App\Models\ArchivedFinalGrade;
use App\Models\Classes;
use App\Models\User;
use TCPDF;


class CustomPDF extends TCPDF
{
    public function Header()
    {
        // Leave empty to remove the default header (including the black line)
    }
}


class ClassArchiveController extends Controller
{
    public function index(Request $request)
    {
        $termOrder = ['Prelim', 'Midterm', 'Semi-Finals', 'Finals'];

        // Get logged-in user
        $loggedInUser = Auth::user();
        $loggedInInstructor = $loggedInUser->name;
        $roles = explode(',', $loggedInUser->role); // Convert roles to an array

        // Check if the user is an admin
        $isAdmin = in_array('admin', $roles);

        // Apply filters
        $query = ClassArchive::query();

        // If the user is not an admin, filter by instructor
        if (!$isAdmin) {
            $query->where('instructor', $loggedInInstructor);
        }

        // Apply additional filters based on the request
        if ($request->has('academic_year') && $request->academic_year != '') {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->has('subject_code') && $request->subject_code != '') {
            $query->where('subject_code', 'LIKE', '%' . $request->subject_code . '%');
        }

        $records = $query->orderBy('academic_year', 'desc')
            ->orderBy('academic_period')
            ->orderBy('descriptive_title')
            ->orderBy('subject_code')
            ->get();

        // Get unique instructors for the dropdown
        $uniqueInstructors = ClassArchive::selectRaw('DISTINCT TRIM(LOWER(instructor)) as instructor')
            ->orderBy('instructor')
            ->pluck('instructor')
            ->map(fn($name) => ucwords($name)) // Capitalize first letter of each word
            ->unique()
            ->values();

        // Group the data
        // Group the data correctly
        $archivedData = $records->groupBy('academic_year')
            ->map(function ($yearGroup) use ($termOrder) {
                return $yearGroup->groupBy('academic_period')
                    ->map(function ($periodGroup) use ($termOrder) {
                        return $periodGroup->groupBy('subject_code') // Subject Code should be grouped here
                            ->map(function ($subjectGroup) use ($termOrder) {
                                return $subjectGroup->groupBy('instructor') // Now, group by Instructor
                                    ->map(function ($instructorGroup) use ($termOrder) {
                                        return $instructorGroup->groupBy('descriptive_title') // Then by Course Title
                                            ->map(function ($titleGroup) use ($termOrder) {
                                                return $titleGroup->groupBy('periodic_term') // Finally, group by Periodic Term
                                                    ->sortBy(function ($_, $key) use ($termOrder) {
                                                        return array_search($key, $termOrder);
                                                    });
                                            });
                                    });
                            });
                    });
            });

        $finalGrades = ArchivedFinalGrade::all()->groupBy(function ($item) {
            return $item->academic_year . '|' . $item->academic_period . '|' . $item->subject_code . '|' . $item->instructor . '|' . $item->descriptive_title . '|' . $item->studentID;
        });

        return view('instructor.my_class_archive', compact('archivedData', 'uniqueInstructors', 'finalGrades'));
    }
























    public function generateGradeSheetPDF(Request $request)
    {
        $academic_year = $request->academic_year;
        $academic_period = $request->academic_period;
        $subject_code = $request->subject_code;
        $instructor = $request->instructor;
        $descriptive_title = $request->descriptive_title;

        // Fetch relevant final grades
        $finalGrades = \App\Models\ArchivedFinalGrade::where([
            ['academic_year', $academic_year],
            ['academic_period', $academic_period],
            ['subject_code', $subject_code],
            ['instructor', $instructor],
            ['descriptive_title', $descriptive_title],
        ])->orderBy('department')->orderBy('name')->get();

        // Get schedule from the first record (if exists)
        $schedule = $finalGrades->first()->schedule ?? '';



        // Get the user who archived the grades (added_by from archived_final_grades)
        // $approvedBy = '___________________________';
        // if ($finalGrades->count() > 0 && $finalGrades->first()->added_by) {
        //     $approvedBy = $finalGrades->first()->added_by;
        // }

        // Group by department
        $gradesByDept = $finalGrades->groupBy('department');

        // College mapping (customize as needed)
        $collegeMap = [
            'Computer Science' => 'COLLEGE OF COMPUTER SCIENCE',
            'Business Administration' => 'COLLEGE OF BUSINESS',
            'Education' => 'COLLEGE OF EDUCATION',
            'Criminology' => 'COLLEGE OF CRIMINOLOGY',
            'English Language Studies' => 'COLLEGE OF ENGLISH LANGUAGE STUDIES',
            'Social Work' => 'COLLEGE OF SOCIAL WORK',

            // Add more mappings as needed
        ];

        $programHeads = [
            'Computer Science' => 'Marjon D. Senarlo, MSIT',
            'Business Administration' => 'Arlene N. Bacus, MBA',
            'Education' => 'Everose C. Toylo, M.Ed.',
            'Criminology' => 'Jennilyn B. Obena, MSCrim',
            'English Language Studies' => 'Anacleto S. Dolar Jr., MATE',
            'Social Work' => 'Sherlita A. Sintos, RSW',
            // Add more as needed
        ];

        // Create new TCPDF
        $pdf = new CustomPDF('P', 'mm', array(215.9, 355.6), true, 'UTF-8', false);
        $pdf->SetCreator('CKCM Grading System');
        $pdf->SetAuthor($instructor);
        $pdf->SetTitle('Grading Sheet');
        $pdf->SetMargins(10, 10, 10, true);

        foreach ($gradesByDept as $dept => $students) {
            // Remove "Bachelor of", "BS in", etc., and get only the last word group (e.g., "Computer Science")
            $deptDisplay = trim(preg_replace('/^(bachelor of|bs in|bsc|ba|ab)\s*/i', '', $dept));
            $parts = preg_split('/\s+in\s+/i', $deptDisplay);
            $mainDept = trim(end($parts));
            $college = 'COLLEGE OF ' . strtoupper($mainDept);

            $pdf->AddPage();

            $approvedBy = $programHeads[$mainDept] ?? '___________________________';


            $schoolLogo = public_path('system_images/logo.jpg'); // left logo
            $deptLogos = [
                'COLLEGE OF COMPUTER SCIENCE' => public_path('system_images/comsci.jpg'),
                'COLLEGE OF BUSINESS ADMINISTRATION' => public_path('system_images/cba.jpg'),
                'COLLEGE OF EDUCATION' => public_path('system_images/educ.jpg'),
                'COLLEGE OF CRIMINOLOGY' => public_path('system_images/crim.jpg'),
                'COLLEGE OF ENGLISH LANGUAGE STUDIES' => public_path('system_images/baels.jpg'),
                'COLLEGE OF SOCIAL WORK' => public_path('system_images/sw.jpg'),

                // Add more mappings as needed
            ];
            $deptLogo = $deptLogos[$college] ?? public_path('system_images/logo.jpg'); // right logo fallback


            $html = '
           <table width="100%">
            <tr>
                <td width="20%" align="right">
                    <img src="' . $schoolLogo . '" width="70" >
                </td>
                <td width="60%" align="center">
                    <p style="font-size:12px; font-weight:bold; line-height:2px;">CHRIST THE KING COLLEGE DE MARANDING, INC.</p>
                    <p style="font-size:10px; line-height:1px;">Maranding Lala, Lanao del Norte</p>
                    <p style="font-size:11px; font-weight:bold; line-height:15px;">' . $college . '</p>
                    <p style="font-size:10px; font-weight:bold; line-height:10px;">GRADING SHEET</p>
                    <p style=" line-height:1px;"></p>
                </td>
                <td width="20%" align="left">
                    <img src="' . $deptLogo . '" width="70">
                </td>
            </tr>
             </table>
             <br>
            <table cellpadding="1" style=" margin-left:10px; font-size:10px;">
                <tr>
                    <td><b>Instructor:</b> ' . $instructor . '</td>
                    <td><b>Date:</b> ' . date('m/d/Y') . '</td>
                </tr>
                <tr>
                    <td><b>Course Code:</b> ' . $subject_code . '</td>
                    <td><b>AY:</b> ' . $academic_year . '</td>
                </tr>
                <tr>
                    <td><b>Descriptive Title:</b> ' . $descriptive_title . ' </td>
                    <td><b>Semester:</b> ' . $academic_period . '</td>
                </tr>
                <tr>
                     <td><b>Number of Student:</b> ' . $students->count() . '</td>
                        <td><b>Schedule:</b> ' . $schedule . '</td>
                </tr>
            </table>

            <table border="1" cellpadding="2" >
                <thead>
                    <tr style="background-color:#eee; font-size:10px;" >
                        <th width="40%"  style="text-align:center;">Name of Student</th>
                        <th width="10%"  style="text-align:center;">Prelim</th>
                        <th width="10%"  style="text-align:center;">Midterm</th>
                        <th width="15%"  style="text-align:center;">Semi-Final</th>
                        <th width="10%"  style="text-align:center;">Final</th>
                        <th width="15%"  style="text-align:center;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
            ';
            foreach ($students as $student) {
                $html .= '
                    <tr style="font-size:10px;">
                        <td width="40%"  style="text-align:start;">' . htmlspecialchars($student->name) . '</td>
                        <td width="10%"  style="text-align:center;">' . htmlspecialchars($student->prelim) . '</td>
                        <td width="10%"  style="text-align:center;">' . htmlspecialchars($student->midterm) . '</td>
                        <td width="15%"  style="text-align:center;">' . htmlspecialchars($student->semi_finals) . '</td>
                        <td width="10%"  style="text-align:center;">' . htmlspecialchars($student->final) . '</td>
                        <td width="15%"  style="text-align:center;">' . htmlspecialchars($student->remarks) . '</td>
                    </tr>
                ';
            }
            $html .= '
                <tr>
                    <td colspan="6" style="text-align:center; font-weight:bold; border:none; font-size: 9px;">*******Nothing Follows********</td>
                </tr>
            ';
            $html .= '</tbody></table><br>';

            $html .= '
            <br>
           <table>
                <tr>
                    <td colspan="2" style="font-size:10px; text-align:left;">
                        Submitted by: <b style="font-size:9px;">' . strtoupper($instructor) . '</b><br>
                        <table width="100%"><tr><td align="center" style="font-size:9px;">Instructor</td><td></td></tr></table>
                    </td>
                </tr>
                <tr>
                  <td colspan="2" style="font-size:10px; text-align:center;"></td>
                </tr>
                <tr>

               <td style="font-size:10px; ">
                    Approved by: <b style="font-size:9px;">' . strtoupper($approvedBy) . '</b><br>
                     <table width="100%"><tr><td align="center" style="font-size:9px;">Program Head</td></tr></table>
                </td>
                <td style="font-size:10px; ">
                    Submitted to:<b style="font-size:9px;"> ' . strtoupper('ELVYN P. SALMERON, MMEM') . '</b> <br>
                     <table width="100%"><tr><td align="center" style="font-size:9px;">Registrar</td></tr></table>
                </td>
            </tr>
            </table>
            <br>
            <p style="font-size:9px; line-height:10px; text-align:center;">
                1.0=<b>EXCELLENT</b> &nbsp; 1.25-1.5=<b>VERY SATISFACTORY</b> &nbsp; 1.75-2.0=<b>SATISFACTORY</b>
                2.25-2.5=<b>FAIR</b> &nbsp; 2.75-3.0=<b>POOR</b> &nbsp; 5.0=<b>FAILED</b>
            </p>
            <div style="height: 10px;"></div>
            <p style="line-height:0; font-size:9px; ">*Attachment: Summary of Students with Deficiency</p>
            <p style="line-height:0; font-size:9px; ">*Copy Furnished: Instructor, College Dean and Registrar</p>
            ';

            $pdf->writeHTML($html, true, false, true, false, '');
        }

        $pdf->Output('gradesheet.pdf', 'I');
        exit;
    }
}
