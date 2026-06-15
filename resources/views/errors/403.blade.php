@extends('layouts.app')

@section('title', '403 - Accès Refusé')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 mt-5">
                <div class="card-body text-center py-5">
                    <div style="font-size: 4rem; color: #dc3545; margin-bottom: 20px;">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h1 class="card-title" style="color: #dc3545;">403 - Accès Refusé</h1>
                    <p class="card-text text-muted mt-3" style="font-size: 1.1rem;">
                        {{ isset($message) && $message ? $message : 'Désolé, vous n\'avez pas la permission d\'accéder à cette ressource.' }}
                    </p>
                    <hr class="my-4">
                    <p class="text-muted mb-4">
                        <i class="fas fa-info-circle"></i> 
                        Si vous pensez que c'est une erreur, veuillez contacter l'administrateur du système.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg mr-2">
                            <i class="fas fa-home"></i> Retour à l'accueil
                        </a>
                        <a href="javascript:history.back()" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Page précédente
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informations de support -->
            <div class="mt-4 text-center">
                <small class="text-muted">
                    <strong>Besoin d'aide ?</strong><br>
                    Contactez l'équipe d'ADECOB à 
                    <a href="mailto:secretariatadecob@yahoo.fr">secretariatadecob@yahoo.fr</a>
                    ou appelez <a href="tel:+22901956473">0195647373</a>
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
</style>
@endsection
