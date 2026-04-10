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

        // Simulasi database user dengan role
        $users = [
            'admin@example.com' => ['password' => '123456', 'name' => 'Admin', 'role' => 'admin'],
            'user@example.com' => ['password' => '123456', 'name' => 'User Biasa', 'role' => 'user'],
        ];

        if (isset($users[$request->email]) && $users[$request->email]['password'] == $request->password) {
            session([
                'logged_in' => true,
                'user_email' => $request->email,
                'user_name' => $users[$request->email]['name'],
                'user_role' => $users[$request->email]['role']
            ]);
            
            // Redirect berdasarkan role
            if ($users[$request->email]['role'] == 'admin') {
                return redirect('/admin/dashboard')->with('success', 'Login berhasil! Selamat datang Admin');
            }
            
            return redirect('/')->with('success', 'Login berhasil! Selamat datang');
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