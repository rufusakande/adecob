@extends('layouts.app')
@section('title', 'Données des infrastructures - ' . $commune->name)
@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Alertes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- En-tête -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">
                <i class="fas fa-building me-2 text-success"></i>
                {{ $commune->name }} - Statistiques des Infrastructures
            </h2>
            <p class="text-muted mb-0">Consultation publique des données et statistiques des infrastructures</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('home') }}" class="btn btn-secondary d-flex align-items-center gap-2">
                <i class="fas fa-arrow-left"></i> Retour à l'accueil
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-gradient text-white">
            <h5 class="mb-0">
                <i class="fas fa-chart-line me-2"></i> 
                Statistiques des Infrastructures
            </h5>
        </div>
        <div class="card-body p-4">
            <!-- Statistiques principales -->
            <div class="row g-4 mb-4">
                <!-- Total -->
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="display-6 text-primary mb-2">{{ $stats['total'] }}</div>
                            <h6 class="text-primary mb-0">Total Infrastructures</h6>
                        </div>
                    </div>
                </div>
                
                <!-- Planifiées -->
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="display-6 text-info mb-2">{{ $stats['planned'] }}</div>
                            <h6 class="text-info mb-0">Planifiées</h6>
                        </div>
                    </div>
                </div>
                
                <!-- À Entretenir -->
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="display-6 text-warning mb-2">{{ $stats['to_maintain'] }}</div>
                            <h6 class="text-warning mb-0">À Entretenir</h6>
                        </div>
                    </div>
                </div>
                
                <!-- Déjà Entretenues -->
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="display-6 text-success mb-2">{{ $stats['maintained'] }}</div>
                            <h6 class="text-success mb-0">Entretenues</h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Répartitions détaillées -->
            <div class="row g-4">
                <!-- Par Commune -->
                <div class="col-md-6 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-city me-2 text-success"></i> Par Commune
                            </h6>
                            <div style="max-height: 200px; overflow-y: auto;">
                                @foreach($stats['by_commune'] as $item)
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                        <small class="text-dark">{{ $item->commune ?? 'N/A' }}</small>
                                        <span class="badge bg-primary rounded-pill">{{ $item->count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Par Secteur -->
                <div class="col-md-6 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-industry me-2 text-success"></i> Par Secteur
                            </h6>
                            <div style="max-height: 200px; overflow-y: auto;">
                                @foreach($stats['by_secteur'] as $item)
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                        <small class="text-dark">{{ $item->secteur_domaine ?? 'N/A' }}</small>
                                        <span class="badge bg-success rounded-pill">{{ $item->count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Par Type -->
                <div class="col-md-6 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-tools me-2 text-success"></i> Par Type
                            </h6>
                            <div style="max-height: 200px; overflow-y: auto;">
                                @foreach($stats['by_type'] as $item)
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                        <small class="text-dark" title="{{ $item->type_infrastructure }}">{{ Str::limit($item->type_infrastructure ?? 'N/A', 15) }}</small>
                                        <span class="badge bg-info rounded-pill">{{ $item->count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Par État -->
                <div class="col-md-6 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-heartbeat me-2 text-success"></i> Par État
                            </h6>
                            <div style="max-height: 200px; overflow-y: auto;">
                                @foreach($stats['by_etat'] as $item)
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                        <small class="text-dark">{{ $item->etat_fonctionnement ?? 'N/A' }}</small>
                                        <span class="badge bg-warning rounded-pill">{{ $item->count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Par Dégradation -->
                <div class="col-md-6 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-exclamation-triangle me-2 text-success"></i> Par Dégradation
                            </h6>
                            <div style="max-height: 200px; overflow-y: auto;">
                                @foreach($stats['by_niveau'] as $item)
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                        <small class="text-dark">{{ $item->niveau_degradation ?? 'N/A' }}</small>
                                        <span class="badge bg-danger rounded-pill">{{ $item->count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progression -->
                <div class="col-md-6 col-lg-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-chart-pie me-2 text-success"></i> Progression
                            </h6>
                            @if($stats['planned'] > 0)
                                @php
                                    $progressPercentage = round(($stats['maintained'] / $stats['planned']) * 100);
                                @endphp
                                <div class="progress mb-3" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $progressPercentage }}%" 
                                         aria-valuenow="{{ $progressPercentage }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ $progressPercentage }}%
                                    </div>
                                </div>
                                <div class="text-center">
                                    <small class="text-muted">{{ $stats['maintained'] }}/{{ $stats['planned'] }} terminées</small>
                                </div>
                            @else
                                <div class="text-center text-muted">
                                    <small>Aucune planification</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Niveaux de priorité -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-exclamation-triangle me-2 text-danger"></i> 
                                Niveaux de Priorité (selon formule de calcul)
                            </h6>
                            <div class="row g-4">
                                <!-- Très Urgent -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-danger mb-2">{{ $priorityStats['tres_urgent'] ?? 0 }}</div>
                                            <h6 class="text-danger mb-1">Très Urgent</h6>
                                            <small class="text-muted">(Score ≥ 4.2)</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Urgent -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-warning mb-2">{{ $priorityStats['urgent'] ?? 0 }}</div>
                                            <h6 class="text-warning mb-1">Urgent</h6>
                                            <small class="text-muted">(Score 3.0-4.19)</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Moyenne -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-info mb-2">{{ $priorityStats['moyenne'] ?? 0 }}</div>
                                            <h6 class="text-info mb-1">Moyenne</h6>
                                            <small class="text-muted">(Score 2.0-2.99)</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Faible -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-secondary mb-2">{{ $priorityStats['faible'] ?? 0 }}</div>
                                            <h6 class="text-secondary mb-1">Faible Priorité</h6>
                                            <small class="text-muted">(Score < 2.0)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Message d'information -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Accès en consultation</strong> - Vous avez accès aux statistiques publiques des infrastructures de {{ $commune->name }}. Pour accéder à des données détaillées et effectuer des modifications, veuillez contacter un administrateur.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

<style>
    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
    }

    .bg-gradient {
        background: linear-gradient(135deg, #198754 0%, #155724 100%);
    }

    .card {
        border-radius: 12px;
    }

    .card-header {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .display-6 {
        font-size: 2.5rem;
        font-weight: 700;
    }
</style>
@endsection
