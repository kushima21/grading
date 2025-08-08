<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\FinalGrade;
use App\Models\Percentage;
use App\Models\QuizzesAndScores;
use App\Models\Classes_Student;
use App\Models\Classes;
use App\Models\Department;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Course;


class RegistrarController extends Controller
{
    public function index()
    {
        return view("registrar.registrar_dashboard");
    }

    

public function registrar_classes()
{
    // Fetch all classes
    $classes = Classes::orderBy('id', 'desc')->get();

    // Fetch all instructors with role 'instructor'
    $instructors = User::where('role', 'LIKE', '%instructor%')->get();

    // Fetch all student-class relationships
    $classes_student = Classes_Student::all()->groupBy('classID');

    // Fetch all courses
    $courses = Course::select('id', 'course_no', 'descriptive_title')->get();

    // Fetch final grades
    $finalGrades = DB::table('final_grade')->get();

    return view('registrar.classes', compact('classes', 'instructors', 'classes_student', 'finalGrades', 'courses'));
}

public function searchInstructor(Request $request)
{
    $query = $request->input('query');

    $instructors = User::where('role', 'LIKE', '%instructor%')
        ->where('name', 'LIKE', $query . '%')
        ->select('id', 'name')
        ->limit(5)
        ->get();

    return response()->json($instructors);
}



public function CreateClass(Request $request)
{
    $request->validate([
        "course_no" => "required",
        // Remove this to avoid "required" error before we compute it manually:
        // "descriptive_title" => "required",
        "units" => "required",
        "instructor" => "required",
        "academic_period" => "required",
        "academic_year" => "required",
        "schedule" => "required",
        "status" => "required",
        "added_by" => "required"
    ]);

    // Fetch descriptive title from DB based on selected course_no
    $course = Course::where('course_no', $request->course_no)->first();

    if (!$course) {
        return redirect()->back()->withInput()->with('error', "Course with code '{$request->course_no}' not found.");
    }

    // Validate the instructor
    $instructor_name = $request->instructor;
    $instructor = User::where('name', $instructor_name)->first();

    if (!$instructor) {
        return redirect()->back()->withInput()->with('error', "The instructor '{$instructor_name}' does not exist in the system.");
    }

    $class = new Classes();
    $class->course_no = $request->course_no;
    $class->descriptive_title = $course->descriptive_title; // fetched from DB
    $class->units = $request->units;
    $class->instructor = $request->instructor;
    $class->academic_period = $request->academic_period;
    $class->academic_year = $request->academic_year;
    $class->schedule = $request->schedule;
    $class->status = $request->status;
    $class->added_by = $request->added_by;

    if ($class->save()) {
        $user = Auth::user();

        // Store notification
        DB::table('notif_table')->insert([
            'notif_type'              => 'Class Added',
            'class_id'                => $class->id,
            'class_course_no'         => $class->course_no,
            'class_descriptive_title' => $class->descriptive_title,
            'department'              => $user->department ?? null,
            'added_by_id'             => $user->studentID,
            'added_by_name'           => $user->name,
            'target_by_id'            => $instructor->studentID ?? null,
            'target_by_name'          => $instructor->name ?? null,
            'status_from_added'       => 'unchecked',
            'status_from_target'      => 'unchecked',
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        return redirect(route("registrar_classes"))->with("success", "Class Created Successfully");
    }

    return redirect(route("registrar_classes"))->withInput()->with("error", "Class Creation Failed");
}


public function EditClass(Request $request, Classes $class)
{
    $request->validate([
        "course_no" => "required", // changed from subject_code
        "descriptive_title" => "required",
        "units" => "required",
        "instructor" => "required",
        "academic_period" => "required",
        "academic_year" => "required",
        "schedule" => "required",
        "status" => "required",
    ]);

    $class->course_no = $request->course_no; // changed
    $class->descriptive_title = $request->descriptive_title;
    $class->units = $request->units;
    $class->instructor = $request->instructor;
    $class->academic_period = $request->academic_period;
    $class->academic_year = $request->academic_year;
    $class->schedule = $request->schedule;
    $class->status = $request->status;

    if ($class->save()) {
        $user = Auth::user();

        $instructor_name = $class->instructor;
        $instructor = User::where('name', $instructor_name)->first();

        if (!$instructor) {
            return redirect()->back()->with('error', "The instructor '{$instructor_name}' does not exist in the system.");
        }

        DB::table('notif_table')->insert([
            'notif_type'              => 'Class Edited',
            'class_id'                => $class->id,
            'class_course_no'         => $class->course_no, // changed
            'class_descriptive_title' => $class->descriptive_title,
            'department'              => $user->department ?? null,
            'added_by_id'             => $user->studentID,
            'added_by_name'           => $user->name,
            'target_by_id'            => $instructor->studentID ?? null,
            'target_by_name'          => $instructor->name ?? null,
            'status_from_added'       => 'unchecked',
            'status_from_target'      => 'unchecked',
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        return redirect(route("registrar_classes"))->with("success", "Class Edited Successfully");
    }

    return redirect(route("registrar_classes"))->with("error", "Class Edition Failed");
}


    public function DeleteClass(Classes $class)
    {
        try {
            $user = Auth::user();

            $instructor_name = $class->instructor; // this is a name like "Dave"
            $instructor = User::where('name', $instructor_name)->first();

            // Store notification
            DB::table('notif_table')->insert([
                'notif_type'      => 'Class Deleted',
                'class_id'        => $class->id,
                'class_course_no' => $class->course_no,
                'class_descriptive_title' => $class->descriptive_title,
                'department'      => $user->department ?? null, // Optional if you store department
                'added_by_id'     => $user->studentID,
                'added_by_name'   => $user->name,
                'target_by_id'    => $instructor->studentID ?? null,
                'target_by_name'  => $instructor->name ?? null,
                'status_from_added'    => 'unchecked',
                'status_from_target'    => 'unchecked',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // Delete related records
            DB::table('classes_student')->where('classID', $class->id)->delete();
            DB::table('final_grade')->where('classID', $class->id)->delete();
            DB::table('percentage')->where('classID', $class->id)->delete();
            DB::table('quizzes_scores')->where('classID', $class->id)->delete();

            // Delete the class from the database
            $class->delete();

            return redirect()->route('registrar_classes')->with('success', 'Class and its related records deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('registrar_classes')->with('error', 'Failed to delete class. Please try again.');
        }
    }


    public function show(Request $request, $class)
    {
        $classes = Classes::where('id', $class)->first();

        if (!$classes) {
            return redirect()->route('instructor.my_class')
                             ->with('warning', 'The class you are trying to view no longer exists.');
        }

        // 💯 This part is untouched and still works for ADDING STUDENTS
        $enrolledStudentIds = Classes_Student::where('classID', $class)->pluck('studentID')->toArray();

        $students = User::where('role', 'student')
            ->whereNotIn('studentID', $enrolledStudentIds)
            ->get();

        // 💯 This part still works for displaying ENROLLED STUDENTS
        $classes_student = Classes_Student::where('classID', $class)->get();

        // 💯 This part still works for quizzes
        $quizzesandscores = QuizzesAndScores::where('classID', $class)->get();

        // 💯 This part still works for percentages
        $percentage = Percentage::where('classID', $class)->get();

        // 💯 This part still works for final grades
        $finalGrades = DB::table('final_grade')
            ->where('classID', $class)
            ->get();

        // ✅ NOW THIS IS THE NEW PART - FILTER BY DEPARTMENT FOR DEAN
        $user = Auth::user();
        $userRoles = explode(',', $user->role);

        if (in_array('dean', $userRoles)) {
            // ✅ The user is a dean, now filter by department
            $userDepartment = $user->department;

            $filteredStudents = Classes_Student::where('classID', $class)
                ->where('department', $userDepartment)
                ->get();
        } else {
            // ✅ If the user is not a dean, show all students
            $filteredStudents = Classes_Student::where('classID', $class)->get();
        }

        // ✅ Now pass EVERYTHING to the Blade (including the new filtered students)
        return view('registrar.classes_view', compact(
            'class',
            'classes',
            'students',
            'classes_student',
            'quizzesandscores',
            'percentage',
            'finalGrades',
            'filteredStudents'
        ));
    }

    public function importCSV(Request $request, $class)
    {
        // Fetch class model
        $class = Classes::findOrFail($class);

        // Validate file
        $request->validate([
            'students_csv' => 'required|mimes:csv,txt|max:2048'
        ]);

        // Read CSV file
        $file = $request->file('students_csv');
        $csvData = array_map('str_getcsv', file($file));

        // Remove CSV header row
        array_shift($csvData);

        $programToDepartment = [
            'BSBA' => 'Bachelor of Science in Business Administration',
            'BSCS' => 'Bachelor of Science in Computer Science',
            'BSSW' => 'Bachelor of Science in Social Work',
            'BAELS' => 'Bachelor of Arts in English Language Studies',
            'BEED'  => 'Bachelor of Elementary Education',
            'BSED' => 'Bachelor of Secondary Education',
            'BSCRIM' => 'Bachelor of Science in Criminology',
        ];

        $students = [];
        $insertedStudentIDs = [];

        foreach ($csvData as $row) {
            if (count($row) < 5) {
                continue; // Skip invalid rows
            }

            $fullname = trim($row[1] . ", " . $row[2] . " " . $row[3]);
            $program = strtoupper(trim(explode('-', $row[6])[0]));


            $department = $programToDepartment[$program] ?? 'Unknown Department';


            $students[] = [
                'studentID'  => $row[4],
                'email'      => $row[5],
                'name'       => $fullname,
                'gender'     => null,
                'department' => $department,
                'classID'    => $class->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Collect student IDs for periodic term insertion
            $insertedStudentIDs[] = $row[4];
        }

        // Bulk insert students
        Classes_Student::insert($students);

        // Array of periodic terms
        $periodicTerms = ['Prelim', 'Midterm', 'Semi-Finals', 'Finals'];

        // Prepare data for quizzes_scores
        $quizScores = [];
        foreach ($insertedStudentIDs as $studentID) {
            foreach ($periodicTerms as $term) {
                $quizScores[] = [
                    'classID'             => $class->id,
                    'studentID'           => $studentID,
                    'periodic_term'       => $term,
                    'quizzez'             => 0,  // Default 0
                    'attendance_behavior' => 0,  // Default 0
                    'assignments'         => 0,  // Default 0
                    'exam'                => 0,  // Default 0
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ];
            }
        }

        // Bulk insert quiz scores
        QuizzesAndScores::insert($quizScores);

        return back()->with('success', 'Students imported successfully.');
    }


    public function addstudent(Request $request, Classes $class)
    {
        $request->validate([
            "student_id" => "required",
            "name" => "required",
            "gender" => "required",
            "email" => "required|email",
            "department" => "required",
        ]);

        // Create a new instance of Classes_Student and assign the values
        $classStudent = new Classes_Student();
        $classStudent->classID = $class->id;
        $classStudent->studentID = $request->student_id;
        $classStudent->name = $request->name;
        $classStudent->gender = $request->gender;
        $classStudent->email = $request->email;
        $classStudent->department = $request->department;

        // Array of periodic terms
        $periodicTerms = ['Prelim', 'Midterm', 'Semi-Finals', 'Finals'];

        // Save the instance of Classes_Student
        if ($classStudent->save()) {
            // Insert a row for each periodic term in quizzes_scores
            foreach ($periodicTerms as $term) {
                $quizzesandscores = new QuizzesAndScores();
                $quizzesandscores->classID = $class->id;
                $quizzesandscores->studentID = $request->student_id;
                $quizzesandscores->periodic_term = $term;
                $quizzesandscores->quizzez = 0;              // Default 0
                $quizzesandscores->attendance_behavior = 0;  // Default 0
                $quizzesandscores->assignments = 0;          // Default 0
                $quizzesandscores->exam = 0;                 // Default 0
                $quizzesandscores->save();
            }

            return redirect()->route("class.show", $class->id)->with("success", "Student added successfully.");
        }

        return redirect()->route("class.show", $class->id)->with("error", "Failed to add student. Please try again.");
    }


    public function removestudent($class, $student)
    {
        // Find the student in the class
        $classStudent = Classes_Student::where('classID', $class)
            ->where('studentID', $student)
            ->first();

        // Find all related quizzes and scores for this student in the class
        $quizzesScores = QuizzesAndScores::where('classID', $class)
            ->where('studentID', $student)
            ->get();

        // Find the student's final grade in the class
        $finalGrade = FinalGrade::where('classID', $class)
            ->where('studentID', $student)
            ->first();

        if ($classStudent || $quizzesScores->isNotEmpty() || $finalGrade) {
            // Delete student from classes_student
            if ($classStudent) {
                $classStudent->delete();
            }

            // Delete related quizzes and scores
            if ($quizzesScores->isNotEmpty()) {
                foreach ($quizzesScores as $score) {
                    $score->delete();
                }
            }

            // Delete the student's final grade
            if ($finalGrade) {
                $finalGrade->delete();
            }

            return redirect()->route("class.show", $class)->with("success", "Student removed successfully.");
        }

        return redirect()->route("class.show", $class)->with("error", "Student not found or already removed.");
    }


    public function addPercentageAndScores(Request $request, $class)
    {
        $periodicTerms = $request->input('periodic_terms');
        $warnings = [];

        foreach ($periodicTerms as $term) {
            // Calculate total percentage
            $totalPercentage = $request->input("quiz_percentage.$term") +
                $request->input("attendance_percentage.$term") +
                $request->input("assignment_percentage.$term") +
                $request->input("exam_percentage.$term");

            if ($totalPercentage !== 100) {
                return redirect()->route("class.show", $class)
                    ->withErrors(["The total percentage for $term must equal 100%."]);
            }

            foreach (['quiz', 'attendance', 'assignment', 'exam'] as $category) {
                $totalScore = $request->input("{$category}_total_score.$term");

                // Check if this total score exists in transmuted_grade
                $scoreExists = DB::table('transmuted_grade')
                    ->where('score_bracket', $totalScore)
                    ->exists();

                if (!$scoreExists) {
                    $warnings[] = "⚠️WARNING! The total score of $totalScore for " . ucfirst($category) . " in $term does not exist in the database (the system cannot calculate, please change the total score).";
                }
            }

            // Save or update for each term
            Percentage::updateOrCreate(
                ['classID' => $class, 'periodic_term' => $term],
                [
                    'quiz_percentage' => $request->input("quiz_percentage.$term") ?? 0,
                    'quiz_total_score' => $request->input("quiz_total_score.$term") ?? 0,
                    'attendance_percentage' => $request->input("attendance_percentage.$term") ?? 0,
                    'attendance_total_score' => $request->input("attendance_total_score.$term") ?? 0,
                    'assignment_percentage' => $request->input("assignment_percentage.$term") ?? 0,
                    'assignment_total_score' => $request->input("assignment_total_score.$term") ?? 0,
                    'exam_percentage' => $request->input("exam_percentage.$term") ?? 0,
                    'exam_total_score' => $request->input("exam_total_score.$term") ?? 0,
                ]
            );
        }

        // Redirect with warnings if any
        return redirect()->route("class.show", $class)
            ->with('success', 'Data saved successfully.')
            ->with('warnings', $warnings);
    }



    public function addQuizAndScore(Request $request, $class)
    {
        $scores = $request->input('scores');
        $periodicTerm = $request->input('periodic_term');

        // Retrieve total scores from the percentage table for the specific class
        $percentage = Percentage::where('classID', $class)
            ->where('periodic_term', $periodicTerm)
            ->first();

        if (!$percentage) {
            return redirect()->back()->with('error', 'Percentage data not found for this class.');
        }

        foreach ($scores as $studentId => $fields) {
            $classStudent = Classes_Student::where('classID', $class)
                ->where('studentID', $studentId)
                ->first();

            $studentName = $classStudent->name ?? "Student ID $studentId"; // Fetch the student record

            // Validate scores against total scores from percentage table
            if (($fields['quizzez'] ?? 0) > $percentage->quiz_total_score) {
                return redirect()->back()->with('error', "Quiz score for {$studentName} in {$periodicTerm} exceeds the total score.");
            }
            if (($fields['attendance_behavior'] ?? 0) > $percentage->attendance_total_score) {
                return redirect()->back()->with('error', "Attendance score for {$studentName} in {$periodicTerm} exceeds the total score.");
            }
            if (($fields['assignments'] ?? 0) > $percentage->assignment_total_score) {
                return redirect()->back()->with('error', "Assignment score for {$studentName} in {$periodicTerm} exceeds the total score.");
            }
            if (($fields['exam'] ?? 0) > $percentage->exam_total_score) {
                return redirect()->back()->with('error', "Exam score for {$studentName} in {$periodicTerm} exceeds the total score.");
            }

            // Check for existing record
            $existingRecord = QuizzesAndScores::where('classID', $class)
                ->where('studentID', $studentId)
                ->where('periodic_term', $periodicTerm)
                ->first();

            if ($existingRecord) {
                $existingRecord->update([
                    'quizzez' => $fields['quizzez'] ?? $existingRecord->quizzez,
                    'attendance_behavior' => $fields['attendance_behavior'] ?? $existingRecord->attendance_behavior,
                    'assignments' => $fields['assignments'] ?? $existingRecord->assignments,
                    'exam' => $fields['exam'] ?? $existingRecord->exam,
                    'updated_at' => now(),
                ]);
            } else {
                QuizzesAndScores::create([
                    'classID' => $class,
                    'studentID' => $studentId,
                    'periodic_term' => $periodicTerm,
                    'quizzez' => $fields['quizzez'] ?? null,
                    'attendance_behavior' => $fields['attendance_behavior'] ?? null,
                    'assignments' => $fields['assignments'] ?? null,
                    'exam' => $fields['exam'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Scores updated successfully.');
    }

    public function initializeGrades(Request $request)
    {
        // Check if the grades are empty or null
        if (empty($request->grades)) {
            return back()->with('error', 'No students yet, you can\'t initialize.');
        }

        foreach ($request->grades as $grade) {
            $classInfo = Classes::find($grade['classID']); // Get class info

            // Fetch student info correctly
            $studentInfo = Classes_Student::where('studentID', $grade['studentID'])->first();

            // Initialize all grades with "Initialized" status
            DB::table('final_grade')->updateOrInsert(
                [
                    'classID' => $grade['classID'],
                    'studentID' => $grade['studentID']
                ],
                [
                    'course_no' => optional($classInfo)->course_no,
                    'descriptive_title' => optional($classInfo)->descriptive_title,
                    'instructor' => optional($classInfo)->instructor,
                    'academic_period' => optional($classInfo)->academic_period,
                    'schedule' => optional($classInfo)->schedule,
                    'name' => optional($studentInfo)->name,
                    'gender' => optional($studentInfo)->gender,
                    'email' => optional($studentInfo)->email,
                    'department' => optional($studentInfo)->department,
                    'prelim' => $grade['prelim'],
                    'midterm' => $grade['midterm'],
                    'semi_finals' => $grade['semi_finals'],
                    'final' => $grade['final'],
                    'remarks' => $grade['remarks'],
                    'status' => '', // ✅ Setting initial status here
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return back()->with('success', 'Grades have been initialized successfully!');
    }


    public function lockInGrades(Request $request)
    {
        // Check if the grades are empty or null
        if (empty($request->grades)) {
            return back()->with('error', 'No students yet, you can\'t lock.');
        }

        foreach ($request->grades as $grade) {
            $classInfo = Classes::find($grade['classID']); // Get class info

            // Fetch student info correctly
            $studentInfo = Classes_Student::where('studentID', $grade['studentID'])->first();

            // Update or insert the active grade record
            DB::table('final_grade')->updateOrInsert(
                [
                    'classID' => $grade['classID'],
                    'studentID' => $grade['studentID']
                ],
                [
                    'course_no' => optional($classInfo)->course_no,
                    'descriptive_title' => optional($classInfo)->descriptive_title,
                    'instructor' => optional($classInfo)->instructor,
                    'academic_period' => optional($classInfo)->academic_period,
                    'schedule' => optional($classInfo)->schedule,
                    'name' => optional($studentInfo)->name,
                    'gender' => optional($studentInfo)->gender,
                    'email' => optional($studentInfo)->email,
                    'department' => optional($studentInfo)->department,
                    'prelim' => $grade['prelim'],
                    'midterm' => $grade['midterm'],
                    'semi_finals' => $grade['semi_finals'],
                    'final' => $grade['final'],
                    'remarks' => $grade['remarks'],
                    'status' => 'Locked', // Add status field here
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $classIDs[] = $grade['classID'];
        }

        Classes::whereIn('id', $classIDs)->update(['status' => 'Locked']);

        return back()->with('success', 'Final grades have been locked successfully!');
    }

    public function UnlockGrades(Request $request)
    {
        $department = $request->input('department');
        $classID = $request->input('classID'); // 🔥 Include classID

        if (!$department || !$classID) {
            return back()->with('error', 'Invalid request. No department or class selected.');
        }

        // Unlock grades only for the specified department and class
        DB::table('final_grade')
            ->where('department', $department)
            ->where('classID', $classID) // 🔥 Ensure only this class is affected
            ->update(['status' => null]);

        Classes::where('id', $classID)->update(['status' => 'Active']);

        return back()->with('success', "Final grades have been unlocked!");
    }


    public function SubmitGrades(Request $request)
    {
        $department = $request->input('department');
        $classID = $request->input('classID'); // 🔥 Include classID

        if (!$department || !$classID) {
            return back()->with('error', 'Invalid request. No department or class selected.');
        }

        // Update submit_status to 'Submitted' for locked grades in the selected department and class
        DB::table('final_grade')
            ->where('department', $department)
            ->where('classID', $classID) // 🔥 Ensure only this class is affected
            ->where('status', 'Locked')
            ->update([
                'submit_status' => 'Submitted',
                'dean_status' => '',
                'dean_comment' => '',
                'registrar_status' => '',
                'registrar_comment' => '',
                'updated_at' => now(),
            ]);

        Classes::where('id', $classID)->update(['status' => 'Grades Submitted, Waiting for dean\'s approval']);

        $user = Auth::user();

        $class = Classes::find($classID);

        if (stripos($department, 'education') !== false) {
            $users = User::whereRaw('LOWER(department) LIKE ?', ['%education%'])->get();
        } else {
            $users = User::where('department', $department)->get();
        }

        $dean = $users->first(function ($user) {
            $roles = explode(',', $user->role); // assuming role is stored as comma-separated
            return in_array('dean', array_map('trim', $roles));
        });


        // Store notification
        DB::table('notif_table')->insert([
            'notif_type'      => 'Class grades has been submitted to Dean of ' . $department  . ' Department',
            'class_id'        => $class->id,
            'class_course_no' => $class->course_no,
            'class_descriptive_title' => $class->descriptive_title,
            'department'      => $user->department ?? null, // Optional if you store department
            'added_by_id'     => $user->studentID,
            'added_by_name'   => $user->name,
            'target_by_id'    => $dean->studentID ?? null,
            'target_by_name'  => $dean->name ?? null,
            'status_from_added'    => 'unchecked',
            'status_from_target'    => 'unchecked',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if($user->name == $class->instructor && strpos($user->role, 'dean') !== false){
            return back()->with('success', "Grades for $department have been submitted to the its corresponding Dean!");
        }

        return redirect()->route('instructor.my_class')->with('success', "Grades for $department have been submitted to the its corresponding Dean!");

    }

    public function SubmitGradesRegistrar(Request $request)
    {
        $department = $request->input('department');
        $classID = $request->input('classID'); // 🔥 Include classID

        if (!$department || !$classID) {
            return back()->with('error', 'Invalid request. No department or class selected.');
        }

        // Update submit_status to 'Submitted' for locked grades in the selected department and class
        DB::table('final_grade')
            ->where('department', $department)
            ->where('classID', $classID) // 🔥 Ensure only this class is affected
            ->where('status', 'Locked')
            ->update([
                'registrar_status' => 'Pending',
                'updated_at' => now(),
            ]);


        Classes::where('id', $classID)->update(['status' => 'Grades has been submitted to the registrar, Waiting for approval']);

        $user = Auth::user();
        $class = Classes::find($classID);
        $users = User::all();

        // Get Registrar
        $registrar = $users->first(function ($user) {
            $roles = explode(',', $user->role); // assuming roles are comma-separated
            return in_array('registrar', array_map('trim', $roles));
        });

        // Get Instructor from class (match by name)
        $instructor = $users->firstWhere('name', $class->instructor);

        // Shared notification content
        $baseNotif = [
            'notif_type'      => 'Class grades submitted to the Registrar',
            'class_id'        => $class->id,
            'class_course_no' => $class->course_no,
            'class_descriptive_title' => $class->descriptive_title,
            'department'      => $user->department ?? null,
            'added_by_id'     => $user->studentID,
            'added_by_name'   => $user->name,
            'status_from_added'    => 'unchecked',
            'status_from_target'    => 'unchecked',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // 🔔 Notify Registrar
        DB::table('notif_table')->insert(array_merge($baseNotif, [
            'target_by_id' => $registrar->studentID ?? null,
            'target_by_name' => $registrar->name ?? null,
        ]));

        // 🔔 Notify Instructor
        DB::table('notif_table')->insert(array_merge($baseNotif, [
            'target_by_id' => $instructor->studentID ?? null,
            'target_by_name' => $instructor->name ?? null,
        ]));


        return redirect()->route('registrar_classes')->with('success', "Grades for $department have been submitted to the Registrar!");
    }


    public function submitDecision(Request $request)
    {
        // Validate input
        $request->validate([
            'dean_status' => 'required',
            'classID' => 'required',
            'department' => 'required', // 🔥 Ensure department is required
            'dean_comment' => 'nullable|string'
        ]);

        // Build update data
        $updateData = [
            'dean_status' => $request->dean_status,
            'dean_comment' => $request->dean_comment,
            'updated_at' => now()
        ];

        // ✅ If "Returned", also update submit_status & class status
        if ($request->dean_status == 'Returned') {
            $updateData['submit_status'] = 'Returned';

            $user = Auth::user();

            $class = Classes::find($request->classID);

            // Get DEAN of the same department
            if (stripos($request->department, 'education') !== false) {
                $users = User::whereRaw('LOWER(department) LIKE ?', ['%education%'])->get();
            } else {
                $users = User::where('department', $request->department)->get();
            }

            $dean = $users->first(function ($user) {
                $roles = explode(',', $user->role); // assuming role is comma-separated
                return in_array('dean', array_map('trim', $roles));
            });

            // Get INSTRUCTOR by matching class instructor name
            $instructor = User::where('name', $class->instructor)->first();

            // Shared notification data
            $baseNotif = [
                'notif_type'      => 'Class grades have been rejected by the dean of ' . $request->department . ' please review them.',
                'class_id'        => $class->id,
                'class_course_no' => $class->course_no,
                'class_descriptive_title' => $class->descriptive_title,
                'department'      => $user->department ?? null,
                'added_by_id'     => $user->studentID,
                'added_by_name'   => $user->name,
                'status_from_added'    => 'unchecked',
                'status_from_target'    => 'unchecked',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 🔔 Notify DEAN
            DB::table('notif_table')->insert(array_merge($baseNotif, [
                'target_by_id' => $dean->studentID ?? null,
                'target_by_name' => $dean->name ?? null,
            ]));

            // 🔔 Notify INSTRUCTOR
            DB::table('notif_table')->insert(array_merge($baseNotif, [
                'target_by_id' => $instructor->studentID ?? null,
                'target_by_name' => $instructor->name ?? null,
            ]));

            // 🔥 Update class status to "Rejected"
            Classes::where('id', $request->classID)->update(['status' => 'Grades returned by the dean of ' . $request->department . ' Please review them.']);
        }

        // ✅ If "Confirmed", update submit_status & class status
        if ($request->dean_status == 'Confirmed') {
            $updateData['submit_status'] = 'Submitted';


            $user = Auth::user();

            $class = Classes::find($request->classID);

            $instructor_name = $class->instructor; // this is a name like "Dave"
            $instructor = User::where('name', $instructor_name)->first();

            // Store notification
            DB::table('notif_table')->insert([
                'notif_type'      => 'Class grades has been approved by the dean of ' . $request->department . ' department ',
                'class_id'        => $class->id,
                'class_course_no' => $class->course_no,
                'class_descriptive_title' => $class->descriptive_title,
                'department'      => $user->department ?? null, // Optional if you store department
                'added_by_id'     => $user->studentID,
                'added_by_name'   => $user->name,
                'target_by_id'    => $instructor->studentID ?? null,
                'target_by_name'  => $instructor->name ?? null,
                'status_from_added'    => 'unchecked',
                'status_from_target'    => 'unchecked',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 🔥 Update class status to "Approved"
            Classes::where('id', $request->classID)->update(['status' => 'Dean approved the submitted grade']);
        }


        // ✅ Update only records matching classID and department
        DB::table('final_grade')
            ->where('classID', $request->classID)
            ->where('department', $request->department)
            ->update($updateData);

        return back()->with('success', 'Dean’s decision has been submitted successfully!');
    }


    public function submitDecisionRegistrar(Request $request)
    {
        // Validate input
        $request->validate([
            'registrar_status' => 'required|string|in:Approved,Rejected',
            'classID' => 'required|integer',
            'department' => 'required|string', // 🔥 Ensure department is required
            'registrar_comment' => 'nullable|string'
        ]);

        // Build update data
        $updateData = [
            'registrar_status' => $request->registrar_status,
            'registrar_comment' => $request->registrar_status === 'Rejected' ? $request->registrar_comment : null,
            'updated_at' => now()
        ];

        // ✅ If "Rejected", also update submit_status & class status
        if ($request->registrar_status == 'Rejected') {
            $updateData['registrar_status'] = 'Rejected';
            $updateData['dean_status'] = 'Returned';

            $user = Auth::user();

            $class = Classes::find($request->classID);

            if (stripos($request->department, 'education') !== false) {
                $users = User::whereRaw('LOWER(department) LIKE ?', ['%education%'])->get();
            } else {
                $users = User::where('department', $request->department)->get();
            }

            // Find the Dean
            $dean = $users->first(function ($user) {
                $roles = explode(',', $user->role); // assuming role is stored as comma-separated
                return in_array('dean', array_map('trim', $roles));
            });

            // Find the Instructor by name (from class model)
            $instructor = User::where('name', $class->instructor)->first();

            // Common notification data
            $baseNotif = [
                'notif_type'      => 'Class grades have been rejected by the registrar. Please review them.',
                'class_id'        => $class->id,
                'class_course_no' => $class->course_no,
                'class_descriptive_title' => $class->descriptive_title,
                'department'      => $user->department ?? null,
                'added_by_id'     => $user->studentID,
                'added_by_name'   => $user->name,
                'status_from_added'    => 'unchecked',
                'status_from_target'   => 'unchecked',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 🔔 Notify Dean
            DB::table('notif_table')->insert(array_merge($baseNotif, [
                'target_by_id' => $dean->studentID ?? null,
                'target_by_name' => $dean->name ?? null,
            ]));

            // 🔔 Notify Instructor
            DB::table('notif_table')->insert(array_merge($baseNotif, [
                'target_by_id' => $instructor->studentID ?? null,
                'target_by_name' => $instructor->name ?? null,
            ]));


            // 🔥 Update class status to "Rejected"
            Classes::where('id', $request->classID)->update(['status' => 'Grades returned by the registrar, please review them.']);

        }

        // ✅ If "Approved", update submit_status & class status
        if ($request->registrar_status == 'Approved') {
            $updateData['registrar_status'] = 'Approved'; // Indicating final step before submission

            if (empty($request->grades)) {
                return back()->with('error', 'No students selected, you can\'t lock.');
            }

            $selectedDepartment = $request->department;
            $classIDs = [];

            foreach ($request->grades as $grade) {
                // Get student info for each department
                $studentInfo = Classes_Student::where('studentID', $grade['studentID'])
                    ->where('department', $selectedDepartment)
                    ->first();

                if ($studentInfo) {
                    $classInfo = Classes::find($grade['classID']);
                    $courseNo = optional($classInfo)->course_no;
                    $descriptiveTitle = optional($classInfo)->descriptive_title;
                    $units = optional($classInfo)->units;
                    $schedule = optional($classInfo)->schedule;
                    $instructor = optional($classInfo)->instructor;
                    $academicYear = optional($classInfo)->academic_year;
                    $academicPeriod = optional($classInfo)->academic_period;
                    $addedby = optional($classInfo)->added_by;

                    // Handle quizzes and scores for the student
                    $quizzesScores = DB::table('quizzes_scores')
                        ->where('classID', $grade['classID'])
                        ->where('studentID', $grade['studentID'])
                        ->get();

                    foreach ($quizzesScores as $score) {
                        $percentageData = DB::table('percentage')
                            ->where('classID', $score->classID)
                            ->first();

                        DB::table('archived_quizzesandscores')->insert([
                            'classID' => $score->classID,
                            'course_no' => $courseNo,
                            'descriptive_title' => $descriptiveTitle,
                            'units' => $units,
                            'instructor' => $instructor,
                            'studentID' => $score->studentID,
                            'periodic_term' => $score->periodic_term,
                            'quiz_percentage' => $percentageData->quiz_percentage ?? null,
                            'quiz_total_score' => $percentageData->quiz_total_score ?? null,
                            'quizzez' => $score->quizzez,
                            'attendance_percentage' => $percentageData->attendance_percentage ?? null,
                            'attendance_total_score' => $percentageData->attendance_total_score ?? null,
                            'attendance_behavior' => $score->attendance_behavior,
                            'assignment_percentage' => $percentageData->assignment_percentage ?? null,
                            'assignment_total_score' => $percentageData->assignment_total_score ?? null,
                            'assignments' => $score->assignments,
                            'exam_percentage' => $percentageData->exam_percentage ?? null,
                            'exam_total_score' => $percentageData->exam_total_score ?? null,
                            'exam' => $score->exam,
                            'academic_period' => $academicPeriod,
                            'academic_year' => $academicYear,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Remove the student's quizzes and scores after transferring
                    DB::table('quizzes_scores')
                        ->where('classID', $grade['classID'])
                        ->where('studentID', $grade['studentID'])
                        ->delete();

                    // Insert into final archived grades
                    DB::table('archived_final_grades')->insert([
                        'classID' => $grade['classID'],
                        'studentID' => $grade['studentID'],
                        'course_no' => $courseNo,
                        'descriptive_title' => $descriptiveTitle,
                        'units' => $units,
                        'schedule' => $schedule,
                        'instructor' => $instructor,
                        'academic_year' => $academicYear,
                        'academic_period' => $academicPeriod,
                        'name' => $studentInfo->name,
                        'gender' => $studentInfo->gender,
                        'email' => $studentInfo->email,
                        'department' => $selectedDepartment,
                        'prelim' => $grade['prelim'],
                        'midterm' => $grade['midterm'], 
                        'semi_finals' => $grade['semi_finals'],
                        'final' => $grade['final'],
                        'remarks' => $grade['remarks'],
                        'status' => 'Approved',
                        'added_by' => $addedby,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]);

                    // Insert into final archived grades
                    DB::table('grade_logs')->insert([
                        'classID' => $grade['classID'],
                        'studentID' => $grade['studentID'],
                        'course_no' => $courseNo,
                        'descriptive_title' => $descriptiveTitle,
                        'units' => $units,
                        'schedule' => $schedule,
                        'instructor' => $instructor,
                        'academic_year' => $academicYear,
                        'academic_period' => $academicPeriod,
                        'name' => $studentInfo->name,
                        'gender' => $studentInfo->gender,
                        'email' => $studentInfo->email,
                        'department' => $selectedDepartment,
                        'prelim' => $grade['prelim'],
                        'midterm' => $grade['midterm'],
                        'semi_finals' => $grade['semi_finals'],
                        'final' => $grade['final'],
                        'remarks' => $grade['remarks'],
                        'status' => 'Approved',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]);

                    // ✅ Remove student from classes_student
                    Classes_Student::where('studentID', $grade['studentID'])
                        ->where('classID', $grade['classID'])
                        ->delete();

                    // ✅ Remove student from final_grades after locking
                    DB::table('final_grade')
                        ->where('classID', $grade['classID'])
                        ->where('studentID', $grade['studentID'])
                        ->delete();

                    $classIDs[] = $grade['classID'];
                }
            }

            $user = Auth::user();

            $class = Classes::find($request->classID);

            // Get DEAN of the same department
            if (stripos($request->department, 'education') !== false) {
                $users = User::whereRaw('LOWER(department) LIKE ?', ['%education%'])->get();
            } else {
                $users = User::where('department', $request->department)->get();
            }

            $dean = $users->first(function ($user) {
                $roles = explode(',', $user->role); // assuming role is comma-separated
                return in_array('dean', array_map('trim', $roles));
            });

            // Get INSTRUCTOR by matching class instructor name
            $instructor = User::where('name', $class->instructor)->first();

            // Shared notification data
            $baseNotif = [
                'notif_type'      => 'Class grades have been approved by the registrar, please check your archive',
                'class_id'        => $class->id,
                'class_course_no' => $class->course_no,
                'class_descriptive_title' => $class->descriptive_title,
                'department'      => $user->department ?? null,
                'added_by_id'     => $user->studentID,
                'added_by_name'   => $user->name,
                'status_from_added'    => 'unchecked',
                'status_from_target'    => 'unchecked',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 🔔 Notify DEAN
            DB::table('notif_table')->insert(array_merge($baseNotif, [
                'target_by_id' => $dean->studentID ?? null,
                'target_by_name' => $dean->name ?? null,
            ]));

            // 🔔 Notify INSTRUCTOR
            DB::table('notif_table')->insert(array_merge($baseNotif, [
                'target_by_id' => $instructor->studentID ?? null,
                'target_by_name' => $instructor->name ?? null,
            ]));



            // ✅ Check if the class still has students
            $classHasStudents = Classes_Student::whereIn('classID', $classIDs)->exists();

            if (!$classHasStudents) {
                // If no students left, delete the class
                Classes::whereIn('id', $classIDs)->delete();
            }

            // Update class status to "Approved"
            Classes::where('id', $request->classID)->update(['status' => 'The Registrar approved the submitted grade of' . $selectedDepartment . 'department']);

            return redirect()->route('registrar_classes')->with('success', 'Final grades for ' . $selectedDepartment . ' have been submitted successfully!');
        }

        // ✅ Update only records matching classID and department
        DB::table('final_grade')
            ->where('classID', $request->classID)
            ->where('department', $request->department)
            ->update($updateData);


        return back()->with('success', 'Registrar’s decision has been submitted successfully!');
    }
}
