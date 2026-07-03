@extends('layouts.app')
@section('title', 'Exploitation des données des infrastructures')
@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Logo avec couleurs du drapeau -->
    {{-- <div class="d-flex align-items-center mb-4">
        <div class="me-3" style="width: 50px; height: 50px; border: 2px solid #dee2e6; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="height: 33.33%; background-color: #198754;"></div>
            <div style="height: 33.33%; background-color: #ffc107;"></div>
            <div style="height: 33.33%; background-color: #dc3545;"></div>
        </div>
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Système de Gestion des Infrastructures</h1>
            <p class="text-muted mb-0">Suivi et maintenance des équipements publics</p>
        </div>
    </div> --}}

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
                Exploitation des Données des Infrastructures
            </h2>
            <p class="text-muted mb-0">Gestion complète des équipements publics et suivi des interventions</p>
        </div>
        @if(!Auth::user()->isPublicUser())
        <div class="d-flex flex-wrap gap-2">
            @if(Auth::user()->isSuperAdmin() || Auth::user()->isCommuneAdmin())
                @php
                    $__pendingCount = \App\Models\Infrastructure::query()
                        ->visibleTo(Auth::user())->pending()->count();
                    $__plannedCount = \App\Models\Infrastructure::query()
                        ->visibleTo(Auth::user())
                        ->whereHas('works', fn($q) => $q->where('status', 'planned'))->count();
                @endphp
                <a href="{{ route('infrastructures.pending') }}" class="btn btn-warning position-relative d-flex align-items-center gap-2">
                    <i class="fas fa-hourglass-half"></i> À valider
                    @if($__pendingCount > 0)
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">{{ $__pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('infrastructures.planned') }}" class="btn btn-info text-white position-relative d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-check"></i> Planifiées
                    @if($__plannedCount > 0)
                        <span class="badge bg-dark position-absolute top-0 start-100 translate-middle rounded-pill">{{ $__plannedCount }}</span>
                    @endif
                </a>
            @endif
            <button class="btn btn-danger d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import"></i> Importer
            </button>
            <a href="{{ route('infrastructures.create') }}" class="btn btn-success d-flex align-items-center gap-2">
                <i class="fas fa-plus"></i> Nouveau
            </a>
        </div>
        @endif
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

    <!-- Formulaire de recherche -->
    @if(!Auth::user()->isPublicUser())
    <form method="GET" action="{{ route('infrastructures.index') }}" class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <h5 class="card-title mb-4 text-dark">
                <i class="fas fa-filter me-2 text-success"></i> 
                Filtres de Recherche
            </h5>
            <div class="row g-3">
                <div class="col-md-6 col-lg-2">
                    <label class="form-label text-muted">Date début</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label text-muted">Date fin</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label text-muted">Commune</label>
                    <select name="commune" class="form-select">
                        <option value="">Toutes les communes</option>
                        @foreach($communes as $commune)
                            <option value="{{ $commune }}" {{ request('commune') == $commune ? 'selected' : '' }}>{{ $commune }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label text-muted">Arrondissement</label>
                    <select name="arrondissement" class="form-select">
                        <option value="">Tous les arrondissements</option>
                        @foreach($arrondissements as $arrondissement)
                            <option value="{{ $arrondissement }}" {{ request('arrondissement') == $arrondissement ? 'selected' : '' }}>{{ $arrondissement }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label text-muted">Village</label>
                    <select name="village" class="form-select">
                        <option value="">Tous les villages</option>
                        @foreach($villages as $village)
                            <option value="{{ $village }}" {{ request('village') == $village ? 'selected' : '' }}>{{ $village }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label text-muted">Secteur</label>
                    <select name="secteur_domaine" class="form-select">
                        <option value="">Tous les secteurs</option>
                        @foreach($secteurs as $secteur)
                            <option value="{{ $secteur }}" {{ request('secteur_domaine') == $secteur ? 'selected' : '' }}>{{ $secteur }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label text-muted">Type d'infrastructure</label>
                    <select name="type_infrastructure" class="form-select">
                        <option value="">Tous les types</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type_infrastructure') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label text-muted">Année</label>
                    <select name="annee_realisation" class="form-select">
                        <option value="">Toutes les années</option>
                        @foreach($annees as $annee)
                            <option value="{{ $annee }}" {{ request('annee_realisation') == $annee ? 'selected' : '' }}>{{ $annee }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label text-muted">État de fonctionnement</label>
                    <select name="etat_fonctionnement" class="form-select">
                        <option value="">Tous les états</option>
                        @foreach($etats as $etat)
                            <option value="{{ $etat }}" {{ request('etat_fonctionnement') == $etat ? 'selected' : '' }}>{{ $etat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label text-muted">Niveau de dégradation</label>
                    <select name="niveau_degradation" class="form-select">
                        <option value="">Tous les niveaux</option>
                        @foreach($niveaux as $niveau)
                            <option value="{{ $niveau }}" {{ request('niveau_degradation') == $niveau ? 'selected' : '' }}>{{ $niveau }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-success flex-grow-1 flex-md-grow-0">
                            <i class="fas fa-search me-2"></i> Rechercher
                        </button>
                        <a href="{{ route('infrastructures.index') }}" class="btn btn-secondary flex-grow-1 flex-md-grow-0">
                            <i class="fas fa-sync me-2"></i> Réinitialiser
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @endif

    <!-- Boutons d'export -->
    @if(!Auth::user()->isPublicUser())
    <form id="exportForm" method="GET" action="{{ route('infrastructures.export') }}" class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <h5 class="card-title mb-4 text-dark">
                <i class="fas fa-download me-2 text-success"></i> 
                Exporter les Données
            </h5>
            <div class="row g-3 align-items-end">
                <div class="col-md-4 col-lg-3">
                    <label class="form-label text-muted">Filtrer par année</label>
                    <select name="year" class="form-select">
                        <option value="">Toutes les années</option>
                        @foreach($annees as $annee)
                            <option value="{{ $annee }}">{{ $annee }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-lg-3">
                    <label class="form-label text-muted">Filtrer par commune</label>
                    <select name="commune" class="form-select">
                        <option value="">Toutes les communes</option>
                        @foreach($communes as $commune)
                            <option value="{{ $commune }}">{{ $commune }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-lg-3">
                    <label class="form-label text-muted">Filtrer par secteur</label>
                    <select name="secteur_domaine" class="form-select">
                        <option value="">Tous les secteurs</option>
                        @foreach($secteurs as $secteur)
                            <option value="{{ $secteur }}">{{ $secteur }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 col-lg-3">
                    <div class="d-flex gap-2">
                        <button type="submit" name="format" value="excel" class="btn btn-success w-100">
                            <i class="fas fa-file-excel me-2"></i> Excel
                        </button>
                        <button type="submit" name="format" value="pdf" class="btn btn-danger w-100">
                            <i class="fas fa-file-pdf me-2"></i> PDF
                        </button>
                    </div>
                </div>
            </div>
            <div class="alert alert-info mt-3 mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Vous pouvez également sélectionner des lignes spécifiques dans le tableau ci-dessous pour un export personnalisé
            </div>
        </div>
    </form>
    @endif

    <!-- Message pour utilisateurs publics -->
    @if(Auth::user()->isPublicUser())
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Accès restreint</strong> - Vous avez accès aux statistiques des infrastructures. Pour accéder à des données détaillées, veuillez contacter un administrateur.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @else

    <!-- Tableau des données -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="select-all">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Statut</th>

                            <th>Enquêteur</th>
                            <th>Téléphone</th>
                            <th>Date</th>
                            <th>Localisation</th>
                            <th>Secteur</th>
                            <th>Infrastructure</th>
                            <th>Caractéristiques</th>
                            <th>État</th>
                            <th>Photos</th>
                            <th>Coordonnées</th>
                            <th>Descriptions</th>
                            <th width="140">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($infrastructures as $infra)
                        @php
                            $isPlanned = in_array($infra->id, $plannedInfrastructureIds ?? []);
                            // Calculer la classe de priorité
                            $priorityClass = '';
                            if($infra->score_priorite >= 4.2) {
                                $priorityClass = 'table-danger'; // Très Urgent - Rouge
                            } elseif($infra->score_priorite >= 3.0) {
                                $priorityClass = 'table-warning'; // Urgent - Jaune
                            } elseif($infra->score_priorite >= 2.0) {
                                $priorityClass = 'table-info'; // Moyenne - Bleu
                            } elseif($infra->score_priorite > 0) {
                                $priorityClass = 'table-secondary'; // Faible - Gris
                            }
                            // Combiner avec la classe planifiée si nécessaire
                            $rowClass = $isPlanned ? 'table-success' : $priorityClass;
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="selected_ids[]" value="{{ $infra->id }}" form="exportForm">
                                </div>
                            </td>
                            <td><strong>{{ $infra->id }}</strong></td>
                            <td>@include('infrastructures.partials._status-badge', ['status' => $infra->status])</td>

                            <td>{{ $infra->nom_enqueteur ?? 'N/A' }}</td>
                            <td>{{ $infra->numero_telephone ?? 'N/A' }}</td>
                            <td>
                                {{
                                    $infra->date instanceof \Illuminate\Support\Carbon
                                        ? $infra->date->format('d/m/Y')
                                        : (is_string($infra->date)
                                            ? \Carbon\Carbon::parse($infra->date)->format('d/m/Y')
                                            : 'N/A')
                                }}
                            </td>
                            <td>
                                <div class="small">
                                    <strong class="text-dark">Commune:</strong> {{ $infra->commune ?? 'N/A' }}<br>
                                    <strong class="text-dark">Arrond.:</strong> {{ $infra->arrondissement ? implode(', ', json_decode($infra->arrondissement)) : 'N/A' }}<br>
                                    <strong class="text-dark">Village:</strong> {{ $infra->village ?? 'N/A' }}<br>
                                    <strong class="text-dark">Hameau:</strong> {{ $infra->hameau ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark">{{ $infra->secteur_domaine ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="small">
                                    <strong class="text-dark">Type:</strong> {{ $infra->type_infrastructure ?? 'N/A' }}<br>
                                    <strong class="text-dark">Nom:</strong> {{ $infra->nom_infrastructure ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <strong class="text-dark">Année:</strong> {{ $infra->annee_realisation ?? 'N/A' }}<br>
                                    <strong class="text-dark">Bailleur:</strong> {{ $infra->bailleur ?? 'N/A' }}<br>
                                    <strong class="text-dark">Matériaux:</strong> {{ $infra->type_materiaux ?? 'N/A' }}<br>
                                    <strong class="text-dark">Gestion:</strong> {{ $infra->mode_gestion ?? 'N/A' }} {{ $infra->mode_gestion_preciser ? '('.$infra->mode_gestion_preciser.')' : '' }}
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <strong class="text-dark">Fonction:</strong> {{ $infra->etat_fonctionnement ?? 'N/A' }}<br>
                                    <strong class="text-dark">Dégradation:</strong> {{ $infra->niveau_degradation ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $hasPhotos = false;
                                    for($i = 1; $i <= 4; $i++) {
                                        if($infra->{"photo$i"} && \Storage::disk('public')->exists($infra->{"photo$i"})) {
                                            $hasPhotos = true;
                                            break;
                                        }
                                    }
                                @endphp
                                @if($hasPhotos)
                                    <div class="d-flex flex-wrap gap-1">
                                        @for($i = 1; $i <= 4; $i++)
                                            @if($infra->{"photo$i"} && \Storage::disk('public')->exists($infra->{"photo$i"}))
                                                <a href="{{ \Storage::url($infra->{"photo$i"}) }}" target="_blank" class="d-block" title="Photo {{$i}}">
                                                    <img src="{{ \Storage::url($infra->{"photo$i"}) }}" alt="Photo {{$i}}" class="rounded" style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                </a>
                                            @endif
                                        @endfor
                                    </div>
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-image" style="font-size: 24px;"></i>
                                        <div class="small">Aucune photo</div>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="small">
                                    <strong class="text-dark">Latitude:</strong> {{ $infra->latitude ?? 'N/A' }}<br>
                                    <strong class="text-dark">Longitude:</strong> {{ $infra->longitude ?? 'N/A' }}<br>
                                    <strong class="text-dark">Altitude:</strong> {{ $infra->altitude ?? 'N/A' }}<br>
                                    <strong class="text-dark">Précision:</strong> {{ $infra->precision ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <strong class="text-dark">Défauts:</strong> {{ $infra->defectuosites_relevees ?? 'N/A' }}<br>
                                    <strong class="text-dark">Mesures:</strong> {{ $infra->mesures_proposees ?? 'N/A' }}<br>
                                    <strong class="text-dark">Observation:</strong> {{ $infra->observation_generale ?? 'N/A' }}<br>
                                    <strong class="text-dark">Réhabilitation:</strong> {{ $infra->rehabilitation ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $canManage = $infra->canBeManagedBy(Auth::user());
                                    $isAdmin = Auth::user()->isSuperAdmin() || Auth::user()->isCommuneAdmin();
                                    $hasPlan = $infra->works->where('status', 'planned')->count() > 0;
                                @endphp
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('infrastructures.show', $infra->id) }}" class="btn btn-sm btn-info text-white" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($canManage)
                                        <a href="{{ route('infrastructures.edit', $infra->id) }}" class="btn btn-sm btn-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if($isAdmin && $infra->isValidated())
                                        <a href="{{ route('infrastructures.plan', $infra->id) }}"
                                           class="btn btn-sm {{ $hasPlan ? 'btn-outline-success' : 'btn-success' }}"
                                           title="{{ $hasPlan ? 'Ajouter une planification' : 'Planifier' }}">
                                            <i class="fas fa-calendar-plus"></i>
                                        </a>
                                    @endif
                                    @if($canManage)
                                        <form action="{{ route('infrastructures.destroy', $infra->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="card-footer bg-white border-0">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <small class="text-muted">
                            Affichage de {{ $infrastructures->firstItem() ?? 0 }} à {{ $infrastructures->lastItem() ?? 0 }} sur {{ $infrastructures->total() }} entrées
                        </small>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end">
                            {{ $infrastructures->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal d'import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('infrastructures.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="fas fa-file-import me-2"></i>
                        Importer des données
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Sélectionner un fichier</label>
                        <input class="form-control" type="file" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Formats acceptés: Excel (.xlsx, .xls) ou CSV</div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="overwrite" name="overwrite">
                        <label class="form-check-label" for="overwrite">
                            <strong>Remplacer les données existantes</strong>
                            <div class="text-muted small">Cette action supprimera toutes les données actuelles et les remplacera par celles du fichier importé</div>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-upload me-2"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Styles personnalisés -->
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
    
    .table th {
        white-space: nowrap;
        font-weight: 600;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .form-control, .form-select {
        border-color: #dee2e6;
        border-radius: 8px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
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
    
    .btn {
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.03);
    }
    
    /* Styles pour les niveaux de priorité */
    /* Très Urgent - Rouge */
    .table-danger td {
        background-color: #f8d7da !important;
        color: #842029;
    }
    /* Urgent - Jaune/Orange */
    .table-warning td {
        background-color: #fff3cd !important;
        color: #664d03;
    }
    /* Moyenne - Bleu */
    .table-info td {
        background-color: #cff4fc !important;
        color: #055160;
    }
    /* Faible - Gris */
    .table-secondary td {
        background-color: #e2e3e5 !important;
        color: #41464b;
    }
    /* Planifié - Vert */
    .table-success td {
        background-color: #d1e7dd !important;
        color: #0f5132;
    }
    
    /* Style pour masquer les barres de défilement */
    .card-body div[style*="overflow-y: auto"] {
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE and Edge */
    }
    
    .card-body div[style*="overflow-y: auto"]::-webkit-scrollbar {
        display: none; /* Chrome, Safari and Opera */
    }
    
    .img-thumbnail {
        transition: all 0.3s ease;
        border: 2px solid #fff;
    }
    
    .img-thumbnail:hover {
        transform: scale(1.5);
        z-index: 10;
        position: relative;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
</style>

<!-- JavaScript -->
<script>
    // Sélection/désélection globale
    document.getElementById('select-all').addEventListener('click', function(event) {
        let checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
        checkboxes.forEach(cb => cb.checked = event.target.checked);
    });
    
    // Masquer les messages après 5 secondes
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 1s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 1000);
        });
    }, 5000);
</script>
@endsection