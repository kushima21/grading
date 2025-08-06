<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // Retrieve all departments
        $departments = Department::all();

        $archivedQuizzes = DB::table('archived_quizzesandscores')->get();
        $archivedFinalGrades = DB::table('archived_final_grades')->get();

        return view('admin.admin_dashboard', compact('departments', 'archivedQuizzes', 'archivedFinalGrades'));
    }

    public function addDepartment(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|max:255',
        ]);

        Department::insert([
            'department_name' => $request->department_name,
        ]);

        return redirect()->route('admin.show')->with('success', 'Department added successfully!');
    }

    public function deleteDepartment(Request $request, $id)
    {
        $request->validate([
            'admin_password' => 'required|string',
        ]);

        // Check if password matches the logged-in user's password
        if (!Hash::check($request->admin_password, Auth::user()->password)) {
            return redirect()->route('admin.show')->with('error', 'Incorrect password. Department not deleted.');
        }

        Department::where('id', $id)->delete();
        return redirect()->route('admin.show')->with('success', 'Department deleted successfully!');
    }
}
