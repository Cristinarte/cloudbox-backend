<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    use Queueable;

    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Usamos la URL que funcionaba originalmente, ajustada al puerto de tu frontend
        $url = 'http://localhost:5173/password-reset/' . $this->token . '?email=' . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Restablecer contraseña')
            ->greeting('¡Hola!')
            ->line('Recibiste este correo porque solicitaste restablecer tu contraseña.')
            ->action('Restablecer Contraseña', $url)
            ->line('Este enlace de restablecimiento expirará en 60 minutos.')
            ->line('Si no solicitaste este cambio, ignora este correo.')
            ->salutation('Saludos, Cloudbox')
            ->line('Si tienes problemas para hacer clic en el botón "Restablecer Contraseña", copia y pega la siguiente URL en tu navegador:')
            ->line($url);
    }
}