@extends('layouts.app')

@section('title', config('app.name') . ' — Plateforme publique')

@section('content')
<style>
    /* ============ PAGE D'ACCUEIL PREMIUM ============ */
    :root {
        --lp-green: #0b6623;
        --lp-green-2: #0a7a2a;
        --lp-green-dark: #064a1a;
        --lp-gold: #FFD100;
        --lp-text: #22312a;
        --lp-muted: #6b7a72;
    }
    .lp-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #064a1a 0%, #0b6623 45%, #0a7a2a 100%);
        color: #fff;
        padding: 90px 0 110px;
    }
    .lp-hero::before {
        content: "";
        position: absolute; inset: 0;
        background:
            radial-gradient(circle at 15% 10%, rgba(255,209,0,.14), transparent 40%),
            radial-gradient(circle at 85% 25%, rgba(255,255,255,.08), transparent 45%),
            radial-gradient(circle at 60% 90%, rgba(255,209,0,.10), transparent 40%);
        pointer-events: none;
    }
    .lp-hero::after {
        content: "";
        position: absolute; inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.06) 1px, transparent 1px);
        background-size: 28px 28px;
        -webkit-mask-image: linear-gradient(180deg, rgba(0,0,0,.5), transparent 90%);
        mask-image: linear-gradient(180deg, rgba(0,0,0,.5), transparent 90%);
        pointer-events: none;
    }
    .lp-hero__inner { position: relative; z-index: 2; }
    .lp-badge {
        display: inline-flex; align-items: center; gap: .5rem;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.25);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        padding: .4rem .9rem; border-radius: 999px;
        font-size: .78rem; font-weight: 600; letter-spacing: .5px;
        text-transform: uppercase;
        margin-bottom: 1.25rem;
    }
    .lp-badge__dot { width: 8px; height: 8px; border-radius: 50%; background: var(--lp-gold); box-shadow: 0 0 0 3px rgba(255,209,0,.25); }
    .lp-hero__title { color: #fff; font-weight: 800; font-size: clamp(1.9rem, 4.5vw, 3.1rem); line-height: 1.12; letter-spacing: -.5px; margin-bottom: 1.1rem; }
    .lp-hero__title .text-warning { color: var(--lp-gold) !important; }
    .lp-hero__sub { font-size: 1.05rem; opacity: .92; max-width: 560px; margin-bottom: 1.75rem; }
    .lp-hero__cta { display: flex; flex-wrap: wrap; gap: .9rem; margin-bottom: 1.5rem; }
    .lp-btn {
        display: inline-flex; align-items: center; gap: .6rem;
        padding: .85rem 1.6rem; border-radius: 14px; font-weight: 700; font-size: .95rem;
        text-decoration: none; transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .lp-btn--gold { background: var(--lp-gold); color: #1a1a1a; box-shadow: 0 10px 25px rgba(255,209,0,.3); }
    .lp-btn--gold:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(255,209,0,.4); color: #000; }
    .lp-btn--ghost { background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.3); color: #fff; backdrop-filter: blur(6px); }
    .lp-btn--ghost:hover { background: rgba(255,255,255,.2); color: #fff; transform: translateY(-2px); }
    .lp-hero__trust { display: flex; flex-wrap: wrap; gap: 1.2rem; }
    .lp-hero__trust div { display: inline-flex; align-items: center; gap: .45rem; font-size: .85rem; opacity: .92; }
    .lp-hero__trust i { color: var(--lp-gold); }

    /* Stats hero (verre dépoli) */
    .lp-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .lp-stats__card {
        background: rgba(255,255,255,.10);
        border: 1px solid rgba(255,255,255,.2);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 20px; padding: 1.6rem 1.2rem; text-align: center;
        transition: transform .25s ease, background .25s ease;
    }
    .lp-stats__card:hover { transform: translateY(-4px); background: rgba(255,255,255,.16); }
    .lp-stats__card--big { grid-column: 1 / -1; padding: 2rem; }
    .lp-stats__value { font-size: 2.4rem; font-weight: 800; color: var(--lp-gold); line-height: 1; }
    .lp-stats__card--big .lp-stats__value { font-size: 3.2rem; }
    .lp-stats__label { margin-top: .5rem; font-size: .85rem; opacity: .9; font-weight: 500; }

    /* Sections */
    .lp-section { padding: 70px 0; }
    .lp-section--alt { background: #f0f5f1; }
    .lp-eyebrow { text-transform: uppercase; letter-spacing: 2px; font-weight: 700; color: var(--lp-green); font-size: .78rem; }
    .lp-title { font-weight: 800; color: var(--lp-text); font-size: clamp(1.5rem, 3vw, 2rem); }
    .lp-sub { color: var(--lp-muted); }

    /* Cartes de répartition */
    .lp-card {
        background: #fff; border-radius: 20px; padding: 1.8rem; height: 100%;
        border: 1px solid #e9f0eb; box-shadow: 0 8px 30px rgba(6,74,26,.06);
    }
    .lp-bar-row { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
    .lp-bar-row .lp-bar-label { width: 150px; font-size: .85rem; font-weight: 600; color: var(--lp-text); flex-shrink: 0; }
    .lp-bar-row .lp-bar { flex: 1; height: 12px; background: #edf3ef; border-radius: 999px; overflow: hidden; }
    .lp-bar-row .lp-bar > span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--lp-green), var(--lp-green-2)); }
    .lp-bar-row .lp-bar-val { width: 44px; text-align: right; font-weight: 700; color: var(--lp-green); font-size: .88rem; }

    /* Fonctionnalités */
    .lp-feature {
        background: #fff; border: 1px solid #e9f0eb; border-radius: 20px; padding: 1.8rem; height: 100%;
        box-shadow: 0 8px 30px rgba(6,74,26,.05); transition: transform .25s ease, box-shadow .25s ease;
    }
    .lp-feature:hover { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(6,74,26,.12); }
    .lp-feature__icon {
        width: 56px; height: 56px; border-radius: 16px; margin-bottom: 1rem;
        background: linear-gradient(135deg, #e8f5ec, #d5ecdd);
        display: flex; align-items: center; justify-content: center;
        color: var(--lp-green); font-size: 1.5rem;
    }
    .lp-feature h5 { font-weight: 700; color: var(--lp-text); }
    .lp-feature p { color: var(--lp-muted); }

    /* CTA */
    .lp-cta {
        position: relative; overflow: hidden;
        background: linear-gradient(120deg, #064a1a, #0b6623 60%, #0a7a2a);
        border-radius: 24px; padding: 2.5rem 2.2rem; color: #fff;
        display: flex; flex-direction: column; gap: 1.4rem;
    }
    .lp-cta::before {
        content: ""; position: absolute; right: -60px; top: -60px; width: 240px; height: 240px; border-radius: 50%;
        background: radial-gradient(circle, rgba(255,209,0,.18), transparent 70%);
    }
    .lp-cta h3 { font-weight: 800; color: #fff; }
    .lp-cta p { opacity: .9; }
    .lp-cta__actions { display: flex; flex-wrap: wrap; gap: .8rem; position: relative; z-index: 1; }

    @media (max-width: 767.98px) {
        .lp-hero { padding: 64px 0 76px; text-align: center; }
        .lp-hero__cta, .lp-hero__trust { justify-content: center; }
        .lp-hero__sub { margin-left: auto; margin-right: auto; }
        .lp-stats { max-width: 380px; margin: 0 auto; }
        .lp-section { padding: 48px 0; }
        .lp-bar-row .lp-bar-label { width: 108px; font-size: .8rem; }
    }
</style>

<section class="lp-hero">
    <div class="container lp-hero__inner">
        <div class="row align-items-center gy-5">
            <div class="col-lg-7">
                <span class="lp-badge"><span class="lp-badge__dot"></span> {{ config('app.name') }} · Borgou — 8 communes</span>
                <h1 class="lp-hero__title">La plateforme de gestion des <span class="text-warning">infrastructures</span> des communes du Borgou</h1>
                <p class="lp-hero__sub">
                    Suivez en temps réel l'état des infrastructures des 8 communes membres de l'{{ config('app.name') }}.
                    Statistiques, cartographie et données ouvertes pour mieux planifier le développement local.
                </p>
                <div class="lp-hero__cta">
                    <a href="{{ route('public.infrastructures') }}" class="lp-btn lp-btn--gold">
                        <i class="fas fa-map-marked-alt"></i> Explorer les infrastructures
                    </a>
                    <a href="{{ route('register.form') }}" class="lp-btn lp-btn--ghost">
                        <i class="fas fa-user-plus"></i> Créer un compte
                    </a>
                </div>
                <div class="lp-hero__trust">
                    <div><i class="fas fa-check-circle"></i> Données ouvertes</div>
                    <div><i class="fas fa-check-circle"></i> Cartographie temps réel</div>
                    <div><i class="fas fa-check-circle"></i> Traçabilité complète</div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="lp-stats">
                    <div class="lp-stats__card lp-stats__card--big">
                        <div class="lp-stats__value">{{ number_format($stats['total_infrastructures'], 0, ',', ' ') }}</div>
                        <div class="lp-stats__label">Infrastructures recensées</div>
                    </div>
                    <div class="lp-stats__card">
                        <div class="lp-stats__value">{{ $stats['total_communes'] }}</div>
                        <div class="lp-stats__label">Communes</div>
                    </div>
                    <div class="lp-stats__card">
                        <div class="lp-stats__value">{{ $stats['total_types'] }}</div>
                        <div class="lp-stats__label">Types suivis</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="lp-section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="lp-eyebrow">Chiffres clés</div>
            <h2 class="lp-title">Répartition des infrastructures</h2>
            <p class="lp-sub">Une vision claire de la répartition sur l'ensemble du territoire du Borgou.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="lp-card">
                    <h5 class="fw-bold mb-1"><i class="fas fa-city me-2" style="color:var(--lp-green);"></i>Répartition par commune</h5>
                    <p class="lp-sub small mb-4">Nombre d'infrastructures enregistrées par commune membre.</p>
                    @php $max = (int) ($byCommune->max('total') ?: 1); @endphp
                    @forelse($byCommune as $row)
                        <div class="lp-bar-row">
                            <div class="lp-bar-label">{{ $row['commune'] }}</div>
                            <div class="lp-bar"><span style="width: {{ max(4, ($row['total'] / $max) * 100) }}%"></span></div>
                            <div class="lp-bar-val">{{ $row['total'] }}</div>
                        </div>
                    @empty
                        <p class="text-muted">Aucune donnée disponible pour l'instant.</p>
                    @endforelse
                </div>
            </div>
            <div class="col-lg-6">
                <div class="lp-card">
                    <h5 class="fw-bold mb-1"><i class="fas fa-tools me-2" style="color:var(--lp-green);"></i>Top types d'infrastructures</h5>
                    <p class="lp-sub small mb-4">Les catégories les plus représentées dans le territoire.</p>
                    @php $maxT = (int) ($byType->max('total') ?: 1); @endphp
                    @forelse($byType as $row)
                        <div class="lp-bar-row">
                            <div class="lp-bar-label">{{ \Illuminate\Support\Str::limit($row->type_infrastructure, 24) }}</div>
                            <div class="lp-bar"><span style="width: {{ max(4, ($row->total / $maxT) * 100) }}%"></span></div>
                            <div class="lp-bar-val">{{ $row->total }}</div>
                        </div>
                    @empty
                        <p class="text-muted">Aucune donnée disponible pour l'instant.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

<section class="lp-section lp-section--alt">
    <div class="container">
        <div class="text-center mb-5">
            <div class="lp-eyebrow">À propos</div>
            <h2 class="lp-title">Une plateforme pensée pour les acteurs du développement</h2>
            <p class="lp-sub">Des outils modernes pour une gouvernance communale transparente et efficace.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lp-feature">
                    <div class="lp-feature__icon"><i class="fas fa-database"></i></div>
                    <h5>Données structurées</h5>
                    <p class="mb-0">Inventaire centralisé, photos, géolocalisation et état de fonctionnement de chaque ouvrage.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lp-feature">
                    <div class="lp-feature__icon"><i class="fas fa-users-cog"></i></div>
                    <h5>Espaces dédiés par rôle</h5>
                    <p class="mb-0">Administrateurs généraux, administrateurs communaux et agents collecteurs disposent chacun de leur tableau de bord.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lp-feature">
                    <div class="lp-feature__icon"><i class="fas fa-shield-alt"></i></div>
                    <h5>Traçabilité complète</h5>
                    <p class="mb-0">Toutes les actions sont auditées : qui a fait quoi et quand, pour une gouvernance transparente.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="lp-section">
    <div class="container">
        <div class="lp-cta d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div class="position-relative" style="z-index:1;">
                <h3 class="mb-1">Vous travaillez pour une commune du Borgou ?</h3>
                <p class="mb-0">Rejoignez la plateforme pour collecter et suivre les infrastructures de votre territoire.</p>
            </div>
            <div class="lp-cta__actions flex-shrink-0">
                <a href="{{ route('register.form') }}" class="lp-btn lp-btn--gold">S'inscrire</a>
                <a href="{{ route('public.infrastructures') }}" class="lp-btn lp-btn--ghost">Voir les données</a>
            </div>
        </div>
    </div>
</section>
@endsection
