<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDeleted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param string $userName  Nom de l'utilisateur dont le compte a été supprimé
     * @param string $deletedBy Nom de l'administrateur à l'origine de la suppression
     */
    public function __construct(
        protected string $userName,
        protected string $deletedBy
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre compte a été supprimé - ' . config('app.name'))
            ->greeting('Bonjour ' . $this->userName . ',')
            ->line('Votre compte sur la plateforme **' . config('app.name') . '** a été supprimé.')
            ->line('Si vous pensez qu\'il s\'agit d\'une erreur ou si vous souhaitez obtenir plus d\'informations, veuillez nous contacter.')
            ->action('Nous contacter', url('/contact'))
            ->line('Merci de votre compréhension.');
    }
}
