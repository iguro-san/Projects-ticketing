<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{

    // Show account management page
    public function index()
    {
        $user = Auth::user();
        
        // Only allow regular users to access account management
        if ($user->isAdmin() || $user->isPanitia()) {
            abort(403, 'Unauthorized');
        }
        
        return view('user.account', compact('user'));
    }

    // Update profile data (name, email, phone)
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Only allow regular users to update profile
        if ($user->isAdmin() || $user->isPanitia()) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
        ]);

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // Change password
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        // Only allow regular users to change password
        if ($user->isAdmin() || $user->isPanitia()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->with('error', 'Password saat ini salah.');
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return back()->with('success', 'Password berhasil diubah.');
    }
}
