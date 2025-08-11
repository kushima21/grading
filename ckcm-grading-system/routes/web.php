<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\ClassArchiveController;
use App\Http\Controllers\ForgotPasswordController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('default', function () {
    return view('layouts.default');
});


Route::get('course', function () {
    return view('admin.course');
});

Route::get('my_class', function () {
    return view('instructor.my_class');
});

Route::get('classes_view', function () {
    return view('registrar.classes_view');
});

Route::get('classes', function () {
    return view('registrar.classes');
});

Route::get('allgrades', function () {
    return view('registrar.allgrades');
});

Route::get('studentlist', function () {
    return view('registrar.studentlist');
});

Route::get('/course', [CourseController::class, 'index'])->name('admin.index');
Route::post('/admin/course', [CourseController::class, 'store'])->name('course.store');
Route::get('/admin/course/{id}', [CourseController::class, 'edit'])->name('course.edit');
Route::put('/course/{id}', [CourseController::class, 'update'])->name('course.update');
Route::delete('/course/{id}', [CourseController::class, 'destroy'])->name('course.destroy');
Route::get('/users', [UserController::class, 'show'])->name('user.show');
Route::get('/course-search', [CourseController::class, 'search']);
Route::get('/instructor-search', [RegistrarController::class, 'searchInstructor']);

 
Route::get("/registrar_dashboard", [RegistrarController::class, "index"])->name('registrar');
Route::get("/classes", [RegistrarController::class, "registrar_classes"])->name('registrar_classes');
Route::post("/classes", [RegistrarController::class, "CreateClass"])->name('classes.create');
Route::get("/classes", [RegistrarController::class, "registrar_classes"])->name('registrar_classes');




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