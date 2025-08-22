<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Tampilkan form login
    public function loginAuth()
    {
        return view('login');
    }

    // Proses login
    public function loginProcess(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard'); // arahkan ke dashboard
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Tampilkan form register
    // Tampilkan form register - HANYA ADMIN yang bisa akses
    public function registerAuth()
    {
        // Cek jika user adalah admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return view('auth.register');
        }

        abort(403, 'Unauthorized access');
    }

    // Proses register - HANYA ADMIN yang bisa akses
    public function registerProcess(Request $request)
    {
        // Cek jika user adalah admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,inputter,user',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect('/admin/users')->with('success', 'Akun berhasil dibuat!');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
