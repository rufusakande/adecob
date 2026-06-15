@extends('layouts.app')

@section('title', 'Tableau de Bord - ' . $commune->name)

@section('content')
<div class="container-fluid mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">
                    <i class="fas fa-chart-line"></i> Tableau de Bord - {{ $commune->name }}
                </h1>
                <a href="{{ route('commune-admin.details') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> Voir les détails
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h4 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Erreur</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    <div class="row">
        <!-- Statistiques -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0" style="border-left: 5px solid #2e8b57;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Infrastructures</p>
                            <h3 class="mb-0" style="color: #2e8b57;">{{ $stats['total_infrastructures'] }}</h3>
                        </div>
                        <div class="text-muted" style="font-size: 2rem;">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0" style="border-left: 5px solid #ffd700;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Travaux en cours</p>
                            <h3 class="mb-0" style="color: #ffd700;">{{ $stats['active_works'] }}</h3>
                        </div>
                        <div class="text-muted" style="font-size: 2rem;">
                            <i class="fas fa-hammer"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0" style="border-left: 5px solid #dc3545;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Agents de mairie</p>
                            <h3 class="mb-0" style="color: #dc3545;">{{ $stats['total_agents'] }}</h3>
                        </div>
                        <div class="text-muted" style="font-size: 2rem;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Actions rapides -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0"><i class="fas fa-cogs"></i> Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('commune-admin.access-code.edit') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-key"></i> Gérer le code d'accès</span>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="{{ route('infrastructures.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-list"></i> Voir les infrastructures</span>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="{{ route('mairie-agent.dashboard') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-chart-bar"></i> Tableau de bord agents</span>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations de la commune -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations de la commune</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nom :</dt>
                        <dd class="col-sm-8">{{ $commune->name }}</dd>

                        <dt class="col-sm-4">Code :</dt>
                        <dd class="col-sm-8">
                            <code>{{ $commune->code }}</code>
                        </dd>

                        <dt class="col-sm-4">Région :</dt>
                        <dd class="col-sm-8">{{ $commune->region ?? 'Non définie' }}</dd>

                        <dt class="col-sm-4">Département :</dt>
                        <dd class="col-sm-8">{{ $commune->department ?? 'Non défini' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .list-group-item-action:hover {
        background-color: #f8f9fa;
        border-left: 4px solid #2e8b57;
    }
</style>
@endsection
