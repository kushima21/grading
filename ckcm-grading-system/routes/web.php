<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AuthController;
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

Route::get('/course', [CourseController::class, 'index'])->name('admin.index');
Route::post('/admin/course', [CourseController::class, 'store'])->name('course.store');
Route::get('/admin/course/{id}', [CourseController::class, 'edit'])->name('course.edit');
Route::put('/course/{id}', [CourseController::class, 'update'])->name('course.update');
Route::delete('/course/{id}', [CourseController::class, 'destroy'])->name('course.destroy');






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