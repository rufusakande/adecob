@extends('layouts.app')

@section('title', '500 - Erreur Serveur')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 mt-5">
                <div class="card-body text-center py-5">
                    <div style="font-size: 4rem; color: #dc3545; margin-bottom: 20px;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h1 class="card-title" style="color: #dc3545;">500 - Erreur Serveur</h1>
                    <p class="card-text text-muted mt-3" style="font-size: 1.1rem;">
                        Une erreur interne du serveur s'est produite. Nos équipes ont été notifiées.
                    </p>
                    <hr class="my-4">
                    <p class="text-muted mb-4">
                        <i class="fas fa-tools"></i> 
                        Notre équipe technique travaille déjà à la résolution du problème. Veuillez réessayer dans quelques instants.
                    </p>
                    
                    @if (app()->environment('local', 'testing') && isset($exception))
                        <div class="alert alert-danger text-start mt-4" style="max-height: 300px; overflow-y: auto;">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-bug"></i> Détails de l'erreur (Mode développement)
                            </h6>
                            <small>
                                <strong>Exception :</strong> {{ get_class($exception) }}<br>
                                <strong>Message :</strong> {{ $exception->getMessage() }}<br>
                                <strong>Fichier :</strong> {{ $exception->getFile() }}<br>
                                <strong>Ligne :</strong> {{ $exception->getLine() }}
                                
                                @if ($exception->getPrevious())
                                    <hr class="my-2">
                                    <strong>Cause :</strong> {{ $exception->getPrevious()->getMessage() }}
                                @endif
                            </small>
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg mr-2">
                            <i class="fas fa-home"></i> Retour à l'accueil
                        </a>
                        <a href="javascript:location.reload()" class="btn btn-secondary btn-lg">
                            <i class="fas fa-redo"></i> Réessayer
                        </a>
                    </div>
                </div>
            </div>

            <!-- Support -->
            <div class="mt-4 text-center">
                <p class="text-muted mb-3">
                    <strong>Si le problème persiste :</strong>
                </p>
                <small class="text-muted">
                    Contactez l'équipe d'ADECOB à 
                    <a href="mailto:secretariatadecob@yahoo.fr">secretariatadecob@yahoo.fr</a>
                    ou appelez <a href="tel:+22901956473">0195647373</a><br>
                    <strong>Horaires :</strong> Lun - Ven : 08h - 12h30 et 15h - 17h30
                </small>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .card {
        border-radius: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }

    .btn {
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .alert {
        border-radius: 8px;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
    }
</style>
@endsection
