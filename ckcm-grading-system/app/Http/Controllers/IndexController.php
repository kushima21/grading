<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;

class IndexController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $needsProfileUpdate = false;

        if ($user->role == 'student') {
            if (is_null($user->studentID) || is_null($user->department) || is_null($user->gender)) {
                $needsProfileUpdate = true;
            }
        }

        $departments = Department::all(); // fetch all departments

        return view('index', compact('user', 'needsProfileUpdate', 'departments'));
    }
}
