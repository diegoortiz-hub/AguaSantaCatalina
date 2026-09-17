<?php

namespace App\Providers;

use App\Services\Tributario\EmisorDocumentos;
use App\Services\Tributario\EmisorManual;
use App\Support\Settings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Settings::class);

        // El emisor de boletas y facturas se elige por configuración. Si el
        // nombre configurado no existe, se cae al manual en vez de reventar:
        // un pedido no debe fallar por un valor mal escrito en el .env.
        $this->app->bind(EmisorDocumentos::class, function () {
            $emisores = config('tributario.emisores', []);
            $elegido  = config('tributario.emisor');

            return $this->app->make($emisores[$elegido] ?? EmisorManual::class);
        });
    }

    public function boot(): void
    {
        View::share('ajustes', $this->app->make(Settings::class));
    }
}
