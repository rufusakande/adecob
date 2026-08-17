@extends('layouts.app')

@section('title', 'Infrastructures publiques — ' . config('app.name'))

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

<style>
    .public-toolbar { background:#fff; border:1px solid #e7eeea; border-radius:16px; padding:20px; box-shadow:0 6px 20px -10px rgba(16,60,35,.12); }
    .infra-card { border:1px solid #eef2ee; border-radius:16px; padding:18px; height:100%; background:#fff; box-shadow:0 6px 20px -10px rgba(16,60,35,.1); transition:transform .2s ease, box-shadow .2s ease;}
    .infra-card:hover { transform: translateY(-3px); box-shadow:0 14px 30px -12px rgba(6,74,26,.2); }
    .infra-card .meta { font-size:.82rem; color:#6b7a72; }
    .badge-etat { font-size:.72rem; padding:5px 10px; border-radius:999px; font-weight:600; }
    .etat-Bon, .etat-bon { background:#e6f5ec; color:#0a7a3d; }
    .etat-Moyen, .etat-moyen { background:#fff5d6; color:#8a6d00; }
    .etat-Mauvais, .etat-mauvais, .etat-Defectueux { background:#fde7e7; color:#a4242a; }
    #map { height: 460px; border-radius: 16px; border: 1px solid #e7eeea; box-shadow:0 6px 20px -10px rgba(16,60,35,.12); }
    .page-title { font-weight: 800; }
    /* Pastilles de clusters — thème vert ARMANI (Leaflet.markercluster) */
    .armacluster { background: transparent; border: none; }
    .armacluster__badge {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #0b6623, #0a7a2a);
        color: #fff; font-weight: 700; font-size: 12px; line-height: 1;
        border: 3px solid #fff; border-radius: 50%;
        box-shadow: 0 3px 10px rgba(6, 74, 26, .35);
    }
    /* Point individuel (hors cluster) : petit point vert centré, sans chiffre */
    .public-dot-icon { background: transparent; border: none; }
    .public-dot {
        width: 11px; height: 11px;
        background: #10b981;
        border: 2px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 1px 4px rgba(6, 74, 26, .45);
    }
    .leaflet-tooltip.inf-tip { font-size: .8rem; line-height: 1.35; }
</style>

<section class="py-4 py-md-5">
    <div class="container">
        <div class="page-hero">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h1 class="h2 mb-1">Infrastructures du Borgou</h1>
                    <p class="hero-sub mb-0">Données publiques — consultation libre, sans inscription.</p>
                </div>
                <div class="hero-actions">
                    <a href="{{ route('public.landing') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Retour à l'accueil
                    </a>
                </div>
            </div>
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
                    <i class="fas fa-info-circle"></i> {{ count($mapPoints) }} point(s) géolocalisé(s) affiché(s) sur la carte.
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

                    <div class="mt-3 d-flex flex-column align-items-center gap-2">
                        <div class="text-muted small">
                            {{ $infrastructures->firstItem() }}–{{ $infrastructures->lastItem() }}
                            sur {{ $infrastructures->total() }} infrastructure(s)
                        </div>
                        {{ $infrastructures->onEachSide(0)->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
(function () {
    const points = @json($mapPoints);
    const container = document.getElementById('map');
    if (container && container._leaflet_id) {
        container._leaflet_id = null;
    }
    const map = L.map('map').setView([10.3, 2.6], 7); // Borgou ~

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    if (points.length) {
        // Regroupement en clusters : les lieux restent visibles à toutes les échelles
        // et la taille des pastilles s'adapte au zoom (meilleure lisibilité avec +10 000 points).
        const clusters = L.markerClusterGroup({
            maxClusterRadius: 45,
            iconCreateFunction: function (cluster) {
                const count = cluster.getChildCount();
                const size = count < 100 ? 40 : count < 500 ? 48 : 58;
                return L.divIcon({
                    html: '<div class="armacluster__badge">' + count + '</div>',
                    className: 'armacluster',
                    iconSize: L.point(size, size),
                });
            },
        });

        // Icône "point" individuel : petit point vert centré exactement, sans chiffre.
        const dotIcon = L.divIcon({
            className: 'public-dot-icon',
            iconSize: [12, 12],
            iconAnchor: [6, 6], // centre du point = position exacte
            html: '<div class="public-dot"></div>',
        });

        const bounds = [];
        points.forEach(p => {
            if (!isFinite(p.lat) || !isFinite(p.lng)) return;
            const m = L.marker([p.lat, p.lng], { icon: dotIcon });

            // Info-bulle au survol : nom + localisation de l'infrastructure.
            m.bindTooltip(`
                <strong>${p.name ?? 'Infrastructure'}</strong><br>
                <small>${p.type ? p.type + ' · ' : ''}${p.commune ?? ''}</small>
            `, { className: 'inf-tip', direction: 'top', offset: [0, -8] });

            // Popup au clic : détail complet.
            m.bindPopup(`
                <strong>${p.name ?? 'Infrastructure'}</strong><br>
                <small>${p.type ?? ''}</small><br>
                <small>${p.commune ?? ''}</small>
                ${p.etat ? '<br><em>État : ' + p.etat + '</em>' : ''}
            `);

            clusters.addLayer(m);
            bounds.push([p.lat, p.lng]);
        });

        clusters.addTo(map);
        if (bounds.length) map.fitBounds(bounds, { padding: [30, 30], maxZoom: 12 });
    }
})();
</script>
@endsection
