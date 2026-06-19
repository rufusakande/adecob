<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MfaCodeNotification extends Notification
{
    use Queueable;

    public function __construct(protected string $code, protected int $ttlMinutes = 10) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Code de vérification ADECOB')
            ->greeting('Bonjour ' . ($notifiable->prenom ?? $notifiable->name))
            ->line('Une connexion à votre espace administrateur ADECOB nécessite une vérification supplémentaire.')
            ->line('Votre code de vérification est :')
            ->line('**' . $this->code . '**')
            ->line('Ce code est valable ' . $this->ttlMinutes . ' minutes.')
            ->line("Si vous n'êtes pas à l'origine de cette connexion, changez immédiatement votre mot de passe et contactez l'équipe ADECOB.");
    }
}
