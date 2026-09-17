<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Puerta propia del panel.
 *
 * Antes el panel compartía el formulario de la tienda, que es una página de
 * cliente: trae el bloque "Crear cuenta gratis" al lado y el acceso por
 * WhatsApp. Aquí sólo se entra con una cuenta de administrador activa; una
 * cuenta de cliente, aunque las credenciales sean correctas, no queda con
 * sesión abierta.
 */
class AdminAuthController extends Controller
{
    public function loginForm(Request $request): View|RedirectResponse
    {
        $usuario = $request->user();

        if ($usuario && $usuario->isAdmin() && $usuario->activo) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.acceso');
    }

    public function login(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credenciales, $request->boolean('remember'))) {
            return $this->rechazar($request, 'Las credenciales no son correctas.');
        }

        $usuario = $request->user();

        if (! $usuario->activo) {
            $this->cerrarSesion($request);

            return $this->rechazar($request, 'Esta cuenta está desactivada.');
        }

        if (! $usuario->isAdmin()) {
            $this->cerrarSesion($request);

            return $this->rechazar($request, 'Esta cuenta no tiene acceso al panel.');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->cerrarSesion($request);

        return redirect()->route('admin.login');
    }

    private function rechazar(Request $request, string $mensaje): RedirectResponse
    {
        return back()
            ->withErrors(['email' => $mensaje])
            ->withInput($request->only('email', 'remember'));
    }

    private function cerrarSesion(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
