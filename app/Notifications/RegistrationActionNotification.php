<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationActionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $action;
    protected $actionBy;

    public function __construct($user, $action, $actionBy)
    {
        $this->user = $user;
        $this->action = $action;
        $this->actionBy = $actionBy;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $action = $this->action === 'approved' ? 'approuvée' : 'rejetée';
        
        return (new MailMessage)
            ->subject("Inscription $action - ADECOB")
            ->greeting('Bonjour ' . $notifiable->name)
            ->line("L'inscription de {$this->user->name} a été $action par {$this->actionBy->name}.")
            ->line('Détails :')
            ->line('Nom : ' . $this->user->name)
            ->line('Email : ' . $this->user->email)
            ->line('Date d\'inscription : ' . $this->user->created_at->format('d/m/Y H:i'))
            ->action('Voir la liste des utilisateurs', url('/admin/users'));
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Inscription de {$this->user->name} {$this->action} par {$this->actionBy->name}",
            'user_id' => $this->user->id,
            'action' => $this->action,
            'action_by' => $this->actionBy->id
        ];
    }
}