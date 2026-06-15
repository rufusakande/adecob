<?php

namespace App\Services;

/**
 * Service pour gérer la politique de mots de passe robuste
 */
class PasswordPolicy
{
    // Critères de la politique
    const MIN_LENGTH = 10;
    const REQUIRE_UPPERCASE = true;
    const REQUIRE_LOWERCASE = true;
    const REQUIRE_NUMBERS = true;
    const REQUIRE_SPECIAL_CHARS = true;
    const SPECIAL_CHARS = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    /**
     * Vérifie si un mot de passe respecte la politique
     * 
     * @param string $password Le mot de passe à vérifier
     * @return array ['valid' => bool, 'errors' => array, 'strength' => string]
     */
    public static function validate(string $password): array
    {
        $errors = [];
        $passedChecks = 0;
        $totalChecks = 5;

        // Vérifier la longueur minimale
        if (strlen($password) < self::MIN_LENGTH) {
            $errors[] = "Le mot de passe doit contenir au minimum " . self::MIN_LENGTH . " caractères";
        } else {
            $passedChecks++;
        }

        // Vérifier la présence d'une majuscule
        if (self::REQUIRE_UPPERCASE && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une lettre majuscule (A-Z)";
        } else {
            $passedChecks++;
        }

        // Vérifier la présence d'une minuscule
        if (self::REQUIRE_LOWERCASE && !preg_match('/[a-z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une lettre minuscule (a-z)";
        } else {
            $passedChecks++;
        }

        // Vérifier la présence d'un chiffre
        if (self::REQUIRE_NUMBERS && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre (0-9)";
        } else {
            $passedChecks++;
        }

        // Vérifier la présence d'un caractère spécial
        if (self::REQUIRE_SPECIAL_CHARS && !preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un caractère spécial (" . self::SPECIAL_CHARS . ")";
        } else {
            $passedChecks++;
        }

        // Calculer la force du mot de passe
        $strength = self::calculateStrength($passedChecks, $totalChecks);

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'strength' => $strength,
            'passed_checks' => $passedChecks,
            'total_checks' => $totalChecks,
            'percentage' => round(($passedChecks / $totalChecks) * 100)
        ];
    }

    /**
     * Calcule le niveau de force du mot de passe
     */
    private static function calculateStrength(int $passedChecks, int $totalChecks): string
    {
        $percentage = ($passedChecks / $totalChecks) * 100;

        if ($percentage === 100) {
            return 'très-fort';
        } elseif ($percentage >= 80) {
            return 'fort';
        } elseif ($percentage >= 60) {
            return 'moyen';
        } elseif ($percentage >= 40) {
            return 'faible';
        } else {
            return 'très-faible';
        }
    }

    /**
     * Obtient les critères de la politique
     */
    public static function getCriteria(): array
    {
        return [
            [
                'label' => 'Longueur minimale',
                'requirement' => 'Au moins ' . self::MIN_LENGTH . ' caractères',
                'regex' => '.{' . self::MIN_LENGTH . ',}',
                'icon' => 'fa-ruler'
            ],
            [
                'label' => 'Majuscules',
                'requirement' => 'Au moins une lettre majuscule (A-Z)',
                'regex' => '[A-Z]',
                'icon' => 'fa-arrow-up'
            ],
            [
                'label' => 'Minuscules',
                'requirement' => 'Au moins une lettre minuscule (a-z)',
                'regex' => '[a-z]',
                'icon' => 'fa-arrow-down'
            ],
            [
                'label' => 'Chiffres',
                'requirement' => 'Au moins un chiffre (0-9)',
                'regex' => '[0-9]',
                'icon' => 'fa-hashtag'
            ],
            [
                'label' => 'Caractères spéciaux',
                'requirement' => 'Au moins un caractère spécial (!@#$%^&*...)',
                'regex' => '[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]',
                'icon' => 'fa-keyboard'
            ]
        ];
    }

    /**
     * Obtient les messages d'erreur formatés en HTML
     */
    public static function getErrorsHtml(array $errors): string
    {
        if (empty($errors)) {
            return '';
        }

        $html = '<div class="alert alert-danger" role="alert"><ul class="mb-0">';
        foreach ($errors as $error) {
            $html .= '<li>' . htmlspecialchars($error) . '</li>';
        }
        $html .= '</ul></div>';

        return $html;
    }

    /**
     * Génère un mot de passe robuste d'exemple
     */
    public static function generateExample(): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $password = '';
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $numbers[rand(0, strlen($numbers) - 1)];
        $password .= $special[rand(0, strlen($special) - 1)];

        // Ajouter des caractères aléatoires pour atteindre 10
        $allChars = $uppercase . $lowercase . $numbers . $special;
        for ($i = 0; $i < 6; $i++) {
            $password .= $allChars[rand(0, strlen($allChars) - 1)];
        }

        // Mélanger
        $password = str_shuffle($password);

        return $password;
    }
}
