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
                \Log::warning('RecaptchaV3 Failed', [
                    'body' => $body,
                    'action' => $this->action
                ]);
                $fail('La vérification reCAPTCHA a échoué. Veuillez réessayer.');
                return;
            }

            // Vérifier que l'action correspond
            if (($body['action'] ?? null) !== $this->action) {
                \Log::warning('RecaptchaV3 Action Mismatch', [
                    'expected' => $this->action,
                    'received' => $body['action'] ?? null
                ]);
                $fail('Vérification reCAPTCHA invalide.');
                return;
            }

            // Vérifier le score
            $receivedScore = $body['score'] ?? 0;
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
