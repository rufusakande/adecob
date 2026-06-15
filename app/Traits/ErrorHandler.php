<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait ErrorHandler
{
    /**
     * Gère les exceptions avec logging et réponse appropriée
     */
    protected function handleException(\Exception $e, Request $request, string $redirectRoute = null)
    {
        // Log l'exception
        \Log::error('Exception dans ' . class_basename($this), [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'user_id' => auth()->id(),
            'url' => $request->url(),
            'method' => $request->method(),
        ]);

        // Réponse JSON si demandée
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur de traitement',
                'message' => app()->environment('production') 
                    ? 'Une erreur est survenue lors du traitement de votre demande.'
                    : $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Redirection avec message
        if ($redirectRoute) {
            return redirect()->route($redirectRoute)->with('error', 
                app()->environment('production')
                    ? 'Une erreur est survenue. Veuillez réessayer.'
                    : $e->getMessage()
            );
        }

        return back()->with('error', 
            app()->environment('production')
                ? 'Une erreur est survenue. Veuillez réessayer.'
                : $e->getMessage()
        );
    }

    /**
     * Valide les autorisations d'accès
     */
    protected function ensureAuthorization($user, bool $isCommuneAdmin = false, bool $isSuperAdmin = false)
    {
        if (!$user) {
            abort(403, 'Vous devez être connecté.');
        }

        if ($isCommuneAdmin && !$user->isCommuneAdmin()) {
            abort(403, 'Vous devez être administrateur de commune pour accéder à cette ressource.');
        }

        if ($isSuperAdmin && !$user->isSuperAdmin()) {
            abort(403, 'Vous devez être super administrateur pour accéder à cette ressource.');
        }

        return $user;
    }

    /**
     * Validations avec messages personnalisés en français
     */
    protected function validateWithCustomMessages(Request $request, array $rules, array $messages = [])
    {
        $defaultMessages = [
            'required' => 'Le champ :attribute est obligatoire.',
            'email' => 'Le champ :attribute doit être une adresse email valide.',
            'unique' => 'La valeur du champ :attribute existe déjà.',
            'min' => 'Le champ :attribute doit contenir au minimum :min caractères.',
            'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
            'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
            'regex' => 'Le format du champ :attribute est invalide.',
        ];

        $messages = array_merge($defaultMessages, $messages);

        return $request->validate($rules, $messages);
    }

    /**
     * Enregistre une action pour audit
     */
    protected function logAction(string $action, string $model, $modelId, array $data = [])
    {
        \Log::info('Action: ' . $action, [
            'user_id' => auth()->id(),
            'model' => $model,
            'model_id' => $modelId,
            'data' => $data,
            'timestamp' => now(),
        ]);
    }
}
