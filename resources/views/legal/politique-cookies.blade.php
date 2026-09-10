@extends('legal._layout')

@section('title', 'Politique de gestion des cookies')
@section('doc_title', 'Politique de gestion des cookies')
@section('doc_version', '1.0')
@section('doc_date', '27/08/2026')

@section('doc_content')

<div class="alert alert-light border">
    <strong>Cadre réglementaire.</strong> La présente Politique de gestion des cookies s'applique à la
    <strong>Plateforme {{ config('app.name') }} de Gestion des Infrastructures Communales</strong>,
    accessible via <strong>{{ url('/') }}</strong>. Elle est établie conformément aux dispositions du
    <em>Code du numérique en République du Bénin (Loi n°2017-20)</em>.
</div>

{{-- ═══════════════════════════════════════════════
     1. DÉFINITION ET NATURE DES COOKIES
═══════════════════════════════════════════════ --}}
<h2>1. Définition et nature des cookies</h2>
<p>Un <strong>cookie</strong> est un fichier texte de taille limitée déposé sur le terminal (ordinateur,
smartphone, tablette) de l'Utilisateur lors de sa navigation sur la Plateforme. Les cookies permettent
d'assurer la sécurité des sessions, de mémoriser les paramètres d'affichage et de fluidifier l'accès aux
services.</p>

{{-- ═══════════════════════════════════════════════
     2. COOKIES STRICTEMENT NÉCESSAIRES ET DE SÉCURITÉ
═══════════════════════════════════════════════ --}}
<h2>2. Typologie des cookies strictement nécessaires et de sécurité</h2>
<p>Conformément à l'analyse technique de l'application (framework Laravel 10) et au Registre général des
traitements, la Plateforme utilise de manière exclusive des <strong>cookies techniques et fonctionnels
indispensables</strong> à la fourniture sécurisée du service public intercommunal. <strong>Aucun traçage
publicitaire n'est mis en œuvre.</strong></p>
<div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Nom du cookie</th>
                <th>Catégorie</th>
                <th>Finalité précise</th>
                <th>Durée de conservation</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>armani_session</code></td>
                <td>Technique</td>
                <td>Maintien de l'état de la session de l'utilisateur et sécurisation de l'authentification des agents communaux</td>
                <td>Durée de la session</td>
            </tr>
            <tr>
                <td><code>XSRF-TOKEN</code></td>
                <td>Sécurité</td>
                <td>Protection des formulaires de saisie et de contact contre les attaques par contrefaçon de requête intersite (CSRF)</td>
                <td>Durée de la session</td>
            </tr>
            <tr>
                <td><code>armani_prefs</code></td>
                <td>Technique</td>
                <td>Mémorisation des préférences de l'interface (état des volets de cartographie OpenStreetMap, filtres par défaut)</td>
                <td>30 jours</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ═══════════════════════════════════════════════
     3. GESTION DU CONSENTEMENT ET DISPENSE
═══════════════════════════════════════════════ --}}
<h2>3. Gestion du consentement et dispense</h2>
<p>Les identifiants et cookies listés ci-dessus bénéficient d'une <strong>dispense légale de recueil
préalable du consentement</strong>, car ils ont pour finalité exclusive de permettre ou faciliter la
communication par voie électronique et sont <strong>strictement nécessaires</strong> à la fourniture du
service expressément demandé par l'Utilisateur (authentification des agents de collecte, soumission du
formulaire de contact).</p>

{{-- ═══════════════════════════════════════════════
     4. PARAMÉTRAGE DES TERMINAUX
═══════════════════════════════════════════════ --}}
<h2>4. Paramétrage des terminaux via les navigateurs internet</h2>
<p>L'Utilisateur dispose de la faculté technique de s'opposer à l'enregistrement de ces cookies ou de les
supprimer en configurant directement les options de confidentialité de son logiciel de navigation :</p>
<ul>
    <li><strong>Google Chrome :</strong> Menu Paramètres › Confidentialité et sécurité › Cookies et données de site</li>
    <li><strong>Mozilla Firefox :</strong> Options › Vie privée et sécurité › Section Cookies et données de sites</li>
    <li><strong>Microsoft Edge :</strong> Paramètres › Cookies et autorisations de site › Gérer les cookies</li>
    <li><strong>Apple Safari :</strong> Préférences › Confidentialité › Bloquer tous les cookies</li>
</ul>
<div class="alert alert-warning">
    <strong>⚠️ Point de vigilance technique :</strong> la désactivation ou le blocage systématique des
    cookies de session (<code>armani_session</code>) et de sécurité (<code>XSRF-TOKEN</code>) empêchera
    toute authentification fonctionnelle sur la Plateforme, rendant impossible la saisie ou la modification
    des fiches d'infrastructures par les agents communaux.
</div>

{{-- ═══════════════════════════════════════════════
     5. CLAUSE D'ÉVOLUTION ET D'AUDIT
═══════════════════════════════════════════════ --}}
<h2>5. Clause d'évolution et d'audit</h2>
<p>En cas d'intégration future d'outils complémentaires de mesure d'audience (ex. : Matomo auto-hébergé) ou
de services tiers, la présente politique sera <strong>préalablement révisée</strong> et un bandeau de
recueil explicite du consentement conforme aux directives de l'APDP sera déployé.</p>

{{-- ═══════════════════════════════════════════════
     6. CONTACT INSTITUTIONNEL
═══════════════════════════════════════════════ --}}
<h2>6. Informations de contact institutionnel</h2>
<p>Pour toute demande d'exercice de droits ou clarification sur la protection de vos données, vous pouvez
écrire au <strong>Secrétariat Permanent de l'ADECOB</strong> (N'Dali, Bénin) à l'adresse électronique
officielle <strong>secretariatadecob@yahoo.fr</strong> ou par téléphone au <strong>+229 01 95 64 73 73</strong>.</p>

@endsection
