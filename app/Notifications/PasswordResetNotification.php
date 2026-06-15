<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    protected $token;
    protected $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->email,
        ], false));

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe - ADECOB')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Vous avez demandé la réinitialisation de votre mot de passe.')
            ->line('Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe.')
            ->action('Réinitialiser mon mot de passe', $resetUrl)
            ->line('Ce lien expirera dans 60 minutes.')
            ->line('Si vous n\'avez pas demandé la réinitialisation de votre mot de passe, ignorez simplement ce message.')
            ->line('Si le bouton ne fonctionne pas, vous pouvez copier le lien suivant dans votre navigateur:')
            ->line($resetUrl);
    }

    public function toArray($notifiable)
    {
        return [
            'token' => $this->token,
            'email' => $this->email,
        ];
    }
}
