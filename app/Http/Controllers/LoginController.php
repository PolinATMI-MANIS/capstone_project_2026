<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        // 1. Validasi data yang dikirim dari form
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Cek ke database, apakah email dan password cocok?
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Jika berhasil, arahkan ke halaman dashboard
            return redirect()->intended('/dashboard');
        }

        // 3. Jika salah, kembalikan ke halaman login dengan pesan error
        return back()->with('error', 'Yah, Email atau Password kamu salah nih!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}