<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestEmailSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email? : Email to send test to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? config('mail.from.address');

        $this->line('');
        $this->line('<info>====== Test d\'Envoi d\'Email ======</info>');
        $this->line('');
        
        // Afficher la configuration
        $this->table(['Configuration', 'Valeur'], [
            ['Mailer', config('mail.default')],
            ['Host', config('mail.mailers.smtp.host')],
            ['Port', config('mail.mailers.smtp.port')],
            ['Encryption', config('mail.mailers.smtp.encryption')],
            ['Username', config('mail.mailers.smtp.username')],
            ['From Address', config('mail.from.address')],
            ['From Name', config('mail.from.name')],
            ['Email Cible', $email],
        ]);

        $this->line('');
        $this->info('➤ Tentative d\'envoi d\'un email de test...');
        $this->line('');

        try {
            // Générer un token de test
            $token = Str::random(64);

            // Créer ou trouver un utilisateur de test
            $user = User::whereEmail($email)->first();

            if (!$user) {
                $this->error("❌ Erreur: Aucun utilisateur trouvé avec l'email: $email");
                return 1;
            }

            // Envoyer la notification
            $this->comment('Envoi de la notification de réinitialisation de mot de passe...');
            $user->notify(new PasswordResetNotification($token, $email));

            $this->line('');
            $this->info('✅ Email envoyé avec succès!');
            $this->line('');
            $this->info('L\'email de réinitialisation a été envoyé à: <fg=green>' . $email . '</>');
            $this->line('Vérifiez votre boîte de réception (et les spams).');
            $this->line('');

            return 0;

        } catch (\Exception $e) {
            $this->line('');
            $this->error('❌ Erreur lors de l\'envoi de l\'email:');
            $this->error($e->getMessage());
            $this->line('');
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());
            $this->line('');

            return 1;
        }
    }
}
