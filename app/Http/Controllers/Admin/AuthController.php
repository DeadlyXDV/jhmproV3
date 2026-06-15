<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function loginForm(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        $user = Auth::guard('admin')->user();

        // Blokir customer yang coba login di sini
        if ($user->role === 'customer') {
            Auth::guard('admin')->logout();

            return back()->withErrors([
                'email' => 'Akun tidak ditemukan.',
            ])->onlyInput('email');
        }

        if (! $user->is_active) {
            Auth::guard('admin')->logout();

            return back()->withErrors([
                'email' => 'Akun tidak aktif, hubungi administrator.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Sync web guard session so Fortify-protected routes also work
        Auth::guard('web')->loginUsingId($user->id, $request->boolean('remember'));

        // Redirect mekanik ke panel mekanik
        if ($user->role === 'mekanik') {
            return redirect()->route('mekanik.dashboard');
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
