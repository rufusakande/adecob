@extends('layouts.app')
@section('content')
<div class="container">
    <div class="text-center mb-4">
        <img src="{{ asset('logo.jpg') }}" alt="Logo ADECOB" class="img-fluid" style="max-height: 100px;">
    </div>
    <h2 class="mb-4">Tableau de bord de suivi dynamique - Agent de la Mairie</h2>
    
    <!-- Section Statistiques des Infrastructures -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i> Statistiques des Infrastructures
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Première ligne : Statistiques principales -->
                    <div class="row g-4 mb-4">
                        <!-- Total -->
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="display-6 text-primary mb-2">{{ $stats['total'] }}</div>
                                    <h6 class="text-primary mb-0">Total Infrastructures</h6>
                                </div>
                            </div>
                        </div>
                        <!-- Planifiées -->
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="display-6 text-info mb-2">{{ $stats['planned'] }}</div>
                                    <h6 class="text-info mb-0">Planifiées</h6>
                                </div>
                            </div>
                        </div>
                        <!-- À Entretenir -->
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="display-6 text-warning mb-2">{{ $stats['to_maintain'] }}</div>
                                    <h6 class="text-warning mb-0">À Entretenir</h6>
                                </div>
                            </div>
                        </div>
                        <!-- Déjà Entretenues -->
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="display-6 text-success mb-2">{{ $stats['maintained'] }}</div>
                                    <h6 class="text-success mb-0">Entretenues</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Deuxième ligne : Répartitions détaillées -->
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
                    
                    <!-- Troisième ligne : Statistiques de priorité -->
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
                                        <div class="col-md-3">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body text-center">
                                                    <div class="display-6 text-danger mb-2">{{ $priorityStats['tres_urgent'] ?? 0 }}</div>
                                                    <h6 class="text-danger mb-1">Très Urgent</h6>
                                                    <small class="text-muted">(Score ≥ 4.2)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Urgent -->
                                        <div class="col-md-3">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body text-center">
                                                    <div class="display-6 text-warning mb-2">{{ $priorityStats['urgent'] ?? 0 }}</div>
                                                    <h6 class="text-warning mb-1">Urgent</h6>
                                                    <small class="text-muted">(Score 3.0-4.19)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Moyenne -->
                                        <div class="col-md-3">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body text-center">
                                                    <div class="display-6 text-info mb-2">{{ $priorityStats['moyenne'] ?? 0 }}</div>
                                                    <h6 class="text-info mb-1">Moyenne</h6>
                                                    <small class="text-muted">(Score 2.0-2.99)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Faible -->
                                        <div class="col-md-3">
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
        </div>
    </div>
    
    <form method="GET" action="{{ route('mairie-agent.dashboard') }}" class="mb-4">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label for="commune" class="form-label">Filtrer par commune</label>
                <select name="commune" id="commune" class="form-select" onchange="this.form.submit()">
                    <option value="">Toutes les communes</option>
                    @foreach($communes as $commune)
                        <option value="{{ $commune }}" {{ request('commune') == $commune ? 'selected' : '' }}>{{ $commune }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label for="secteur" class="form-label">Filtrer par secteur</label>
                <select name="secteur" id="secteur" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les secteurs</option>
                    @foreach($secteurs as $secteur)
                        <option value="{{ $secteur }}" {{ request('secteur') == $secteur ? 'selected' : '' }}>{{ $secteur }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('mairie-agent.export-pdf', request()->query()) }}" class="btn btn-primary">
                <i class="fas fa-download me-2"></i> Télécharger PDF
            </a>
        </div>
    </form>
    
    @if($data->count() > 0)
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Commune</th>
                            <th>Secteur</th>
                            <th>Désignation</th>
                            <th>Localisation</th>
                            <th>Activités</th>
                            <th>Responsables</th>
                            <th>Personnes Associées</th>
                            <th>Source de financement</th>
                            <th>Montant</th>
                            <th>2023</th>
                            <th>2024</th>
                            <th>2025</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalMontant = 0;
                        @endphp
                        @foreach($data as $item)
                            <tr>
                                <td><strong>{{ $item->commune }}</strong></td>
                                <td>{{ $item->secteur }}</td>
                                <td>{{ $item->designation }}</td>
                                <td>{{ $item->localisation }}</td>
                                <td>{{ $item->activites }}</td>
                                <td>{{ $item->responsables }}</td>
                                <td>{{ $item->personnes_associes }}</td>
                                <td>{{ $item->source_financement }}</td>
                                <td><strong>{{ number_format($item->montant, 2, ',', ' ') }}</strong></td>
                                <td class="text-center">
                                    @if($item->periode_2023)
                                        <span class="badge bg-success">X</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->periode_2024)
                                        <span class="badge bg-success">X</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->periode_2025)
                                        <span class="badge bg-success">X</span>
                                    @endif
                                </td>
                            </tr>
                            @php
                                $totalMontant += $item->montant;
                            @endphp
                        @endforeach
                        <tr class="table-dark">
                            <td colspan="8" class="text-end"><strong>Total Montant</strong></td>
                            <td><strong>{{ number_format($totalMontant, 2, ',', ' ') }}</strong></td>
                            <td colspan="3"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-md-0">
                        Affichage de {{ $data->firstItem() ?? 0 }} à {{ $data->lastItem() ?? 0 }} sur {{ $data->total() }} entrées
                    </div>
                    <div>
                        {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i> Aucune donnée trouvée pour les critères sélectionnés.
        </div>
    @endif
    
    <!-- Section des infrastructures déjà planifiées -->
    @if(isset($plannedInfrastructures) && $plannedInfrastructures->count() > 0)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-warning text-dark">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> Infrastructures Déjà Planifiées
                        </h5>
                        <small class="text-dark">Ces infrastructures ont déjà une planification - Éviter de les planifier à nouveau</small>
                    </div>
                    <span class="badge bg-dark">{{ $plannedInfrastructures->count() }} infrastructures</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID Infra</th>
                                <th>Nom Infrastructure</th>
                                <th>Commune</th>
                                <th>Secteur</th>
                                <th>Type</th>
                                <th>État</th>
                                <th>Dégradation</th>
                                <th>Montant Planifié</th>
                                <th>Périodes</th>
                                <th>Date Planification</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plannedInfrastructures as $planned)
                                <tr class="bg-warning-subtle">
                                    <td><strong>#{{ $planned->infrastructure_id }}</strong></td>
                                    <td>{{ $planned->nom_infrastructure ?? $planned->designation }}</td>
                                    <td>{{ $planned->infra_commune ?? $planned->planning_commune }}</td>
                                    <td>{{ $planned->secteur_domaine ?? $planned->planning_secteur }}</td>
                                    <td>{{ $planned->type_infrastructure ?? 'N/A' }}</td>
                                    <td>
                                        @if($planned->etat_fonctionnement)
                                            <span class="badge {{ $planned->etat_fonctionnement == 'Fonctionnel' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $planned->etat_fonctionnement }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($planned->niveau_degradation)
                                            <span class="badge {{ $planned->niveau_degradation == 'Élevé' ? 'bg-danger' : ($planned->niveau_degradation == 'Moyen' ? 'bg-warning' : 'bg-success') }}">
                                                {{ $planned->niveau_degradation }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ number_format($planned->montant ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                                    <td>
                                        @php
                                            $periodes = [];
                                            for($year = 2023; $year <= 2030; $year++) {
                                                if($planned->{"periode_$year"}) {
                                                    $periodes[] = $year;
                                                }
                                            }
                                        @endphp
                                        @if(count($periodes) > 0)
                                            <span class="badge bg-info">{{ implode(', ', $periodes) }}</span>
                                        @else
                                            <span class="text-muted">Aucune</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($planned->planned_date)->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    
    @if(isset($totalMontantBySecteur) && $totalMontantBySecteur->count() > 0)
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
                <h4 class="card-title mb-0">Totaux par secteur et commune</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Secteur</th>
                                <th>Commune</th>
                                <th>Total Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($totalMontantBySecteur as $total)
                                <tr>
                                    <td><strong>{{ $total->secteur }}</strong></td>
                                    <td>{{ $total->commune }}</td>
                                    <td><strong>{{ number_format($total->total_montant, 2, ',', ' ') }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .bg-gradient {
        background: linear-gradient(45deg, #198754, #198754, #ffc107, #dc3545);
        background-size: 400% 400%;
        animation: gradient 15s ease infinite;
    }
    
    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .card {
        border-radius: 12px;
    }
    
    .card-header {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    
    .card-footer {
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    
    .table th {
        white-space: nowrap;
        font-weight: 600;
    }
    
    .badge {
        font-weight: 500;
    }
    
    .progress {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .progress-bar {
        transition: width 0.6s ease;
    }

    /* Style pour masquer les barres de défilement tout en gardant la fonction de scroll */
    .card-body div[style*="overflow-y: auto"] {
        scrollbar-width: none; /* Pour Firefox */
        -ms-overflow-style: none; /* Pour Internet Explorer et Edge */
    }

    .card-body div[style*="overflow-y: auto"]::-webkit-scrollbar {
        width: 0;
        height: 0;
        display: none; /* Pour Chrome, Safari et Opera */
    }
</style>
@endsection