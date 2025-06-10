<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login', [
            'title' => 'Login',
            'authTheme' => 'bg-primary', // Warna tema AdminLTE
            'loginRoute' => 'login',
            'forgotPasswordRoute' => 'password.request'
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return $this->authenticated($request, Auth::user());
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    protected function authenticated(Request $request, $user)
    {
        // Tambahkan notifikasi toast
        session()->flash('toast_type', 'success');
        session()->flash('toast_message', 'Login berhasil!');

        return match ($user->role) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'kepsek' => redirect()->intended(route('kepsek.dashboard')),
            default => redirect()->intended(route('guru.dashboard'))
        };
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('toast_type', 'success')->with('toast_message', 'Logout berhasil!');
    }
}
