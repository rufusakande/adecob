<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Le reporting est automatiquement géré par Laravel
        })->stop();

        // Gestion des erreurs 404
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ressource non trouvée',
                    'message' => 'La page ou la ressource que vous cherchez n\'existe pas.'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->view('errors.404', [], Response::HTTP_NOT_FOUND);
        });

        // Gestion des erreurs 403 (Forbidden)
        $this->renderable(function (HttpException $e, $request) {
            if ($e->getStatusCode() === Response::HTTP_FORBIDDEN) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Accès refusé',
                        'message' => $e->getMessage() ?: 'Vous n\'avez pas la permission d\'accéder à cette ressource.'
                    ], Response::HTTP_FORBIDDEN);
                }

                return response()->view('errors.403', ['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
            }
        });

        // Gestion générale des erreurs HTTP
        $this->renderable(function (HttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erreur HTTP',
                    'status' => $e->getStatusCode(),
                    'message' => $e->getMessage() ?: 'Une erreur s\'est produite lors du traitement de votre demande.'
                ], $e->getStatusCode());
            }

            return response()->view("errors.{$e->getStatusCode()}", 
                ['exception' => $e], 
                $e->getStatusCode()
            );
        });

        // Gestion générale des exceptions
        $this->renderable(function (Throwable $e, $request) {
            // En production, enregistrer l'erreur
            if (app()->environment('production')) {
                \Log::error('Exception non gérée', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'user_id' => auth()->id(),
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                ]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erreur serveur',
                    'message' => app()->environment('production') 
                        ? 'Une erreur est survenue. Veuillez contacter l\'administrateur.'
                        : $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->view('errors.500', 
                ['exception' => $e], 
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        });
    }
}
