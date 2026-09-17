<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
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
            // Una cuenta desactivada no debe poder entrar ni a la tienda.
            // Hasta ahora sólo el panel lo comprobaba, así que un cliente
            // dado de baja seguía viendo sus pedidos.
            if (! $request->user()->activo) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Esta cuenta está desactivada. Escríbenos si crees que es un error.',
                ])->withInput($request->only('email', 'remember'));
            }

            $request->session()->regenerate();

            // Cada rol aterriza donde trabaja. Se mantiene intended() para no
            // perder la página que el usuario pedía antes de que lo mandáramos
            // a iniciar sesión.
            return redirect()->intended(
                $request->user()->isAdmin() ? route('admin.dashboard') : route('mi-cuenta')
            );
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

    // ── Recuperación de contraseña ────────────────────────────────────────

    public function forgotForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        PasswordBroker::sendResetLink($request->only('email'));

        // Se responde igual exista o no la cuenta: distinguirlas permitiría
        // averiguar qué correos están registrados en la tienda.
        return back()->with('status', 'Si el correo está registrado, te enviamos un enlace para crear una nueva contraseña. Revisa tu bandeja.');
    }

    public function resetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $status = PasswordBroker::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                     ->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === PasswordBroker::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('status', 'Tu contraseña quedó actualizada. Ya puedes ingresar.');
        }

        return back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
