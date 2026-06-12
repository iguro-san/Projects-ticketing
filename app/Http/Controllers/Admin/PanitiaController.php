<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PanitiaController extends Controller
{
    public function index()
    {
        $panitia = User::where('role', 'panitia')
            ->withCount('events')
            ->latest()
            ->paginate(10);

        return view('admin.panitia.index', compact('panitia'));
    }

    public function create()
    {
        return view('admin.panitia.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'panitia'
        ]);

        return redirect()->route('admin.panitia.index')
            ->with('success', 'Akun panitia berhasil ditambahkan.');
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'panitia') {
            return back()->with('error', 'User bukan panitia.');
        }

        if ($user->events()->count() > 0) {
            return back()->with('error', 'Panitia tidak dapat dihapus karena masih memiliki event.');
        }

        $user->delete();

        return back()->with('success', 'Akun panitia berhasil dihapus.');
    }
}