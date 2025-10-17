<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // if (Auth::attempt($credentials)) {
        //     return redirect()->route('welcome'); // after login, redirect to users page
        // }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // 👇 Check if profile is created or not
            if (!$user->profile_created) {
                return redirect()->route('profile.create')
                                 ->with('info', 'Please complete your profile before continuing.');
            }

            // ✅ If profile already created, go to dashboard
            return redirect()->route('welcome');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    
//password

public function changePasswordForm()
{
    return view('users.change-password');
}

public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:6|confirmed',
    ]);

    $user = Auth::user();

    // Check current password
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect']);
    }

    // Update password
    $user->password = Hash::make($request->new_password);
    $user->save();

    return redirect()->route('users.index')->with('success', 'Password updated successfully!');
}


}