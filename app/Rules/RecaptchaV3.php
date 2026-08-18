<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class RecaptchaV3 implements ValidationRule
{
    protected $score;
    protected $action;

    /**
     * Créer une nouvelle instance de la règle
     *
     * @param float $minScore Score minimum accepté (0-1)
     * @param string $action Action reCAPTCHA attendue
     */
    public function __construct($minScore = 0.5, $action = 'submit')
    {
        $this->score = $minScore;
        $this->action = $action;
    }

    /**
     * Valider le token reCAPTCHA
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Vérifier que le token existe
        if (empty($value)) {
            $fail('Le reCAPTCHA est requis. Veuillez recharger et réessayer.');
            return;
        }

        try {
            // Vérifier le token avec Google (avec timeout de 10 secondes)
            $response = Http::asForm()
                ->timeout(10)
                ->connectTimeout(5)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $value,
                ]);

            // Vérifier si la réponse est valide (status 200)
            if (!$response->successful()) {
                \Log::error('RecaptchaV3 API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                $fail('Erreur de vérification de sécurité. Veuillez réessayer.');
                return;
            }

            $body = $response->json();

            // Vérifier la réponse
            if (!($body['success'] ?? false)) {
                $errorCodes = $body['error-codes'] ?? [];

                \Log::warning('RecaptchaV3 Failed', [
                    'body' => $body,
                    'action' => $this->action
                ]);

                // Erreurs de CONFIGURATION (clé non enregistrée pour le domaine actuel, clé
                // secrète invalide, ...) : on NE bloque PAS les utilisateurs légitimes, sinon
                // toute l'équipe serait bloquée tant que la config n'est pas corrigée.
                // On journalise fortement pour que l'admin ajoute le domaine à la clé dans la
                // console Google. Dès que la configuration est corrigée, la protection redevient
                // totale automatiquement (ce bloc ne sera plus atteint).
                $configErrors = [
                    'invalid-domain',           // domaine non autorisé pour cette clé (cas fréquent)
                    'invalid-site-secret-key',  // clé secrète invalide
                    'invalid-keypair',          // les deux clés ne correspondent pas
                    'missing-input-secret',     // clé secrète manquante
                    'bad-request',              // requête mal formée
                ];

                $isOnlyConfigError = !empty($errorCodes)
                    && count(array_diff($errorCodes, $configErrors)) === 0;

                if ($isOnlyConfigError) {
                    \Log::error('RecaptchaV3 Config Error — CONNEXION OUVERTE (fail-open). Ajoutez le domaine actuel à la clé reCAPTCHA dans la console Google (https://www.google.com/recaptcha/admin) pour rétablir la protection anti-bot.', [
                        'error-codes' => $errorCodes,
                        'action' => $this->action
                    ]);
                    return; // Accepte la soumission : une erreur de config ne doit pas bloquer les utilisateurs
                }

                $fail('La vérification reCAPTCHA a échoué. Veuillez réessayer.');
                return;
            }

            // Vérifier que l'action correspond — uniquement si Google la renvoie.
            // (Les clés de TEST officielles ne renvoient pas l'action ; on ne bloque alors pas.)
            if (!empty($body['action']) && $body['action'] !== $this->action) {
                \Log::warning('RecaptchaV3 Action Mismatch', [
                    'expected' => $this->action,
                    'received' => $body['action'] ?? null
                ]);
                $fail('Vérification reCAPTCHA invalide.');
                return;
            }

            // Vérifier le score — uniquement si Google en fournit un.
            // (Les clés de TEST officielles renvoient success=true sans score → on accepte.)
            $receivedScore = $body['score'] ?? null;

            if ($receivedScore === null) {
                \Log::info('RecaptchaV3 Success (sans score — clés de test)', [
                    'action' => $this->action
                ]);
                return;
            }

            \Log::info('RecaptchaV3 Score', [
                'score' => $receivedScore,
                'threshold' => $this->score,
                'action' => $this->action
            ]);

            if ($receivedScore < $this->score) {
                // Déterminer si c'est probablement un bot
                $isLikelyBot = $receivedScore < 0.3;
                if ($isLikelyBot) {
                    $fail('Activité suspecte détectée. Veuillez réessayer plus tard.');
                } else {
                    $fail('Vérification reCAPTCHA insuffisante. Veuillez réessayer.');
                }
                return;
            }

            // Succès! Le score est bon et l'action est correcte
            \Log::info('RecaptchaV3 Success', [
                'score' => $receivedScore,
                'action' => $this->action
            ]);

        } catch (\Illuminate\Http\Client\ConnectException $e) {
            \Log::error('RecaptchaV3 Connection Error', [
                'message' => $e->getMessage()
            ]);
            $fail('Impossible de vérifier la sécurité. Veuillez vérifier votre connexion Internet et réessayer.');
            return;

        } catch (\Illuminate\Http\Client\RequestTimeoutException $e) {
            \Log::error('RecaptchaV3 Timeout', [
                'message' => $e->getMessage()
            ]);
            $fail('Vérification de sécurité trop lente. Veuillez réessayer.');
            return;

        } catch (\Exception $e) {
            \Log::error('RecaptchaV3 Unexpected Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $fail('Erreur lors de la vérification de sécurité. Veuillez réessayer.');
            return;
        }
    }
}
