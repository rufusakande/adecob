<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationStatus extends Notification
{
    use Queueable;

    protected $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Statut de votre inscription - ADECOB');

        switch ($this->status) {
            case 'pending':
                return $message
                    ->greeting('Bonjour ' . $notifiable->name)
                    ->line('Votre inscription sur la plateforme ADECOB a été reçue avec succès.')
                    ->line('Un administrateur va examiner votre demande dans les plus brefs délais.')
                    ->line('Vous recevrez une notification dès que votre compte sera validé.');
            case 'approved':
                return $message
                    ->greeting('Félicitations ' . $notifiable->name . ' !')
                    ->line('Votre inscription sur la plateforme ADECOB a été validée.')
                    ->action('Connectez-vous maintenant', url('/login'))
                    ->line('Vous pouvez désormais accéder à toutes les fonctionnalités de la plateforme.');
            case 'rejected':
                return $message
                    ->greeting('Bonjour ' . $notifiable->name)
                    ->error()
                    ->line('Nous sommes désolés de vous informer que votre inscription a été rejetée.')
                    ->line('Si vous pensez qu\'il s\'agit d\'une erreur, veuillez nous contacter.')
                    ->action('Nous contacter', url('/contact'));
            default:
                return $message
                    ->line('Mise à jour du statut de votre compte.');
        }
    }

    public function toArray($notifiable)
    {
        return [
            'status' => $this->status,
            'user_id' => $notifiable->id
        ];
    }
}
