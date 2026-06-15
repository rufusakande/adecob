<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ErrorHandlingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $response = $next($request);
            return $response;
        } catch (\Exception $e) {
            // Enregistrer l'erreur de façon structurée
            $this->logError($request, $e);

            // Retourner une réponse appropriée
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erreur de traitement',
                    'message' => app()->environment('production')
                        ? 'Une erreur interne s\'est produite.'
                        : $e->getMessage(),
                    'trace_id' => $this->getTraceId(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Rediriger vers la page d'erreur
            return redirect()->back()->with('error', 
                app()->environment('production')
                    ? 'Une erreur est survenue. Un identifiant de trace a été généré pour le suivi.'
                    : $e->getMessage()
            );
        }
    }

    /**
     * Enregistre l'erreur avec des détails structurés
     */
    private function logError(Request $request, \Exception $e): void
    {
        $traceId = $this->getTraceId();
        
        $context = [
            'trace_id' => $traceId,
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ];

        // Ajouter les données POST si disponibles (sauf les données sensibles)
        if ($request->method() !== 'GET') {
            $safeData = $request->except(['password', 'password_confirmation', 'token', 'secret']);
            $context['input'] = $safeData;
        }

        // Log l'erreur
        \Log::error('Exception non gérée - Trace ID: ' . $traceId, $context);

        // Enregistrer dans un fichier spécifique pour faciliter le débogage
        if (app()->environment('local', 'testing')) {
            $this->writeErrorLog($traceId, $context, $e);
        }
    }

    /**
     * Génère un ID unique de trace pour le suivi
     */
    private function getTraceId(): string
    {
        return uniqid('TRACE_') . '_' . now()->timestamp;
    }

    /**
     * Écrit l'erreur dans un fichier de log détaillé
     */
    private function writeErrorLog(string $traceId, array $context, \Exception $e): void
    {
        $logDir = storage_path('logs/errors');
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/' . date('Y-m-d') . '_errors.json';
        
        $logEntry = [
            'trace_id' => $traceId,
            'timestamp' => now()->toIso8601String(),
            'context' => $context,
            'trace' => $e->getTraceAsString(),
        ];

        $content = json_encode($logEntry, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($logFile, $content, FILE_APPEND);
    }
}
