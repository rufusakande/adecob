<?php

// Script de test pour l'envoi d'email
// À exécuter: php test_email.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Support\Str;

echo "\n";
echo "====== Test d'Envoi d'Email ======\n";
echo "\n";

// Afficher la configuration
echo "Configuration Mail:\n";
echo "  Mailer: " . config('mail.default') . "\n";
echo "  Host: " . config('mail.mailers.smtp.host') . "\n";
echo "  Port: " . config('mail.mailers.smtp.port') . "\n";
echo "  Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
echo "  Username: " . config('mail.mailers.smtp.username') . "\n";
echo "  From Address: " . config('mail.from.address') . "\n";
echo "\n";

// Récupérer le premier utilisateur
$user = User::first();

if (!$user) {
    echo "❌ Erreur: Aucun utilisateur trouvé dans la base de données!\n";
    exit(1);
}

echo "Utilisateur trouvé:\n";
echo "  Nom: " . $user->name . "\n";
echo "  Email: " . $user->email . "\n";
echo "\n";

// Tester l'envoi
try {
    echo "➤ Tentative d'envoi d'un email de réinitialisation...\n";
    
    $token = Str::random(64);
    $user->notify(new PasswordResetNotification($token, $user->email));
    
    echo "\n✅ Email envoyé avec succès!\n";
    echo "   Vérifiez votre boîte de réception: " . $user->email . "\n";
    echo "\n";
    
    exit(0);
} catch (\Exception $e) {
    echo "\n❌ Erreur lors de l'envoi:\n";
    echo "   Message: " . $e->getMessage() . "\n";
    echo "\n";
    
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    echo "\n";
    
    exit(1);
}
