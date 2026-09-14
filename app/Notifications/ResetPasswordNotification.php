<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $minutos = Config::get('auth.passwords.users.expire', 60);

        $url = route('password.reset', ['token' => $this->token])
            . '?email=' . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Recupera tu contraseña — Aguas Santa Catalina')
            ->view('emails.reset-password', [
                'nombre'  => $notifiable->nombre,
                'url'     => $url,
                'minutos' => $minutos,
            ]);
    }
}
