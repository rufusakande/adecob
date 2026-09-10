@extends('legal._layout')

@section('title', 'Conditions Générales d\'Utilisation')
@section('doc_title', 'Conditions Générales d\'Utilisation (CGU)')
@section('doc_version', '1.0')
@section('doc_date', '27/08/2026')

@section('doc_content')

<div class="alert alert-light border">
    <strong>Objet.</strong> Les présentes Conditions Générales d'Utilisation (CGU) régissent l'accès et
    l'usage de la <strong>Plateforme {{ config('app.name') }} de Gestion des Infrastructures Communales</strong>,
    hébergée sur <strong>{{ url('/') }}</strong>. En accédant à la Plateforme, vous acceptez sans réserve
    les présentes CGU.
</div>

{{-- ═══════════════════════════════════════════════
     ARTICLE 1 — DÉFINITIONS
═══════════════════════════════════════════════ --}}
<h2>Article 1 — Définitions et cadre institutionnel</h2>
<p>Dans les présentes CGU, les termes ci-dessous ont la signification suivante :</p>
<ul>
    <li><strong>Plateforme :</strong> l'application web géo-référencée « {{ config('app.name') }} »
        (accessible via <strong>{{ url('/') }}</strong>) développée sous framework <strong>Laravel 10</strong>
        pour la collecte, la gestion et le suivi des infrastructures communales du Borgou.</li>
    <li><strong>Éditeur / Administrateur :</strong> l'Association pour le Développement des Communes du
        Borgou (<strong>ADECOB</strong>), regroupant les 8 mairies du département. Le siège social de
        l'association est situé à <strong>N'Dali</strong>, République du Bénin.</li>
</ul>

{{-- ═══════════════════════════════════════════════
     ARTICLE 2 — OBJET
═══════════════════════════════════════════════ --}}
<h2>Article 2 — Objet, périmètre et finalités</h2>
<p>La Plateforme {{ config('app.name') }} est un système d'information intercommunal à vocation d'intérêt
général. Elle poursuit les finalités suivantes :</p>
<ul>
    <li>La centralisation et la cartographie publique des infrastructures des <strong>8 communes du
        Borgou</strong> : Bembèrèkè, Kalalé, N'Dali, Nikki, Parakou, Pèrèrè, Sinendé et Tchaourou.</li>
    <li>L'aide à la décision pour la planification des investissements budgétaires communaux
        (période 2023–2030).</li>
</ul>
<p>L'accès public aux données agrégées est <strong>entièrement gratuit</strong>. La Plateforme ne poursuit
aucun but commercial.</p>

{{-- ═══════════════════════════════════════════════
     ARTICLE 3 — ACCÈS ET SÉCURITÉ
═══════════════════════════════════════════════ --}}
<h2>Article 3 — Accès et sécurité technique</h2>
<p>L'accès à l'interface publique (lecture seule) est libre. L'accès aux interfaces de gestion nécessite la
création d'un Compte sécurisé, soumis aux prérequis suivants :</p>
<ul>
    <li>Utilisation d'un navigateur web récent avec JavaScript activé.</li>
    <li>L'utilisation d'une connexion sécurisée via le protocole <strong>HTTPS (HSTS activé)</strong>.</li>
    <li>Pour les comptes à privilèges (Administrateurs communaux et Super-administrateurs), l'activation et
        l'usage de l'<strong>Authentification Multi-Facteurs (MFA)</strong> par code OTP envoyé par e-mail
        sont obligatoires.</li>
</ul>

{{-- ═══════════════════════════════════════════════
     ARTICLE 4 — COMPTES ET RÔLES (RBAC)
═══════════════════════════════════════════════ --}}
<h2>Article 4 — Création et validation des comptes rôles (RBAC)</h2>
<p>L'inscription s'effectue en ligne via un formulaire d'adresse e-mail professionnelle. Tout compte créé
est placé « en attente » et nécessite une <strong>validation manuelle</strong> par l'Administrateur
communal de la mairie concernée avant toute activation.</p>
<p>Les droits d'accès sont strictement limités selon le modèle de contrôle suivant :</p>
<ol>
    <li><strong>Public (non authentifié) :</strong> consultation exclusive en lecture seule des
        cartographies (via fonds de carte OpenStreetMap) et des statistiques générales. Extraction de
        données avancées interdite.</li>
    <li><strong>Agent Collecteur / Enquêteur :</strong> droits de saisie, modification et mise à jour des
        infrastructures <em>uniquement</em> sur le périmètre géographique de sa commune d'affectation.
        Aucun accès aux données des autres communes.</li>
    <li><strong>Administrateur Communal :</strong> supervision complète des données de sa commune,
        validation des données saisies par ses agents, et gestion des accès aux comptes de sa mairie.</li>
    <li><strong>Super-administrateur :</strong> accès global en lecture/écriture sur l'ensemble des
        communes, configuration du système, et accès exclusif aux <em>journaux d'audit de sécurité (logs)</em>.</li>
</ol>

{{-- ═══════════════════════════════════════════════
     ARTICLE 5 — OBLIGATIONS ET COMPORTEMENTS INTERDITS
═══════════════════════════════════════════════ --}}
<h2>Article 5 — Obligations de l'utilisateur et comportements interdits</h2>
<p>L'Utilisateur s'engage à utiliser la plateforme de manière loyale et conforme à sa destination de
service public. Sont expressément interdits :</p>
<ul>
    <li>Le partage d'identifiants ou l'usage d'un compte par plusieurs agents.</li>
    <li>L'introduction volontaire de données fausses, fictives ou erronées (sabotage de recensement).</li>
    <li>L'extraction massive automatisée de données par des robots (scraping) en dehors des modules
        d'exportation (Excel/PDF) prévus pour les agents.</li>
    <li>Toute tentative d'intrusion, de contournement des privilèges (élévation de rôle) ou d'attaque par
        déni de service.</li>
</ul>

{{-- ═══════════════════════════════════════════════
     ARTICLE 6 — SANCTIONS
═══════════════════════════════════════════════ --}}
<h2>Article 6 — Sanctions et responsabilités pénales (Code du numérique)</h2>
<p>Conformément aux <strong>articles 549 et suivants de la Loi n°2017-20 portant Code du numérique en
République du Bénin</strong>, toute entrave au fonctionnement d'un système de traitement automatisé de
données, toute falsification de données informatiques ou tout accès frauduleux constitue une infraction
pénale.</p>
<p>L'ADECOB et l'ensemble des mairies se réservent le droit de suspendre immédiatement tout compte
contrevenant et d'engager des poursuites judiciaires devant les juridictions compétentes, y compris la
<strong>CRIET</strong>, en cas de dégradation ou de manipulation malveillante des données du patrimoine
communal.</p>

{{-- ═══════════════════════════════════════════════
     ARTICLE 7 — PROPRIÉTÉ INTELLECTUELLE
═══════════════════════════════════════════════ --}}
<h2>Article 7 — Propriété intellectuelle et statut des données</h2>
<ul>
    <li><strong>Propriété de l'application :</strong> le code source Laravel, l'architecture, la charte
        graphique et le logo de la plateforme {{ config('app.name') }} sont la propriété exclusive de
        l'ensemble des mairies.</li>
    <li><strong>Propriété des données infrastructures :</strong> les données métier et relevés
        géographiques saisis sur la Plateforme restent la propriété inaliénable des mairies respectives du
        Borgou qui les ont générées.</li>
</ul>

{{-- ═══════════════════════════════════════════════
     ARTICLE 8 — DONNÉES PERSONNELLES
═══════════════════════════════════════════════ --}}
<h2>Article 8 — Protection des données personnelles</h2>
<p>Les traitements de données personnelles liés à la gestion des comptes (noms, prénoms, emails, numéros de
téléphone chiffrés AES-256) et aux formulaires de contact sont effectués conformément au Livre V du Code du
numérique béninois. Les traitements sont en cours de déclaration auprès de l'<strong>Autorité de Protection
des Données Personnelles (APDP)</strong> du Bénin. Pour exercer vos droits d'accès, de rectification ou
d'effacement, contactez l'administration à l'adresse : <strong>secretariatadecob@yahoo.fr</strong>.</p>

{{-- ═══════════════════════════════════════════════
     ARTICLE 9 — LIMITATION DE RESPONSABILITÉ
═══════════════════════════════════════════════ --}}
<h2>Article 9 — Limitation de responsabilité technique</h2>
<p>L'ADECOB met en œuvre tous les moyens raisonnables pour assurer la disponibilité du service 24h/24 et
7j/7. Toutefois, sa responsabilité ne saurait être engagée en cas de :</p>
<ul>
    <li>Coupures d'électricité ou défaillances des réseaux de télécommunication locaux au Bénin.</li>
    <li>Inexactitudes matérielles dans les fiches d'infrastructures : la validité des données terrain
        incombe exclusivement aux agents enquêteurs et aux mairies qui valident les saisies.</li>
</ul>

{{-- ═══════════════════════════════════════════════
     ARTICLE 10 — DROIT APPLICABLE
═══════════════════════════════════════════════ --}}
<h2>Article 10 — Droit applicable et attribution de juridiction</h2>
<p>Les présentes CGU sont exclusivement régies par le <strong>droit béninois</strong>. Tout litige relatif
à l'utilisation de la plateforme {{ config('app.name') }}, à défaut de résolution amiable dans un délai de
30 jours, sera soumis à la compétence exclusive des tribunaux du Bénin, en particulier le
<strong>Tribunal de Première Instance</strong>.</p>

@endsection
