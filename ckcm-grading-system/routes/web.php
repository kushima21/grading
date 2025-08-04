<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('default', function () {
    return view('layouts.default');
});


Route::get('course', function () {
    return view('admin.course');
});