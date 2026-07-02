@extends('layouts.app')
@section('title', 'Détails de l\'infrastructure')

@push('styles')
<style>
    :root{
        --brand:#0b7a3b; --brand-2:#f2b81a; --brand-3:#c62828;
        --ink:#0f172a; --ink-soft:#475569; --line:#e5e7eb;
        --surface:#ffffff; --surface-2:#f8fafc; --radius:14px;
        --shadow: 0 6px 24px -8px rgba(15,23,42,.12);
    }
    body { background: linear-gradient(180deg,#f8fafc 0%, #eef2f6 100%); }
    .infra-hero {
        background:linear-gradient(135deg,#0b7a3b 0%, #0e9a4a 60%, #f2b81a 130%);
        color:#fff; border-radius: var(--radius); padding:1.5rem 1.75rem;
        box-shadow: var(--shadow); position:relative; overflow:hidden;
    }
    .infra-hero::after{
        content:""; position:absolute; right:-40px; top:-40px; width:220px;height:220px;
        background:radial-gradient(circle at center, rgba(255,255,255,.18), transparent 70%);
    }
    .infra-hero h1{ font-weight:700; letter-spacing:-.01em; }
    .field-card { background:var(--surface); border:1px solid var(--line); border-radius: var(--radius); box-shadow: var(--shadow); }
    .field-card .card-header{
        background: linear-gradient(180deg, #f9fafb 0%, #f3f4f6 100%);
        border-bottom:1px solid var(--line);
        font-weight:600; color:var(--ink); border-top-left-radius: var(--radius); border-top-right-radius: var(--radius);
    }
    .field-row{ display:flex; justify-content:space-between; align-items:flex-start; padding:.55rem 0; border-bottom:1px dashed #eef2f7;}
    .field-row:last-child{ border-bottom:0;}
    .field-label{ color:var(--ink-soft); font-size:.85rem; font-weight:500; padding-right:1rem;}
    .field-value{ color:var(--ink); font-weight:600; text-align:right; word-break:break-word;}
    .field-value.muted{ color:#94a3b8; font-weight:400; font-style:italic;}
    .photo-thumb{ height:150px; width:100%; object-fit:cover; border-radius:10px; border:1px solid var(--line); cursor:zoom-in; transition:transform .2s;}
    .photo-thumb:hover{ transform:scale(1.02);}
    .status-strip{ padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; font-weight:500;}
    .status-strip.warning{ background:#fef3c7; color:#78350f; border:1px solid #fde68a;}
    .status-strip.danger{ background:#fee2e2; color:#7f1d1d; border:1px solid #fecaca;}
    .status-strip.success{ background:#dcfce7; color:#14532d; border:1px solid #bbf7d0;}
    .btn-brand{ background:var(--brand); color:#fff; border:none;}
    .btn-brand:hover{ background:#095c2c; color:#fff;}
    #map-mini{ height:260px; border-radius:12px; border:1px solid var(--line); }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
@php
    $u = auth()->user();
    $arr = is_array($infrastructure->arrondissement) ? $infrastructure->arrondissement : (json_decode($infrastructure->arrondissement, true) ?: []);
    $canManage = $infrastructure->canBeManagedBy($u);
    $canValidate = $infrastructure->canBeValidatedBy($u);
    $isOwner = (int)$infrastructure->user_id === (int)$u->id;
@endphp
<div class="container-fluid px-3 px-md-4 py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- HERO --}}
    <div class="infra-hero mb-4 d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div style="max-width: 720px;">
            <div class="d-flex align-items-center gap-2 mb-2">
                @include('infrastructures.partials._status-badge', ['status' => $infrastructure->status])
                <span class="badge bg-white text-dark">
                    <i class="fas fa-hashtag me-1 text-muted"></i>{{ $infrastructure->id }}
                </span>
            </div>
            <h1 class="h3 mb-1">{{ $infrastructure->nom_infrastructure ?: 'Infrastructure sans nom' }}</h1>
            <div class="opacity-75">
                <i class="fas fa-map-marker-alt me-1"></i>
                {{ $infrastructure->commune }}@if($infrastructure->village) — {{ $infrastructure->village }}@endif
                @if($infrastructure->secteur_domaine)
                    <span class="mx-2">•</span><i class="fas fa-layer-group me-1"></i>{{ $infrastructure->secteur_domaine }}
                @endif
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('infrastructures.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>

            @if($canManage)
                <a href="{{ route('infrastructures.edit', $infrastructure) }}" class="btn btn-warning">
                    <i class="fas fa-pen me-1"></i> Modifier
                </a>
                <form method="POST" action="{{ route('infrastructures.destroy', $infrastructure) }}"
                      onsubmit="return confirm('Supprimer définitivement cette infrastructure ?');" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger"><i class="fas fa-trash me-1"></i> Supprimer</button>
                </form>
            @endif

            @if($canValidate)
                <form method="POST" action="{{ route('infrastructures.validate', $infrastructure) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-success"><i class="fas fa-check me-1"></i> Valider</button>
                </form>
                <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fas fa-times me-1"></i> Rejeter
                </button>
            @endif

            @if($isOwner && $u->isAgent() && $infrastructure->isRejected())
                <form method="POST" action="{{ route('infrastructures.resubmit', $infrastructure) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Renvoyer pour validation</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Bandeau contextuel selon le statut --}}
    @if($infrastructure->isPending())
        <div class="status-strip warning">
            <i class="fas fa-hourglass-half me-2"></i>
            Cette saisie est <strong>en attente de validation</strong> par un administrateur
            @if($infrastructure->submitted_at) depuis le {{ $infrastructure->submitted_at->format('d/m/Y H:i') }}@endif.
            Elle ne sera prise en compte dans l'analyse qu'après validation.
        </div>
    @elseif($infrastructure->isRejected())
        <div class="status-strip danger">
            <i class="fas fa-times-circle me-2"></i>
            <strong>Saisie rejetée</strong>
            @if($infrastructure->validator) par {{ $infrastructure->validator->name }}@endif
            @if($infrastructure->validated_at) le {{ $infrastructure->validated_at->format('d/m/Y H:i') }}@endif.
            @if($infrastructure->rejection_reason)
                <div class="mt-1"><strong>Motif :</strong> {{ $infrastructure->rejection_reason }}</div>
            @endif
        </div>
    @elseif($infrastructure->isValidated())
        <div class="status-strip success">
            <i class="fas fa-check-circle me-2"></i>
            Saisie <strong>validée</strong>
            @if($infrastructure->validator) par {{ $infrastructure->validator->name }}@endif
            @if($infrastructure->validated_at) le {{ $infrastructure->validated_at->format('d/m/Y H:i') }}@endif.
        </div>
    @endif

    <div class="row g-4">
        {{-- Colonne principale --}}
        <div class="col-lg-8">
            {{-- 1. Enquêteur --}}
            <div class="card field-card mb-4">
                <div class="card-header"><i class="fas fa-user-tie me-2 text-success"></i>Enquêteur & date</div>
                <div class="card-body">
                    @php
                        $rows = [
                            'Nom enquêteur' => $infrastructure->nom_enqueteur,
                            'Téléphone' => $infrastructure->numero_telephone,
                            'Date de l\'enquête' => optional($infrastructure->date)->format('d/m/Y'),
                        ];
                    @endphp
                    @foreach($rows as $l => $v)
                        <div class="field-row">
                            <span class="field-label">{{ $l }}</span>
                            <span class="field-value {{ $v ? '' : 'muted' }}">{{ $v ?: 'Non renseigné' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 2. Localisation --}}
            <div class="card field-card mb-4">
                <div class="card-header"><i class="fas fa-map-marked-alt me-2 text-success"></i>Localisation</div>
                <div class="card-body">
                    @php
                        $rows = [
                            'Commune' => $infrastructure->commune,
                            'Arrondissement(s)' => $arr ? implode(', ', $arr) : null,
                            'Village' => $infrastructure->village,
                            'Hameau' => $infrastructure->hameau,
                            'Latitude' => $infrastructure->latitude,
                            'Longitude' => $infrastructure->longitude,
                            'Altitude' => $infrastructure->altitude ? $infrastructure->altitude.' m' : null,
                            'Précision GPS' => $infrastructure->precision ? $infrastructure->precision.' m' : null,
                        ];
                    @endphp
                    @foreach($rows as $l => $v)
                        <div class="field-row">
                            <span class="field-label">{{ $l }}</span>
                            <span class="field-value {{ $v ? '' : 'muted' }}">{{ $v ?: 'Non renseigné' }}</span>
                        </div>
                    @endforeach
                    @if($infrastructure->latitude && $infrastructure->longitude)
                        <div id="map-mini" class="mt-3"></div>
                    @endif
                </div>
            </div>

            {{-- 3. Infrastructure --}}
            <div class="card field-card mb-4">
                <div class="card-header"><i class="fas fa-building me-2 text-success"></i>Infrastructure</div>
                <div class="card-body">
                    @php
                        $rows = [
                            'Secteur / Domaine' => $infrastructure->secteur_domaine,
                            'Type d\'infrastructure' => $infrastructure->type_infrastructure,
                            'Nom de l\'infrastructure' => $infrastructure->nom_infrastructure,
                            'Année de réalisation' => $infrastructure->annee_realisation,
                            'Bailleur / Financement' => $infrastructure->bailleur,
                            'Type de matériaux' => $infrastructure->type_materiaux,
                        ];
                    @endphp
                    @foreach($rows as $l => $v)
                        <div class="field-row">
                            <span class="field-label">{{ $l }}</span>
                            <span class="field-value {{ $v ? '' : 'muted' }}">{{ $v ?: 'Non renseigné' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 4. État & gestion --}}
            <div class="card field-card mb-4">
                <div class="card-header"><i class="fas fa-clipboard-check me-2 text-success"></i>État & gestion</div>
                <div class="card-body">
                    @php
                        $rows = [
                            'État de fonctionnement' => $infrastructure->etat_fonctionnement,
                            'Niveau de dégradation' => $infrastructure->niveau_degradation,
                            'Mode de gestion' => $infrastructure->mode_gestion,
                            'Précision mode de gestion' => $infrastructure->mode_gestion_preciser,
                            'Besoin de réhabilitation' => $infrastructure->rehabilitation,
                        ];
                    @endphp
                    @foreach($rows as $l => $v)
                        <div class="field-row">
                            <span class="field-label">{{ $l }}</span>
                            <span class="field-value {{ $v ? '' : 'muted' }}">{{ $v ?: 'Non renseigné' }}</span>
                        </div>
                    @endforeach

                    @foreach(['Défectuosités relevées' => $infrastructure->defectuosites_relevees,
                              'Mesures proposées' => $infrastructure->mesures_proposees,
                              'Observation générale' => $infrastructure->observation_generale] as $l => $v)
                        <div class="mt-3">
                            <div class="field-label mb-1"><strong>{{ $l }}</strong></div>
                            <div class="p-2 bg-light rounded {{ $v ? '' : 'text-muted fst-italic' }}">{{ $v ?: 'Aucune information' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Colonne latérale --}}
        <div class="col-lg-4">
            {{-- Photos --}}
            <div class="card field-card mb-4">
                <div class="card-header"><i class="fas fa-camera me-2 text-success"></i>Photos</div>
                <div class="card-body">
                    @php $hasPhoto = false; @endphp
                    <div class="row g-2">
                        @for ($i = 1; $i <= 4; $i++)
                            @php $pf = 'photo'.$i; @endphp
                            @if($infrastructure->$pf)
                                @php $hasPhoto = true; @endphp
                                <div class="col-6">
                                    <a href="{{ asset('storage/'.$infrastructure->$pf) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$infrastructure->$pf) }}" class="photo-thumb" alt="Photo {{ $i }}">
                                    </a>
                                </div>
                            @endif
                        @endfor
                    </div>
                    @if(!$hasPhoto)
                        <p class="text-muted fst-italic mb-0">Aucune photo jointe.</p>
                    @endif
                </div>
            </div>

            {{-- Traçabilité --}}
            <div class="card field-card mb-4">
                <div class="card-header"><i class="fas fa-history me-2 text-success"></i>Traçabilité</div>
                <div class="card-body">
                    <div class="field-row">
                        <span class="field-label">Saisie par</span>
                        <span class="field-value">{{ optional($infrastructure->user)->name ?: '—' }}</span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Créée le</span>
                        <span class="field-value">{{ $infrastructure->created_at?->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($infrastructure->submitted_at)
                    <div class="field-row">
                        <span class="field-label">Soumise le</span>
                        <span class="field-value">{{ $infrastructure->submitted_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    @if($infrastructure->validated_at)
                    <div class="field-row">
                        <span class="field-label">{{ $infrastructure->isRejected() ? 'Rejetée le' : 'Validée le' }}</span>
                        <span class="field-value">{{ $infrastructure->validated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    @if($infrastructure->validator)
                    <div class="field-row">
                        <span class="field-label">{{ $infrastructure->isRejected() ? 'Rejetée par' : 'Validée par' }}</span>
                        <span class="field-value">{{ $infrastructure->validator->name }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Rejet --}}
@if($canValidate)
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('infrastructures.reject', $infrastructure) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-times-circle text-danger me-2"></i>Rejeter cette saisie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Précisez le motif — l'agent verra ce message et pourra corriger sa saisie.</p>
                <textarea name="rejection_reason" class="form-control" rows="4" required minlength="5" maxlength="1000" placeholder="Ex. : coordonnées GPS incohérentes avec la commune indiquée…"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger">Confirmer le rejet</button>
            </div>
        </form>
    </div>
</div>
@endif

@if($infrastructure->latitude && $infrastructure->longitude)
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    (function(){
        const lat = parseFloat("{{ $infrastructure->latitude }}");
        const lng = parseFloat("{{ $infrastructure->longitude }}");
        if (!isFinite(lat) || !isFinite(lng)) return;
        const map = L.map('map-mini').setView([lat, lng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19, attribution: '© OpenStreetMap'
        }).addTo(map);
        L.marker([lat, lng]).addTo(map)
         .bindPopup(@json($infrastructure->nom_infrastructure ?: 'Infrastructure'))
         .openPopup();
    })();
</script>
@endpush
@endif
@endsection
