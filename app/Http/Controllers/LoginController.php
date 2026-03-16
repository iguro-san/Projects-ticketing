<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function proses(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Coba login
        if ($request->email == "admin@example.com" && $request->password == "123456") {
            // Simpan session login
            session(['logged_in' => true]);
            session(['user_email' => $request->email]);
            session(['user_name' => 'Admin']);
            
            // ARAHKAN KE DASHBOARD
            return redirect('/dashboard')->with('success', 'Login berhasil! Selamat datang di Dashboard');
        } else {
            return back()->with('error', 'Email atau password salah!');
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect('/login')->with('success', 'Logout berhasil!');
    }
}