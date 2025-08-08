<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DeanController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AllGradesController;
use App\Http\Controllers\StudentsGradeController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ClassArchiveController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CompleteCredentialController;
use App\Models\ClassArchive;
use Illuminate\Contracts\Routing\Registrar;

/*
|--------------------------------------------------------------------------
| Guest Routes (For Users Not Logged In)
|--------------------------------------------------------------------------
|
| These routes are only accessible to guests. If a user is logged in,
| they will be redirected to their dashboard instead.
|
*/

Route::middleware(['guest'])->group(function () {
    Route::get("/login", [AuthController::class, "login"])->name('login');

    Route::get("/register", [AuthController::class, 'register'])->name('register');

    Route::post("/register/security", function (\Illuminate\Http\Request $request) {
        $securityPassword = 'register_password'; // <-- Set your password here

        if ($request->input('security_code') === $securityPassword) {
            session(['register_access' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Incorrect security code.'], 403);
    })->name('register.security');



    Route::post("/login", [AuthController::class, 'LoginPost'])->name('login.post');
    Route::post("/register", [AuthController::class, 'RegisterPost'])->name('register.post');

    // Google OAuth Routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    // Forgot & Reset Password Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (For Logged-in Users)
|--------------------------------------------------------------------------
|
| These routes are only accessible when logged in. If the user is not
| logged in, they will be redirected to the login page automatically.
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get("/", [IndexController::class, "index"])->name("index");

    Route::post('/profile/update', [CompleteCredentialController::class, 'update'])->name('profile.update');

    // for mygrades
    Route::get('/my_grades', [StudentsGradeController::class, 'show'])->name('my_grades');

    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');



    // Admin
    Route::get("/index", [AdminController::class, "index"])->name('admin');
    Route::delete('/admin/department/{id}', [AdminController::class, 'deleteDepartment'])->name('admin.delete_department');


    Route::post('/admin/verify-archive-password', function (\Illuminate\Http\Request $request) {
        // Set your built-in password here (e.g., in .env or hardcoded)
        $builtInPassword = env('ARCHIVE_SECTION_PASSWORD', 'admin_dashboard');
        if ($request->password !== $builtInPassword) {
            return response()->json(['success' => false], 403);
        }
        return response()->json(['success' => true]);
    })->name('admin.verify_archive_password');


    // Registrar
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

    Route::post('/lockedfinalgrade', [RegistrarController::class, 'LockInGrades'])->name('finalgrade.lock');
    Route::post('/savefinalgrade', [RegistrarController::class, 'SubmitGrades'])->name('finalgrade.save');
    Route::post('/savefinalgradetoregistrar', [RegistrarController::class, 'SubmitGradesRegistrar'])->name('finalgraderegistrar.save');
    Route::post('/unlockfinalgrade', [RegistrarController::class, 'UnlockGrades'])->name('finalgrade.unlock');
    Route::post('/initializegrade', [RegistrarController::class, 'initializeGrades'])->name('initialize.grade');
    Route::post('/submitfinalgrades', [RegistrarController::class, 'submitFinalGrades'])->name('submit.finalgrade');
    Route::post('/students/import/{class}', [RegistrarController::class, 'importCSV'])->name('students.import');


    Route::delete('/grades/{id}', [AllGradesController::class, 'destroy'])->name('grades.destroy');


    Route::post('/finalgrade/decision', [RegistrarController::class, 'submitDecision'])->name('finalgrade.decision');
    Route::post('/finalgrade/decisionregistrar', [RegistrarController::class, 'submitDecisionRegistrar'])->name('finalgraderegistrar.decision');
    // for action access
    Route::post('/class/verify-password', [RegistrarController::class, 'verifyPassword'])->name('class.verifyPassword');


    // for instructor
    Route::get("/my_class", [InstructorController::class, "index"])->name('instructor.my_class');
    Route::get("/my_class_archive", [ClassArchiveController::class, "index"])->name('instructor.my_class_archive');



    Route::get('/allgrades', [AllGradesController::class, 'index'])->name('show.grades');
    Route::get('/api/grades', [AllGradesController::class, 'getGrades']);

    Route::get('/users', [UserController::class, 'show'])->name('user.show');
    Route::post('/users', [UserController::class, 'editUser'])->name('user.edituser');
    Route::delete('/users', [UserController::class, 'destroy'])->name('user.destroy');


    Route::get("/admin", [AdminController::class, "index"])->name('admin.show');
    Route::post('/admin', [AdminController::class, 'addDepartment'])->name('admin.add_department');


    Route::get('/course', [CourseController::class, 'index'])->name('course.show');
    Route::post('/course', [CourseController::class, 'addCourse'])->name('admin.add_course');

    Route::get('/registrar/classes', [RegistrarController::class, 'showClasses'])->name('registrar.classes');



    // Dean
    Route::get("/dean_dashboard", [DeanController::class, "index"])->name('dean');

    // Instructor
    Route::get("/instructor_dashboard", [InstructorController::class, "index"])->name('instructor');
    Route::get("/instructor_classes", [InstructorController::class, "classes"])->name('classes');

    // for pdf
    Route::get('/generate-pdf', [PDFController::class, 'generatePDF']);

    Route::post('/instructor/generate-gradesheet-pdf', [ClassArchiveController::class, 'generateGradeSheetPDF'])->name('instructor.generate_gradesheet_pdf');

    // Logout Route
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/login')->with('success', 'You have been logged out successfully.');
    })->name('logout');

    // 👇 Place this at the bottom of web.php

    Route::delete('/admin/archived_quiz/{id}', function ($id) {
        DB::table('archived_quizzesandscores')->where('id', $id)->delete();
        return back()->with('success', 'Archived quiz/score deleted.');
    })->name('admin.delete_archived_quiz');

    Route::delete('/admin/archived_final_grade/{id}', function ($id) {
        DB::table('archived_final_grades')->where('id', $id)->delete();
        return back()->with('success', 'Archived final grade deleted.');
    })->name('admin.delete_archived_final_grade');
});

// Optimize application
Route::get('/command/optimize', function () {
    Artisan::call('optimize');
    return 'Optimization command executed';
});




// TODO: FOR DEVELOPMENT ONLY; will be removed in production
// Route::get('/command/migrate', function () {
//     Artisan::call('migrate --force');
//     return 'Migration command executed';
// });