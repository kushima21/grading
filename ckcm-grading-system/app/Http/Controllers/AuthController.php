<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login()
    {
        return view("auth.login");
    }

    function LoginPost(Request $request)
    {
        $request->validate([
            "login" => "required",
            "password" => "required",
        ]);

        $login = $request->login;
        $password = $request->password;

        // Check if the login input is an email or a studentID (numeric)
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        // Use separate queries for email and studentID to avoid type issues
        if ($isEmail) {
            $user = User::where('email', $login)->first();
        } else {
            // Try to find by studentID - ensure it's treated as numeric
            $user = User::where('studentID', $login)->first();
        }

        if (!$user) {
            return back()->withErrors(['login' => 'No account found for the provided email or student ID.']);
        }

        // Check for email domain if login was via email
        if ($isEmail && !preg_match('/@ckcm\.edu\.ph$/', $login)) {
            return back()->withErrors(['login' => 'Only @ckcm.edu.ph emails are allowed.']);
        }

        if (!Hash::check($password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        Auth::login($user);
        return redirect()->route('index')->with('success', 'Login Success');
    }


    public function register()
    {
        $departments = Department::all(); // Fetch all departments from the departments table
        return view('auth.register', compact('departments')); // Pass the departments to the register view
    }

    function RegisterPost(Request $request)
    {
        $request->validate([
            "studentID" => "required",
            "name" => "required",
            "gender" => "required",
            "email" => "required|email",
            "department" => "",
            "password" => "required|min:4",
            "confirm_password" => "required|same:password",
            "role" => "required",
        ]);

        $user = new User();
        $user->studentID = $request->studentID;
        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->email = $request->email;
        $user->department = $request->department;
        $user->password = $request->password;
        $user->role = $request->role;

        if($user->save()){
            return redirect(route("login"))->with("success", "Account Created Successfully");
        }

        return redirect(route("register"))->with("error", "Account Creation Failed");

    }

    // Google login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Google callback
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if the Google email domain is allowed
            if (!preg_match('/@ckcm\.edu\.ph$/', $googleUser->getEmail())) {
                return redirect('login')->withErrors(['login' => 'Only @ckcm.edu.ph emails are allowed.']);
            }

            // Find or create user
            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                // Check if user exists with same email
                $user = User::where('email', $googleUser->getEmail())->first();

                if (!$user) {
                    // Create new user with default values
                    $user = new User();
                    $user->name = $googleUser->getName();
                    $user->email = $googleUser->getEmail();
                    $user->role = 'student';
                    $user->password = Hash::make(rand(100000, 999999)); // Random password
                }

                // Update Google specific fields
                $user->google_id = $googleUser->getId();
                $user->google_token = $googleUser->token;


                // Save the avatar/profile picture safely
                if ($googleUser->getAvatar()) {
                    $user->avatar = $googleUser->getAvatar();
                }

                $user->save();
            } else {
                // Update avatar on login if changed
                if ($googleUser->getAvatar() && $user->avatar !== $googleUser->getAvatar()) {
                    $user->avatar = $googleUser->getAvatar();
                    $user->save();
                }
            }

            Auth::login($user);
            return redirect()->route('index')->with('success', 'Google Login Success');

        } catch (\Exception $e) {
            // Log the full error for debugging
            Log::error('Google auth error: ' . $e->getMessage());
            return redirect('login')->withErrors(['error' => 'Google authentication failed. Please try again or contact support.']);

        }
    }
}
