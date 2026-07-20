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

        // Check if this is an email update request
        if ($request->has('password_confirm')) {
            return $this->updateEmail($request);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
        ]);

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // Update email with password confirmation
    public function updateEmail(Request $request)
    {
        $user = Auth::user();
        
        // Only allow regular users to update email
        if ($user->isAdmin() || $user->isPanitia()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password_confirm' => 'required',
        ]);

        if (!Hash::check($request->input('password_confirm'), $user->password)) {
            return back()->with('error', 'Kata sandi yang Anda masukkan salah.');
        }

        $oldEmail = $user->email;
        $user->email = $request->input('email');
        $user->save();

        return back()->with('success', "Email berhasil diperbarui dari {$oldEmail} menjadi {$user->email}.");
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
