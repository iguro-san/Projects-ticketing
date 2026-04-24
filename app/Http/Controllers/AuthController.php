<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login user
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Coba login
        if (Auth::attempt($credentials, $request->remember)) {
            // Regenerate session untuk keamanan
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect berdasarkan role
            $redirectTo = match($user->role) {
                'admin' => route('admin.dashboard'),
                'panitia' => route('panitia.dashboard'),
                default => route('home')
            };
            
            $roleName = ucfirst($user->role);
            
            return redirect($redirectTo)
                ->with('success', "Selamat datang, {$roleName} {$user->name}!");
        }

        // Login gagal
        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->withInput($request->only('email'));
    }

    /**
     * Menampilkan halaman register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi user baru
     */
    public function register(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Buat user baru
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user', // Default role adalah user
        ]);

        // Langsung login setelah register
        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Registrasi berhasil! Selamat datang di Event Management System.');
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        // Invalidate session
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();
        
        return redirect('/')
            ->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Menampilkan halaman forgot password (opsional)
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses forgot password (opsional)
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Di sini Anda bisa menambahkan logika reset password
        // Contoh: kirim email reset password

        return back()
            ->with('success', 'Link reset password telah dikirim ke email Anda.');
    }

    /**
     * Menampilkan halaman reset password (opsional)
     */
    public function showResetPassword($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Proses reset password (opsional)
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Login user
        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Password berhasil direset!');
    }
}