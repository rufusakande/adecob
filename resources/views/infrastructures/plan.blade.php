@extends('layouts.app')
@section('title', 'Planifier une infrastructure')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .plan-hero{
        background:linear-gradient(135deg,#0b7a3b 0%, #0e9a4a 60%, #f2b81a 130%);
        color:#fff; border-radius:14px; padding:1.4rem 1.6rem;
        box-shadow:0 6px 24px -8px rgba(15,23,42,.15);
    }
    .infra-card{ border:1px solid #e5e7eb; border-radius:12px; background:#fff;}
    .infra-card .card-header{ background:#f9fafb; font-weight:600;}
    .field-row{display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px dashed #eef2f7;font-size:.88rem;}
    .field-row:last-child{border-bottom:0;}
    .field-label{color:#64748b;}
    .field-value{color:#0f172a;font-weight:600;text-align:right;}
    #plan-map{height:220px;border-radius:10px;border:1px solid #e5e7eb;}
    .form-section-title{font-weight:600;color:#0b7a3b;border-left:4px solid #0b7a3b;padding-left:.6rem;margin:1.2rem 0 .8rem;}
    .cost-input{font-size:1.1rem;font-weight:600;}
    .existing-plan{background:#ecfdf5;border-left:4px solid #10b981;padding:.6rem .8rem;border-radius:8px;font-size:.85rem;}
</style>
@endpush

@section('content')
@php
    $arr = is_array($infrastructure->arrondissement) ? $infrastructure->arrondissement : (json_decode($infrastructure->arrondissement, true) ?: []);
    $existingPlans = $infrastructure->works->where('status', 'planned');
    $existingPlannedWork = isset($existingPlannedWork) ? $existingPlannedWork : null;
@endphp
<div class="container-fluid px-3 px-md-4 py-4">

    <div class="plan-hero mb-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <div class="mb-1"><span class="badge bg-light text-dark"><i class="fas fa-hashtag"></i> {{ $infrastructure->id }}</span></div>
            <h1 class="h4 mb-1"><i class="fas fa-calendar-plus me-2"></i>Planification d'intervention</h1>
            <div class="opacity-75">{{ $infrastructure->nom_infrastructure ?: 'Infrastructure' }} — {{ $infrastructure->commune }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('infrastructures.show', $infrastructure) }}" class="btn btn-light"><i class="fas fa-eye me-1"></i> Voir les détails</a>
            <a href="{{ route('infrastructures.planned') }}" class="btn btn-outline-light"><i class="fas fa-list me-1"></i> Liste des planifiées</a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Corrigez les erreurs :</strong>
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- Colonne gauche : détails infra pour aider la décision --}}
        <div class="col-lg-5">
            <div class="infra-card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-building text-success me-2"></i>Infrastructure</span>
                    @include('infrastructures.partials._status-badge', ['status' => $infrastructure->status])
                </div>
                <div class="card-body">
                    @foreach([
                        'Nom'          => $infrastructure->nom_infrastructure,
                        'Type'         => $infrastructure->type_infrastructure,
                        'Secteur'      => $infrastructure->secteur_domaine,
                        'Année réal.'  => $infrastructure->annee_realisation,
                        'Bailleur'     => $infrastructure->bailleur,
                        'Matériaux'    => $infrastructure->type_materiaux,
                    ] as $l=>$v)
                        <div class="field-row"><span class="field-label">{{ $l }}</span><span class="field-value">{{ $v ?: '—' }}</span></div>
                    @endforeach
                </div>
            </div>

            <div class="infra-card mb-3">
                <div class="card-header"><i class="fas fa-map-marker-alt text-success me-2"></i>Localisation</div>
                <div class="card-body">
                    @foreach([
                        'Commune'         => $infrastructure->commune,
                        'Arrondissement'  => $arr ? implode(', ', $arr) : null,
                        'Village'         => $infrastructure->village,
                        'Latitude'        => $infrastructure->latitude,
                        'Longitude'       => $infrastructure->longitude,
                    ] as $l=>$v)
                        <div class="field-row"><span class="field-label">{{ $l }}</span><span class="field-value">{{ $v ?: '—' }}</span></div>
                    @endforeach
                    @if($infrastructure->latitude && $infrastructure->longitude)
                        <div id="plan-map" class="mt-3"></div>
                    @endif
                </div>
            </div>

            <div class="infra-card mb-3">
                <div class="card-header"><i class="fas fa-clipboard-check text-success me-2"></i>État constaté</div>
                <div class="card-body">
                    @foreach([
                        'Fonctionnement' => $infrastructure->etat_fonctionnement,
                        'Dégradation'    => $infrastructure->niveau_degradation,
                        'Réhabilitation' => $infrastructure->rehabilitation,
                        'Mode gestion'   => $infrastructure->mode_gestion,
                    ] as $l=>$v)
                        <div class="field-row"><span class="field-label">{{ $l }}</span><span class="field-value">{{ $v ?: '—' }}</span></div>
                    @endforeach
                    @if($infrastructure->defectuosites_relevees)
                        <div class="mt-2"><strong class="field-label">Défectuosités :</strong>
                            <div class="p-2 bg-light rounded small">{{ $infrastructure->defectuosites_relevees }}</div></div>
                    @endif
                    @if($infrastructure->mesures_proposees)
                        <div class="mt-2"><strong class="field-label">Mesures proposées :</strong>
                            <div class="p-2 bg-light rounded small">{{ $infrastructure->mesures_proposees }}</div></div>
                    @endif
                </div>
            </div>

            @if($existingPlans->count())
                <div class="infra-card">
                    <div class="card-header"><i class="fas fa-calendar-check text-success me-2"></i>Planifications existantes ({{ $existingPlans->count() }})</div>
                    <div class="card-body">
                        @foreach($existingPlans as $p)
                            <div class="existing-plan mb-2">
                                <strong>{{ $p->work_type }}</strong> — {{ $p->completion_date->format('d/m/Y') }}<br>
                                <span class="text-muted">Coût : {{ $p->cost ? number_format($p->cost, 0, ',', ' ').' FCFA' : '—' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Colonne droite : formulaire de planification --}}
        <div class="col-lg-7">
            <form method="POST" action="{{ route('infrastructures.plan.store', $infrastructure) }}" class="infra-card p-4">
                @csrf
                <h5 class="mb-3"><i class="fas fa-pen-to-square text-success me-2"></i>{{ $existingPlannedWork ? 'Modifier la planification' : 'Nouvelle planification' }}</h5>
                <p class="text-muted small">Renseignez le type d'intervention, la date prévue, le coût et les actions à effectuer. Ces informations aideront au suivi budgétaire de la commune.</p>

                <div class="form-section-title">Nature de l'intervention</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Type de travail <span class="text-danger">*</span></label>
                        <select name="work_type" class="form-select @error('work_type') is-invalid @enderror" required>
                            <option value="">— Choisir —</option>
                            @foreach(['Entretien courant','Réhabilitation','Réparation urgente','Extension','Construction neuve','Nettoyage','Autre'] as $wt)
                                <option value="{{ $wt }}" @selected(old('work_type', optional($existingPlannedWork)->work_type)=== $wt)>{{ $wt }}</option>
                            @endforeach
                        </select>
                        @error('work_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date prévue de réalisation <span class="text-danger">*</span></label>
                        <input type="date" name="completion_date" class="form-control @error('completion_date') is-invalid @enderror"
                               min="{{ now()->format('Y-m-d') }}"
                               value="{{ old('completion_date', optional($existingPlannedWork)->completion_date?->format('Y-m-d')) }}" required>
                        @error('completion_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Actions à effectuer / description détaillée <span class="text-danger">*</span></label>
                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" minlength="5" maxlength="5000" required
                                  placeholder="Ex. : remplacement de la pompe manuelle, curage du puits, pose d'une nouvelle margelle…">{{ old('description', optional($existingPlannedWork)->description) }}</textarea>
                        <div class="form-text">Minimum 5 caractères.</div>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-section-title">Budget & acteurs</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Coût estimé (FCFA) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="cost" class="form-control cost-input" min="0" step="500" value="{{ old('cost', optional($existingPlannedWork)->cost) }}" required>
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Année d'exécution (plan triennal) <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group" aria-label="Année d'exécution">
                            @php $selectedYear = (int) old('annee_execution', optional($existingPlannedWork)->annee_execution); @endphp
                            @foreach([1,2,3] as $y)
                                <input type="radio" class="btn-check" name="annee_execution" id="annee_execution_{{ $y }}" value="{{ $y }}" @checked($selectedYear === $y) required>
                                <label class="btn btn-outline-success" for="annee_execution_{{ $y }}">Année {{ $y }}</label>
                            @endforeach
                        </div>
                        <div class="form-text">Cochez l'année du plan triennal à laquelle l'intervention est prévue.</div>
                        @error('annee_execution')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Acteur(s) concerné(s) <span class="text-danger">*</span></label>
                        <textarea name="acteurs_concernes" rows="2" class="form-control @error('acteurs_concernes') is-invalid @enderror" maxlength="1000" required placeholder="Ex. : DST, DSI, AUE, APE, ONG partenaires…">{{ old('acteurs_concernes', optional($existingPlannedWork)->acteurs_concernes) }}</textarea>
                        @error('acteurs_concernes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Source(s) de financement <span class="text-danger">*</span></label>
                        <textarea name="sources_financement" rows="2" class="form-control @error('sources_financement') is-invalid @enderror" maxlength="1000" required placeholder="Ex. : FADeC Investissement, Budget communal, Coopération Suisse…">{{ old('sources_financement', optional($existingPlannedWork)->sources_financement) }}</textarea>
                        @error('sources_financement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Acteurs / prestataires additionnels</label>
                        <textarea name="provider_name" rows="2" class="form-control" maxlength="255" placeholder="Ex. : Mairie, artisans locaux, comité de gestion…">{{ old('provider_name', optional($existingPlannedWork)->provider_name) }}</textarea>
                    </div>
                </div>

                <div class="form-section-title">Observations complémentaires</div>
                <textarea name="observations" rows="3" class="form-control" maxlength="5000" placeholder="Ex. : Priorité très élevée, contraintes saisonnières…">{{ old('observations', optional($existingPlannedWork)->observations) }}</textarea>


                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('infrastructures.index') }}" class="btn btn-light"><i class="fas fa-arrow-left me-1"></i> Retour</a>
                    <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-calendar-check me-1"></i> Enregistrer la planification</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($infrastructure->latitude && $infrastructure->longitude)
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function(){
    const lat = parseFloat("{{ $infrastructure->latitude }}");
    const lng = parseFloat("{{ $infrastructure->longitude }}");
    if(!isFinite(lat) || !isFinite(lng)) return;
    const map = L.map('plan-map').setView([lat,lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:19, attribution:'© OpenStreetMap'}).addTo(map);
    L.marker([lat,lng]).addTo(map).bindPopup(@json($infrastructure->nom_infrastructure ?: 'Infrastructure')).openPopup();
})();
</script>
@endpush
@endif
@endsection
