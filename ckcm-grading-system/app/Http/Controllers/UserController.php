<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $query = User::query();

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Apply department filter
        if ($request->has('department') && !empty($request->department)) {
            $query->where('department', $request->department);
        }

        // Apply role filter
        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }

        $users = $query->get();

        if ($request->ajax()) {
            return response()->json(['users' => $users]);
        }

        // Get unique departments and roles for filters
        $departments = User::select('department')->distinct()->pluck('department');
        $department = DB::table('departments')->pluck('department_name'); // Assuming your department table has a 'name' column
        $roles = User::select('role')->distinct()->pluck('role');

        foreach ($users as $user) {
            if (str_contains($user->role, 'student')) { // ✅ Check if 'student' exists in the role string
                $user->grades = DB::table('grade_logs')
                    ->select('course_no', 'descriptive_title', 'units', 'academic_period', 'academic_year', 'prelim', 'midterm', 'semi_finals', 'final', 'remarks', 'created_at')
                    ->where('studentID', $user->studentID)
                    ->get();
            }
        }



        return view('users.user', compact('users', 'departments', 'roles', 'department'));
    }

    public function editUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'gender' => 'required',
            'department' => 'required|string',
            'roles' => 'required|string', // Expect a JSON string
        ]);

        try {
            $user = User::where('email', $request->email)->firstOrFail();
            $oldName = $user->name;

            $user->studentID = $request->user_id;
            $user->name = $request->name;
            $user->gender = $request->gender;
            $user->email = $request->email;
            $user->department = $request->department;
            $rolesArray = json_decode($request->roles, true);
            $user->role = implode(',', $rolesArray); // Store roles as comma-separated

            $user->save();

            // Explode roles and check if user is an instructor
            $rolesExploded = array_map('trim', explode(',', $user->role));
            if (in_array('instructor', $rolesExploded)) {
                $tables = [
                    'archived_final_grades',
                    'archived_quizzesandscores',
                    'classes',
                    'final_grade',
                    'grade_logs'
                ];
                foreach ($tables as $table) {
                    DB::table($table)
                        ->where('instructor', $oldName)
                        ->update(['instructor' => $request->name]);
                }
            }

            return redirect(route("user.show"))->with("success", "User Updated Successfully");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update user. ' . $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $userId = $request->input('user_id');  // Get the user ID from the form

        // Find and delete the user
        $user = User::where('studentID', $userId);
        $user->delete();

        // Redirect back with a success message (or do whatever logic you want)
        return redirect()->route('user.show')->with('success', 'User deleted successfully!');
    }

}