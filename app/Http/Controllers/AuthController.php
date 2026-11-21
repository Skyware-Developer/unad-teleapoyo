<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Solo usuarios activos pueden ingresar.
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password'], 'active' => true])) {
            $request->session()->regenerate(); // regenera el identificador de sesión (ASVS V3)
            AuditLog::record('login');

            return redirect()->intended(route('home'));
        }

        AuditLog::record('login_failed', null, null, null);

        // Mensaje genérico: no revela si el correo existe.
        return back()->withErrors(['email' => 'Credenciales inválidas.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        AuditLog::record('logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
