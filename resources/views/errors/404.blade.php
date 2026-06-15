@extends('layouts.app')

@section('title', '404 - Page Non Trouvée')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 mt-5">
                <div class="card-body text-center py-5">
                    <div style="font-size: 4rem; color: #ffd700; margin-bottom: 20px;">
                        <i class="fas fa-map"></i>
                    </div>
                    <h1 class="card-title" style="color: #ffd700;">404 - Page Non Trouvée</h1>
                    <p class="card-text text-muted mt-3" style="font-size: 1.1rem;">
                        Oups ! La page que vous cherchez n'existe pas ou a été supprimée.
                    </p>
                    <hr class="my-4">
                    <p class="text-muted mb-4">
                        <i class="fas fa-search"></i> 
                        Vérifiez l'URL et réessayez, ou utilisez le bouton ci-dessous pour revenir à l'accueil.
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
                    <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger" 
                       onclick="document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
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

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

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
