<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && password_verify($credentials['password'], $user->password_hash)) {
            Auth::login($user);
            $request->session()->regenerate();

            // Redirigir según rol
            $rol = $user->rol->nombre;
            
            return match($rol) {
                'Administrativo' => redirect()->route('admin.dashboard'),
                'Farmaceutico' => redirect()->route('farmaceutico.dashboard'),
                'TecnicoFarmaceutico' => redirect()->route('tecnico.dashboard'),
                'EncargadoAlmacen' => redirect()->route('almacen.dashboard'),
                'Cliente' => redirect()->route('cliente.dashboard'),
                default => redirect('/'),
            };
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}