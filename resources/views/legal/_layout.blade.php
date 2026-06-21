@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 900px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('public.landing') }}">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">@yield('doc_title')</li>
        </ol>
    </nav>

    <article class="bg-white shadow-sm rounded-3 p-4 p-md-5 legal-doc">
        <header class="border-bottom pb-3 mb-4">
            <h1 class="h3 mb-2 text-success">@yield('doc_title')</h1>
            <p class="text-muted mb-0 small">
                <i class="bi bi-calendar3"></i> Version @yield('doc_version', '1.0') —
                Dernière mise à jour : @yield('doc_date', date('d/m/Y'))
                · <strong>ADECOB</strong> — Association pour le Développement des Communes du Borgou
            </p>
        </header>

        @yield('doc_content')

        <footer class="mt-5 pt-3 border-top small text-muted">
            <p class="mb-1"><strong>Contact responsable de traitement :</strong> ADECOB — Parakou, Bénin · <a href="{{ route('contact') }}">Formulaire de contact</a></p>
            <p class="mb-0">Document conforme à la <em>loi n°2017-20 du 20 avril 2018 portant Code du numérique en République du Bénin</em>.</p>
        </footer>
    </article>
</div>

<style>
.legal-doc h2 { color:#0b6623; font-size:1.4rem; margin-top:2rem; margin-bottom:1rem; border-left:4px solid #FFD100; padding-left:.75rem; }
.legal-doc h3 { font-size:1.1rem; margin-top:1.5rem; color:#333; }
.legal-doc p, .legal-doc li { line-height:1.7; text-align:justify; }
.legal-doc table { width:100%; border-collapse:collapse; margin:1rem 0; font-size:.92rem; }
.legal-doc table th, .legal-doc table td { border:1px solid #dee2e6; padding:.5rem .75rem; vertical-align:top; }
.legal-doc table th { background:#f1f8f1; color:#0b6623; }
.legal-doc .alert { font-size:.95rem; }
</style>
@endsection
