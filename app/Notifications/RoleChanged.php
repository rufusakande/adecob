<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoleChanged extends Notification
{
    use Queueable;

    private const ROLE_LABELS = [
        'super_admin'   => 'Super Administrateur',
        'commune_admin' => 'Administrateur de Commune',
        'agent'         => 'Agent Collecteur',
        'public_user'   => 'Utilisateur Public',
    ];

    public function __construct(
        private readonly string $newRole,
        private readonly ?string $communeName = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $roleLabel  = self::ROLE_LABELS[$this->newRole] ?? $this->newRole;
        $loginUrl   = route('login.form');
        $salutation = "Bonjour {$notifiable->prenom} {$notifiable->name},";

        $mail = (new MailMessage)
            ->subject('[' . config('app.name') . '] Votre rôle a été modifié')
            ->greeting($salutation)
            ->line("Votre rôle sur la plateforme **" . config('app.name') . "** vient d'être mis à jour.")
            ->line("**Nouveau rôle :** {$roleLabel}");

        // Message contextuel selon le nouveau rôle
        match ($this->newRole) {
            'super_admin' => $mail
                ->line('Vous disposez désormais d\'un accès complet à toute la plateforme : gestion des communes, des utilisateurs, des infrastructures et des logs d\'audit.')
                ->line('**Important :** Un code de vérification (MFA) vous sera demandé à chaque connexion.'),

            'commune_admin' => $mail
                ->line("Vous êtes désormais administrateur de la commune **{$this->communeName}**.")
                ->line('Vos accès incluent : validation des saisies de vos agents, planification des réhabilitations, et approbation des nouvelles inscriptions de votre commune.')
                ->line('**Important :** Un code de vérification (MFA) vous sera demandé à chaque connexion.'),

            'agent' => $mail
                ->line('Vous avez le rôle d\'agent collecteur.')
                ->line('Vous pouvez saisir les données d\'infrastructure et consulter votre tableau de bord.'),

            default => $mail->line("Votre nouveau rôle vous a été attribué par un administrateur."),
        };

        // Avertissement de déconnexion
        $mail
            ->line('---')
            ->line('⚠️ **Votre session précédente a été fermée** pour sécuriser l\'accès à votre nouveau rôle. Veuillez vous reconnecter.')
            ->action('Se connecter à ' . config('app.name'), $loginUrl)
            ->line('Si vous n\'êtes pas à l\'origine de cette demande, contactez immédiatement votre administrateur.');

        return $mail;
    }
}
