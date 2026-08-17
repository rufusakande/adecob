@extends('legal._layout')

@section('title', 'Politique de gestion des cookies')
@section('doc_title', 'Politique de gestion des cookies')
@section('doc_version', '1.0')
@section('doc_date', date('d/m/Y'))

@section('doc_content')

<div class="alert alert-light border">
    <strong>Résumé.</strong> La Plateforme {{ config('app.name') }} utilise uniquement des
    <strong>cookies techniques et de sécurité</strong>, strictement nécessaires à son
    fonctionnement. Aucun cookie publicitaire ou de mesure d'audience n'est utilisé à ce jour.
</div>

{{-- ═══════════════════════════════════════════════
     1. QU'EST-CE QU'UN COOKIE ?
═══════════════════════════════════════════════ --}}
<h2>1. Qu'est-ce qu'un cookie ?</h2>
<p>Un <strong>cookie</strong> est un petit fichier texte déposé sur votre appareil (ordinateur,
tablette, téléphone) lors de la consultation d'un site web. Il permet de reconnaître votre
navigateur, de mémoriser vos préférences ou de sécuriser votre session.</p>

{{-- ═══════════════════════════════════════════════
     2. COOKIES UTILISÉS SUR LA PLATEFORME
═══════════════════════════════════════════════ --}}
<h2>2. Cookies utilisés sur la Plateforme</h2>
<p>La Plateforme {{ config('app.name') }} utilise <strong>uniquement des cookies techniques et
de sécurité</strong>, strictement nécessaires à son fonctionnement :</p>
<div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Cookie</th>
                <th>Type</th>
                <th>Finalité</th>
                <th>Durée de vie</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Cookie de session</strong></td>
                <td>Technique</td>
                <td>Maintenir votre connexion (authentification)</td>
                <td>Durée de la session</td>
            </tr>
            <tr>
                <td><strong>Cookie de protection CSRF</strong></td>
                <td>Sécurité</td>
                <td>Protéger les formulaires contre les attaques</td>
                <td>Durée de la session</td>
            </tr>
            <tr>
                <td><strong>Cookie de préférences</strong></td>
                <td>Technique</td>
                <td>Mémoriser certaines préférences d'affichage</td>
                <td>[À COMPLÉTER — ex. 30 jours]</td>
            </tr>
        </tbody>
    </table>
</div>
<blockquote class="blockquote fst-italic">
    Aucun cookie publicitaire n'est utilisé. Aucun cookie de mesure d'audience n'est actuellement
    en place (voir point 5 pour l'évolution).
</blockquote>

{{-- ═══════════════════════════════════════════════
     3. CONSENTEMENT
═══════════════════════════════════════════════ --}}
<h2>3. Consentement</h2>
<p>Les cookies <strong>techniques et de sécurité</strong> étant <strong>strictement
nécessaires</strong> au fonctionnement du site, ils ne requièrent <strong>pas de consentement
préalable</strong> (dispense prévue par la réglementation).</p>
<p>En cas d'ajout de cookies <strong>non essentiels</strong> (statistiques d'audience,
publicitaires, réseaux sociaux), une <strong>bannière de consentement</strong> sera mise en
place : l'utilisateur pourra accepter ou refuser ces cookies librement, et modifier son choix
à tout moment.</p>

{{-- ═══════════════════════════════════════════════
     4. GÉRER LES COOKIES DANS VOTRE NAVIGATEUR
═══════════════════════════════════════════════ --}}
<h2>4. Gérer les cookies dans votre navigateur</h2>
<p>Vous pouvez à tout moment <strong>bloquer ou supprimer</strong> les cookies via les paramètres
de votre navigateur :</p>
<ul>
    <li><strong>Chrome :</strong> Paramètres → Confidentialité et sécurité → Cookies et données de site</li>
    <li><strong>Firefox :</strong> Paramètres → Vie privée et sécurité → Cookies</li>
    <li><strong>Edge :</strong> Paramètres → Cookies et autorisations de site</li>
    <li><strong>Safari :</strong> Préférences → Confidentialité</li>
</ul>
<div class="alert alert-warning">
    <strong>⚠️ Attention :</strong> la suppression ou le blocage des cookies de session peut
    <strong>désactiver la connexion</strong> et certaines fonctionnalités de la Plateforme.
</div>

{{-- ═══════════════════════════════════════════════
     5. ÉVOLUTIONS PRÉVUES
═══════════════════════════════════════════════ --}}
<h2>5. Évolutions prévues</h2>
<p>Si la Plateforme évolue (outil de statistiques d'audience, partage sur les réseaux sociaux),
la présente politique sera mise à jour et un <strong>mécanisme de consentement</strong> (bannière
+ gestion des préférences) sera intégré.</p>

{{-- ═══════════════════════════════════════════════
     6. CONTACT
═══════════════════════════════════════════════ --}}
<h2>6. Contact</h2>
<p>Pour toute question relative aux cookies ou aux données personnelles, contactez :</p>
<ul>
    <li><strong>Référent RGPD :</strong> [À COMPLÉTER — e-mail]</li>
    <li><strong>Adresse :</strong> [À COMPLÉTER]</li>
</ul>
<p>Voir également la <a href="{{ route('legal.confidentialite') }}">Politique de confidentialité</a>
et les <a href="{{ route('legal.cgu') }}">Conditions Générales d'Utilisation</a>.</p>

@endsection
