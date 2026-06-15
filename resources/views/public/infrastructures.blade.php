@extends('layouts.app')

@section('title', 'Infrastructures publiques — ADECOB')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<style>
    .public-toolbar { background:#f6f9f7; border:1px solid #e3ebe6; border-radius:14px; padding:18px; }
    .infra-card { border:1px solid #eef2ee; border-radius:14px; padding:18px; height:100%; background:#fff; transition:transform .15s ease, box-shadow .15s ease;}
    .infra-card:hover { transform: translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.06); }
    .infra-card .meta { font-size:.82rem; color:#6b7a72; }
    .badge-etat { font-size:.72rem; padding:5px 10px; border-radius:999px; font-weight:600; }
    .etat-Bon, .etat-bon { background:#e6f5ec; color:#0a7a3d; }
    .etat-Moyen, .etat-moyen { background:#fff5d6; color:#8a6d00; }
    .etat-Mauvais, .etat-mauvais, .etat-Defectueux { background:#fde7e7; color:#a4242a; }
    #map { height: 460px; border-radius: 14px; border: 1px solid #e3ebe6; }
    .page-title { font-weight: 800; }
</style>

<section class="py-4 py-md-5">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h1 class="page-title h2 mb-1">Infrastructures du Borgou</h1>
                <p class="text-muted mb-0">Données publiques — consultation libre, sans inscription.</p>
            </div>
            <a href="{{ route('public.landing') }}" class="btn btn-outline-success">
                <i class="fas fa-arrow-left"></i> Retour à l'accueil
            </a>
        </div>

        <form method="GET" action="{{ route('public.infrastructures') }}" class="public-toolbar mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Commune</label>
                    <select name="commune_id" class="form-select">
                        <option value="">Toutes les communes</option>
                        @foreach($communes as $c)
                            <option value="{{ $c->id }}" @selected((string) request('commune_id') === (string) $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Type d'infrastructure</label>
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}" @selected(request('type') === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">État</label>
                    <select name="etat" class="form-select">
                        <option value="">Tous les états</option>
                        @foreach(['Bon', 'Moyen', 'Mauvais', 'Défectueux'] as $e)
                            <option value="{{ $e }}" @selected(request('etat') === $e)>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-success" type="submit"><i class="fas fa-filter"></i></button>
                </div>
            </div>
        </form>

        <div class="row g-4">
            <div class="col-lg-7">
                <div id="map"></div>
                <p class="text-muted small mt-2 mb-0">
                    <i class="fas fa-info-circle"></i> {{ count($mapPoints) }} point(s) géolocalisé(s) affiché(s) (limite 500).
                </p>
            </div>
            <div class="col-lg-5">
                @if($infrastructures->isEmpty())
                    <div class="alert alert-info">Aucune infrastructure ne correspond à ces filtres.</div>
                @else
                    <div class="row g-3">
                        @foreach($infrastructures as $infra)
                            <div class="col-12">
                                <div class="infra-card">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <div class="fw-bold">{{ $infra->nom_infrastructure ?: $infra->type_infrastructure ?: 'Infrastructure #'.$infra->id }}</div>
                                            <div class="meta mt-1">
                                                <i class="fas fa-map-marker-alt"></i>
                                                {{ $infra->communeModel?->name ?? $infra->commune ?? '—' }}
                                                @if($infra->village) · {{ $infra->village }} @endif
                                            </div>
                                            <div class="meta">
                                                <i class="fas fa-layer-group"></i> {{ $infra->type_infrastructure ?? '—' }}
                                                @if($infra->annee_realisation) · {{ $infra->annee_realisation }} @endif
                                            </div>
                                        </div>
                                        @if($infra->etat_fonctionnement)
                                            <span class="badge-etat etat-{{ \Illuminate\Support\Str::slug($infra->etat_fonctionnement, '') }}">
                                                {{ $infra->etat_fonctionnement }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        {{ $infrastructures->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    const points = @json($mapPoints);
    const map = L.map('map').setView([10.3, 2.6], 7); // Borgou ~

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    if (points.length) {
        const bounds = [];
        points.forEach(p => {
            if (!isFinite(p.lat) || !isFinite(p.lng)) return;
            const m = L.marker([p.lat, p.lng]).addTo(map);
            m.bindPopup(`
                <strong>${p.name ?? 'Infrastructure'}</strong><br>
                <small>${p.type ?? ''}</small><br>
                <small>${p.commune ?? ''}</small>
                ${p.etat ? '<br><em>État : ' + p.etat + '</em>' : ''}
            `);
            bounds.push([p.lat, p.lng]);
        });
        if (bounds.length) map.fitBounds(bounds, { padding: [30, 30], maxZoom: 12 });
    }
})();
</script>
@endsection
