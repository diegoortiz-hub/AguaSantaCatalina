<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function loginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('mi-cuenta'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ])->withInput($request->only('email', 'remember'));
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'                => ['required', 'string', 'max:255'],
            'register_email'        => ['required', 'email', 'max:255', 'unique:users,email'],
            'telefono'              => ['nullable', 'string', 'max:20'],
            'password'              => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'nombre'   => $request->nombre,
            'email'    => $request->register_email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'rol'      => 'cliente',
            'activo'   => true,
        ]);

        Auth::login($user);

        return redirect()->route('mi-cuenta');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
