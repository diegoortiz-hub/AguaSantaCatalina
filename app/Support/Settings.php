<?php

namespace App\Support;

class Settings
{
    private ?array $data = null;

    public function all(): array
    {
        return $this->data ??= array_merge($this->defaults(), $this->stored());
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function save(array $values): void
    {
        $merged = array_merge($this->all(), $values);
        file_put_contents(
            $this->path(),
            json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        $this->data = $merged;
    }

    /**
     * Subconjunto seguro para inyectar en el navegador. Deja fuera los datos
     * bancarios y de facturación que sólo debe ver el admin.
     */
    public function publicos(): array
    {
        return [
            'despacho' => [
                'gratis_desde' => (int) $this->get('despacho_gratis'),
                'estandar'     => (int) $this->get('despacho_estandar'),
                'express'      => (int) $this->get('despacho_express'),
            ],
            'whatsapp' => $this->whatsappNumero(),
        ];
    }

    /** Número de WhatsApp normalizado a sólo dígitos, listo para wa.me. */
    public function whatsappNumero(): string
    {
        return preg_replace('/\D+/', '', (string) $this->get('whatsapp'));
    }

    public function defaults(): array
    {
        return [
            'empresa'           => 'Aguas Purificadas Santa Catalina',
            'rut'               => '76.000.000-0',
            'email'             => 'contacto@aguassantacatalina.cl',
            'telefono'          => '+56 9 8149 3272',
            'direccion'         => 'Av. Purificación 1234',
            'comuna'            => 'Santiago',
            'ciudad'            => 'Santiago',
            'horario'           => 'Lun–Sáb 08:00–18:00',
            'banco'             => 'Banco Estado',
            'tipo_cuenta'       => 'Cuenta Corriente',
            'nro_cuenta'        => '000000000',
            'titular'           => 'Aguas Purificadas Santa Catalina',
            'rut_titular'       => '76.000.000-0',
            'email_pagos'       => 'pagos@aguassantacatalina.cl',
            'whatsapp'          => '+56981493272',
            'despacho_gratis'   => 15000,
            'despacho_estandar' => 2500,
            'despacho_express'  => 3990,
        ];
    }

    private function stored(): array
    {
        if (! file_exists($this->path())) {
            return [];
        }

        return json_decode(file_get_contents($this->path()), true) ?? [];
    }

    private function path(): string
    {
        return storage_path('app/settings.json');
    }
}
