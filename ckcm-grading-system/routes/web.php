<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CourseController,
    AuthController,
    UserController,
    RegistrarController,
    ClassArchiveController,
    ForgotPasswordController,
    IndexController,
    InstructorController,
    AllGradesController,
    StudentsGradeController
};

// --------------------
// DEFAULT ROUTES
// --------------------
Route::get('/', function () {
    return view('auth.login');
});

Route::get('default', fn() => view('layouts.default'));

// --------------------
// STATIC VIEW ROUTES (Consider removing duplicates with controllers later)
// --------------------
Route::view('course', 'admin.course');
Route::view('my_class', 'instructor.my_class');
Route::view('classes_view', 'registrar.classes_view');
Route::view('classes', 'registrar.classes');
Route::view('allgrades', 'registrar.allgrades');
Route::view('studentlist', 'registrar.studentlist');
Route::view('my_class_archive', 'instructor.my_class_archive');

// --------------------
// COURSE MANAGEMENT (Admin)
// --------------------
Route::get('/course', [CourseController::class, 'index'])->name('admin.index');
Route::post('/admin/course', [CourseController::class, 'store'])->name('course.store');
Route::get('/admin/course/{id}', [CourseController::class, 'edit'])->name('course.edit');
Route::put('/course/{id}', [CourseController::class, 'update'])->name('course.update');
Route::delete('/course/{id}', [CourseController::class, 'destroy'])->name('course.destroy');
Route::get('/course-search', [CourseController::class, 'search']);
Route::get('/instructor-search', [RegistrarController::class, 'searchInstructor']);

// --------------------
// REGISTRAR DASHBOARD & CLASS MANAGEMENT
// --------------------
Route::get("/registrar_dashboard", [RegistrarController::class, "index"])->name('registrar');
Route::get("/classes", [RegistrarController::class, "registrar_classes"])->name('registrar_classes');
Route::post("/classes", [RegistrarController::class, "CreateClass"])->name('classes.create');
Route::put("/registrar_dashboard/{class}", [RegistrarController::class, "EditClass"])->name('classes.update');
Route::delete("/registrar_dashboard/{class}", [RegistrarController::class, "DeleteClass"])->name('classes.destroy');
Route::get('/classes/{class}', [RegistrarController::class, 'show'])->name('class.show');
Route::post('/classes/class={class}', [RegistrarController::class, 'addstudent'])->name('class.addstudent');
Route::delete('/classes/class={class}/student={student}', [RegistrarController::class, 'removestudent'])->name('class.removestudent');
Route::put('/classes/class={class}', [RegistrarController::class, 'addPercentageAndScores'])->name('class.addPercentageAndScores');
Route::get('/quizzesadded/class={class}', [RegistrarController::class, 'show'])->name('class.quizzes');
Route::put('/quizzesadded/class={class}', [RegistrarController::class, 'addQuizAndScore'])->name('class.addquizandscore');

// --------------------
// GRADE MANAGEMENT
// --------------------
Route::post('/lockedfinalgrade', [RegistrarController::class, 'LockInGrades'])->name('finalgrade.lock');
Route::post('/savefinalgrade', [RegistrarController::class, 'SubmitGrades'])->name('finalgrade.save');
Route::post('/savefinalgradetoregistrar', [RegistrarController::class, 'SubmitGradesRegistrar'])->name('finalgraderegistrar.save');
Route::post('/unlockfinalgrade', [RegistrarController::class, 'UnlockGrades'])->name('finalgrade.unlock');
Route::post('/initializegrade', [RegistrarController::class, 'initializeGrades'])->name('initialize.grade');
Route::post('/submitfinalgrades', [RegistrarController::class, 'submitFinalGrades'])->name('submit.finalgrade');
Route::post('/students/import/{class}', [RegistrarController::class, 'importCSV'])->name('students.import');

// Delete a grade
Route::delete('/grades/{id}', [AllGradesController::class, 'destroy'])->name('grades.destroy');

// --------------------
// AUTHENTICATION (Guest only)
// --------------------
Route::middleware(['guest'])->group(function () {

    // Login & Register
    Route::get("/login", [AuthController::class, "login"])->name('login');
    Route::get("/register", [AuthController::class, 'register'])->name('register');

    // Registration security code
    Route::post("/register/security", function (\Illuminate\Http\Request $request) {
        $securityPassword = 'register_password'; // <-- Change to secure value

        if ($request->input('security_code') === $securityPassword) {
            session(['register_access' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Incorrect security code.'], 403);
    })->name('register.security');

    Route::post("/login", [AuthController::class, 'LoginPost'])->name('login.post');
    Route::post("/register", [AuthController::class, 'RegisterPost'])->name('register.post');

    // Google OAuth
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    // Forgot & Reset Password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

// --------------------
// MAIN PAGE & LOGOUT
// --------------------
Route::get("/", [IndexController::class, "index"])->name("index");
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login')->with('success', 'You have been logged out successfully.');
})->name('logout');

// --------------------
// INSTRUCTOR DASHBOARD
// --------------------
Route::get("/instructor_dashboard", [InstructorController::class, "index"])->name('instructor');
Route::get("/instructor_classes", [InstructorController::class, "classes"])->name('classes');
Route::get("/my_class", [InstructorController::class, "index"])->name('instructor.my_class');
Route::get("/my_class_archive", [ClassArchiveController::class, "index"])->name('instructor.my_class_archive');

// --------------------
// STUDENT GRADES
// --------------------
Route::get('/my_grades', [StudentsGradeController::class, 'show'])->name('my_grades');

// FIX: AllGradesController index method missing
// Change 'index' to 'show' if that's the correct method
Route::get('/allgrades', [AllGradesController::class, 'show'])->name('show.grades');

// API endpoint for grades
Route::get('/api/grades', [AllGradesController::class, 'getGrades']);
