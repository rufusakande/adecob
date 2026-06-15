<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    /**
     * Critères de mot de passe robuste:
     * - Minimum 10 caractères
     * - Au moins une lettre majuscule
     * - Au moins une lettre minuscule
     * - Au moins un chiffre
     * - Au moins un caractère spécial (!@#$%^&*()_+-=[]{}|;:,.<>?)
     */

    private $failedRules = [];

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Réinitialiser les règles échouées
        $this->failedRules = [];

        // Vérifier la longueur minimale
        if (strlen($value) < 10) {
            $this->failedRules[] = 'minimum 10 caractères';
        }

        // Vérifier la présence d'une majuscule
        if (!preg_match('/[A-Z]/', $value)) {
            $this->failedRules[] = 'au moins une lettre majuscule';
        }

        // Vérifier la présence d'une minuscule
        if (!preg_match('/[a-z]/', $value)) {
            $this->failedRules[] = 'au moins une lettre minuscule';
        }

        // Vérifier la présence d'un chiffre
        if (!preg_match('/[0-9]/', $value)) {
            $this->failedRules[] = 'au moins un chiffre (0-9)';
        }

        // Vérifier la présence d'un caractère spécial
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $value)) {
            $this->failedRules[] = 'au moins un caractère spécial (!@#$%^&*...)';
        }

        // Si des règles ont échoué, générer le message d'erreur
        if (!empty($this->failedRules)) {
            $message = 'Le mot de passe doit contenir: ' . implode(', ', $this->failedRules);
            $fail($message);
        }
    }

    /**
     * Obtenir les règles échouées
     */
    public function getFailedRules(): array
    {
        return $this->failedRules;
    }
}
