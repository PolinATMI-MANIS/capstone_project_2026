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

        $user = Auth::user();
        $userRoleRaw = $user->role ?? 'user';

        // Normalisasi role user (ubah ke lowercase dan ganti spasi/dash jadi underscore)
        $userRoleNormalized = strtolower(str_replace([' ', '-'], '_', trim($userRoleRaw)));

        // Normalisasi daftar role yang diizinkan dari parameter rute
        $allowedRoles = array_map(function($role) {
            return strtolower(str_replace([' ', '-'], '_', trim($role)));
        }, $roles);

        // Cek apakah role yang dinormalisasi ada di dalam daftar yang diizinkan
        if (in_array($userRoleNormalized, $allowedRoles)) {
            return $next($request);
        }

        // Tampilkan 403 beserta info role user saat ini untuk debugging
        abort(403, "Akses ditolak! Role Anda saat ini adalah: [ {$userRoleRaw} ], sedangkan yang dibutuhkan: [ " . implode(', ', $roles) . " ]");
    }
}