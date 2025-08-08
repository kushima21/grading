<?php

namespace App\Http\Controllers;
use App\Models\Classes;
use App\Models\User;

use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function index()
    {
        $user = auth()->user(); // Get the logged-in instructor
        $classes = Classes::where('instructor', $user->name)->get(); // Fetch only their classes

        return view('instructor.my_class', compact('classes'));
    }



}