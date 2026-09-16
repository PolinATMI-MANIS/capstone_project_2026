<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Cek apakah role user sesuai dengan role yang diizinkan
        if (in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        // Jika role tidak sesuai, tampilkan halaman 403 (Forbidden)
        abort(403, 'Kamu tidak memiliki akses ke halaman ini.');
    }
}