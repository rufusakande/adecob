<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegistration extends Notification implements ShouldQueue
{
    use Queueable;

    protected $newUser;

    public function __construct($user)
    {
        $this->newUser = $user;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $url = url('/admin/pending-registrations');
        
        return (new MailMessage)
            ->subject('Nouvelle inscription en attente - ADECOB')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('Un nouvel utilisateur vient de s\'inscrire sur la plateforme ADECOB.')
            ->line('Informations de l\'utilisateur :')
            ->line('Nom : ' . $this->newUser->name)
            ->line('Email : ' . $this->newUser->email)
            ->action('Voir les inscriptions en attente', $url)
            ->line('Merci de traiter cette demande d\'inscription.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Nouvelle inscription de ' . $this->newUser->name,
            'user_id' => $this->newUser->id
        ];
    }
}
