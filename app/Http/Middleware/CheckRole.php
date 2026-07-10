<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Jika user adalah admin dan mencoba akses panitia/user, redirect ke admin dashboard
        if ($user->isAdmin() && in_array('panitia', $roles)) {
            return redirect()->route('admin.dashboard');
        }
        
        // Jika user adalah panitia dan mencoba akses admin/user, redirect ke panitia dashboard
        if ($user->isPanitia() && in_array('admin', $roles)) {
            return redirect()->route('panitia.dashboard');
        }
        
        // Jika user adalah user biasa dan mencoba akses admin/panitia, redirect ke home
        if ($user->isUser() && (in_array('admin', $roles) || in_array('panitia', $roles))) {
            return redirect()->route('home');
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}