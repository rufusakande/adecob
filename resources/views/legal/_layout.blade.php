@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 960px;">

    {{-- Fil d'Ariane --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('public.landing') }}">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">@yield('doc_title')</li>
        </ol>
    </nav>

    {{-- Navigation entre les documents légaux --}}
    <div class="d-flex flex-wrap gap-2 mb-4 small">
        <a href="{{ route('legal.pssi') }}"
           class="btn btn-sm {{ request()->routeIs('legal.pssi') ? 'btn-success' : 'btn-outline-secondary' }}">
            <i class="bi bi-shield-lock"></i> PSSI
        </a>
        <a href="{{ route('legal.confidentialite') }}"
           class="btn btn-sm {{ request()->routeIs('legal.confidentialite') ? 'btn-success' : 'btn-outline-secondary' }}">
            <i class="bi bi-eye-slash"></i> Confidentialité
        </a>
        <a href="{{ route('legal.cgu') }}"
           class="btn btn-sm {{ request()->routeIs('legal.cgu') ? 'btn-success' : 'btn-outline-secondary' }}">
            <i class="bi bi-file-text"></i> CGU
        </a>
        <a href="{{ route('legal.registre') }}"
           class="btn btn-sm {{ request()->routeIs('legal.registre') ? 'btn-success' : 'btn-outline-secondary' }}">
            <i class="bi bi-journal-bookmark"></i> Registre des traitements
        </a>
    </div>

    <article class="bg-white shadow-sm rounded-3 p-4 p-md-5 legal-doc">

        {{-- En-tête du document --}}
        <header class="border-bottom pb-3 mb-4">
            <div class="d-flex align-items-start gap-3">
                <div class="flex-shrink-0 text-success fs-2">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h1 class="h3 mb-1 text-success">@yield('doc_title')</h1>
                    <p class="text-muted mb-0 small">
                        <i class="bi bi-tag"></i> Version @yield('doc_version', '1.0')
                        &nbsp;·&nbsp;
                        <i class="bi bi-calendar3"></i> Mise à jour le @yield('doc_date', date('d/m/Y'))
                        &nbsp;·&nbsp;
                        <strong>ADECOB</strong> — Association pour le Développement des Communes du Borgou
                    </p>
                    <p class="text-muted mb-0 small mt-1">
                        <i class="bi bi-geo-alt"></i> Parakou, République du Bénin
                        &nbsp;·&nbsp;
                        Conforme à la <em>loi n°2017-20 portant Code du numérique du Bénin</em>
                    </p>
                </div>
            </div>
        </header>

        {{-- Contenu principal du document --}}
        @yield('doc_content')

        {{-- Pied de document --}}
        <footer class="mt-5 pt-4 border-top">
            <div class="row g-3 small text-muted">
                <div class="col-md-6">
                    <p class="mb-1"><strong><i class="bi bi-envelope"></i> Contact responsable de traitement :</strong></p>
                    <p class="mb-0">ADECOB — Parakou, Bénin<br>
                        <a href="{{ route('contact.form') }}">Formulaire de contact officiel</a>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong><i class="bi bi-bank"></i> Autorité de contrôle :</strong></p>
                    <p class="mb-0">APDP — Autorité de Protection des Données Personnelles<br>
                        République du Bénin, Cotonou
                    </p>
                </div>
            </div>
            <hr class="mt-3">
            <p class="mb-0 text-center small text-muted">
                Document conforme à la <em>loi n°2017-20 du 20 avril 2018 portant Code du numérique en République du Bénin</em>
                &nbsp;·&nbsp; <strong>ADECOB</strong> &copy; {{ date('Y') }}
            </p>
        </footer>

    </article>
</div>

<style>
/* ── Styles des documents légaux ────────────────────────────────── */
.legal-doc h2 {
    color: #0b6623;
    font-size: 1.35rem;
    margin-top: 2rem;
    margin-bottom: 0.85rem;
    border-left: 4px solid #FFD100;
    padding-left: .75rem;
}
.legal-doc h3 {
    font-size: 1.05rem;
    margin-top: 1.4rem;
    margin-bottom: .5rem;
    color: #1a1a1a;
    font-weight: 600;
}
.legal-doc p,
.legal-doc li {
    line-height: 1.75;
    text-align: justify;
}
.legal-doc table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0 1.5rem;
    font-size: .91rem;
}
.legal-doc table th,
.legal-doc table td {
    border: 1px solid #dee2e6;
    padding: .55rem .8rem;
    vertical-align: top;
}
.legal-doc table th {
    background: #f1f8f1;
    color: #0b6623;
    font-weight: 600;
    white-space: nowrap;
}
.legal-doc table tr:hover td {
    background: #fafff8;
}
.legal-doc .alert {
    font-size: .95rem;
    border-radius: .5rem;
}
.legal-doc code {
    background: #f4f4f4;
    padding: .1rem .35rem;
    border-radius: .25rem;
    font-size: .88em;
    color: #d63384;
}
@media print {
    .breadcrumb, .d-flex.flex-wrap, footer { display: none !important; }
    .legal-doc { box-shadow: none !important; padding: 0 !important; }
}
</style>
@endsection
