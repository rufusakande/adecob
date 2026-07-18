@extends('layouts.app')

@section('title', '419 - Page Expirée')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 mt-5">
                <div class="card-body text-center py-5">
                    <div style="font-size: 4rem; color: #ffd700; margin-bottom: 20px;">
                        <i class="fas fa-hourglass-end"></i>
                    </div>
                    <h1 class="card-title" style="color: #ffd700;">419 - Page Expirée</h1>
                    <p class="card-text text-muted mt-3" style="font-size: 1.1rem;">
                        Oups ! Votre session a expiré en raison d'une inactivité prolongée.
                    </p>
                    <hr class="my-4">
                    <p class="text-muted mb-4">
                        <i class="fas fa-sync-alt"></i> 
                        Veuillez recharger la page et réessayer.
                    </p>
                    <div class="mt-4">
                        <a href="javascript:window.location.reload(true)" class="btn btn-primary btn-lg mr-2">
                            <i class="fas fa-redo"></i> Recharger la page
                        </a>
                        <a href="javascript:history.back()" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Page précédente
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pages populaires -->
            <div class="mt-4 text-center">
                <p class="text-muted mb-3"><strong>Pages utiles :</strong></p>
                <div>
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                    <a href="{{ route('contact.form') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-envelope"></i> Contact
                    </a>
                </div>
            </div>

            <!-- Support -->
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
