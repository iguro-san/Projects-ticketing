<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function authenticate(Request $request)
    {
        // Simulasi validasi sederhana
        $email = $request->input('email');
        return "Login berhasil untuk email: " . $email;
    }
}