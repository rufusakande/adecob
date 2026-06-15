@extends('layouts.app')

@section('title', 'ADECOB Infrastructure Plannification — Plateforme publique')

@section('content')
<style>
    .hero-public {
        background: linear-gradient(135deg, #008751 0%, #0a5a3a 60%, #003d20 100%);
        color: #fff;
        padding: 80px 0 100px;
        position: relative;
        overflow: hidden;
    }
    .hero-public::after {
        content: "";
        position: absolute; inset: 0;
        background: radial-gradient(circle at 80% 20%, rgba(252,209,22,0.18), transparent 50%);
        pointer-events: none;
    }
    .hero-public h1 { font-weight: 800; letter-spacing: -0.5px; }
    .hero-public .lead { opacity: .92; }
    .kpi-card {
        background: #fff; border-radius: 16px; padding: 28px 22px;
        box-shadow: 0 6px 24px rgba(0,0,0,.06);
        border: 1px solid #eef2ee; height: 100%;
    }
    .kpi-card .kpi-value { font-size: 2.4rem; font-weight: 800; color: #008751; line-height: 1; }
    .kpi-card .kpi-label { color: #5a6b62; margin-top: 6px; font-weight: 500; }
    .section-title { font-weight: 700; margin-bottom: 8px; }
    .section-sub { color: #6b7a72; margin-bottom: 32px; }
    .bar-row { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
    .bar-row .label { width: 180px; font-size: .9rem; color: #2a3b32; }
    .bar-row .bar  { flex: 1; height: 10px; background: #eef2ee; border-radius: 999px; overflow: hidden; }
    .bar-row .bar > span { display:block; height:100%; background: linear-gradient(90deg, #008751, #FCD116); }
    .bar-row .val { width: 50px; text-align: right; font-weight: 600; color: #2a3b32; font-size:.9rem; }
    .cta-block { background: #FCD116; color: #1a1a1a; border-radius: 18px; padding: 36px; }
    .cta-block .btn { font-weight: 600; }
    .feature-card { border: 1px solid #eef2ee; border-radius: 14px; padding: 22px; height: 100%; background:#fff; }
    .feature-card i { color: #008751; font-size: 1.6rem; }
</style>

<section class="hero-public">
    <div class="container position-relative">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7">
                <span class="badge bg-light text-dark mb-3">ADECOB · Borgou</span>
                <h1 class="display-5 mb-3">La plateforme de gestion des infrastructures des communes du Borgou</h1>
                <p class="lead mb-4">
                    Suivez en temps réel l'état des infrastructures des 8 communes membres de l'ADECOB.
                    Statistiques, cartographie et données ouvertes pour mieux planifier le développement local.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('public.infrastructures') }}" class="btn btn-warning btn-lg">
                        <i class="fas fa-map-marked-alt"></i> Explorer les infrastructures
                    </a>
                    <a href="{{ route('register.form') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-user-plus"></i> S'inscrire
                    </a>
                    <a href="{{ route('login.form') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="kpi-card text-center">
                            <div class="kpi-value">{{ number_format($stats['total_infrastructures'], 0, ',', ' ') }}</div>
                            <div class="kpi-label">Infrastructures recensées</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="kpi-card text-center">
                            <div class="kpi-value">{{ $stats['total_communes'] }}</div>
                            <div class="kpi-label">Communes</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="kpi-card text-center">
                            <div class="kpi-value">{{ $stats['total_types'] }}</div>
                            <div class="kpi-label">Types d'infrastructures suivis</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <h2 class="section-title">Répartition par commune</h2>
                <p class="section-sub">Nombre d'infrastructures enregistrées pour chaque commune membre.</p>
                @php $max = (int) ($byCommune->max('total') ?: 1); @endphp
                @forelse($byCommune as $row)
                    <div class="bar-row">
                        <div class="label">{{ $row['commune'] }}</div>
                        <div class="bar"><span style="width: {{ max(4, ($row['total'] / $max) * 100) }}%"></span></div>
                        <div class="val">{{ $row['total'] }}</div>
                    </div>
                @empty
                    <p class="text-muted">Aucune donnée disponible pour l'instant.</p>
                @endforelse
            </div>

            <div class="col-lg-6">
                <h2 class="section-title">Top types d'infrastructures</h2>
                <p class="section-sub">Les catégories les plus représentées dans le territoire.</p>
                @php $maxT = (int) ($byType->max('total') ?: 1); @endphp
                @forelse($byType as $row)
                    <div class="bar-row">
                        <div class="label">{{ \Illuminate\Support\Str::limit($row->type_infrastructure, 28) }}</div>
                        <div class="bar"><span style="width: {{ max(4, ($row->total / $maxT) * 100) }}%"></span></div>
                        <div class="val">{{ $row->total }}</div>
                    </div>
                @empty
                    <p class="text-muted">Aucune donnée disponible pour l'instant.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center">À propos de la plateforme</h2>
        <p class="section-sub text-center">Pensée pour les acteurs du développement communal du Borgou.</p>
        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="fas fa-database mb-2"></i>
                    <h5 class="fw-bold">Données structurées</h5>
                    <p class="mb-0 text-muted">Inventaire centralisé, photos, géolocalisation et état de fonctionnement de chaque ouvrage.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="fas fa-users-cog mb-2"></i>
                    <h5 class="fw-bold">Espaces dédiés par rôle</h5>
                    <p class="mb-0 text-muted">Administrateurs généraux, administrateurs communaux et agents collecteurs disposent chacun de leur tableau de bord.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="fas fa-shield-alt mb-2"></i>
                    <h5 class="fw-bold">Traçabilité complète</h5>
                    <p class="mb-0 text-muted">Toutes les actions sont auditées : qui a fait quoi et quand, pour une gouvernance transparente.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="cta-block d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h3 class="mb-1 fw-bold">Vous travaillez pour une commune du Borgou ?</h3>
                <p class="mb-0">Rejoignez la plateforme pour collecter et suivre les infrastructures de votre territoire.</p>
            </div>
            <div class="d-flex gap-2 flex-shrink-0">
                <a href="{{ route('register.form') }}" class="btn btn-dark btn-lg">S'inscrire</a>
                <a href="{{ route('public.infrastructures') }}" class="btn btn-outline-dark btn-lg">Voir les données</a>
            </div>
        </div>
    </div>
</section>
@endsection
