@extends('legal._layout')

@section('title', 'Mentions légales')
@section('doc_title', 'Mentions légales')
@section('doc_version', '1.0')
@section('doc_date', date('d/m/Y'))

@section('doc_content')

<div class="alert alert-light border">
    <strong>Présentation.</strong> Les présentes mentions légales encadrent l'accès et l'utilisation de la
    <strong>Plateforme {{ config('app.name') }} de Gestion des Infrastructures Communales</strong>,
    accessible à l'adresse <strong>{{ url('/') }}</strong>. Elles sont établies conformément au
    <em>Code du numérique de la République du Bénin (loi n°2017-20 du 20 avril 2018)</em>.
</div>

{{-- ═══════════════════════════════════════════════
     1. ÉDITEUR DU SITE
═══════════════════════════════════════════════ --}}
<h2>1. Éditeur du site</h2>
<p>La Plateforme {{ config('app.name') }} est éditée et gérée par :</p>
<ul>
    <li><strong>Association :</strong> Association pour le Développement des Communes du Borgou ({{ config('app.name') }})</li>
    <li><strong>Siège social :</strong> Parakou, République du Bénin</li>
    <li><strong>Téléphone :</strong> [À COMPLÉTER]</li>
    <li><strong>E-mail :</strong> [À COMPLÉTER]</li>
    <li><strong>Représentant légal :</strong> [À COMPLÉTER — Président / Directeur]</li>
    <li><strong>Récépissé de déclaration :</strong> [À COMPLÉTER — n° de récépissé de l'association]</li>
</ul>

{{-- ═══════════════════════════════════════════════
     2. DIRECTEUR DE LA PUBLICATION
═══════════════════════════════════════════════ --}}
<h2>2. Directeur de la publication</h2>
<ul>
    <li><strong>Directeur de publication :</strong> [À COMPLÉTER — Nom, Prénom]</li>
    <li><strong>Contact :</strong> [À COMPLÉTER — e-mail / téléphone]</li>
</ul>

{{-- ═══════════════════════════════════════════════
     3. HÉBERGEMENT
═══════════════════════════════════════════════ --}}
<h2>3. Hébergement</h2>
<p>Le site est hébergé chez :</p>
<ul>
    <li><strong>Hébergeur :</strong> Open.bj (Open Business)</li>
    <li><strong>Type d'hébergement :</strong> Hébergement cloud — offre <strong>DIAMOND</strong></li>
    <li><strong>Site web :</strong> <a href="https://open.bj" target="_blank" rel="noopener">https://open.bj</a></li>
    <li><strong>Adresse de l'hébergeur :</strong> [À COMPLÉTER — adresse d'Open.bj, Bénin]</li>
</ul>
<p><strong>Caractéristiques de l'offre :</strong> 5 domaines, sous-domaines illimités, 100 Go SSD,
1 000 Go de transfert mensuel, 50 comptes e-mail, 5 comptes FTP, 5 bases de données MySQL,
support PHP/Perl/Python, certificat SSL Let's Encrypt, support des sauvegardes.</p>

{{-- ═══════════════════════════════════════════════
     4. PROPRIÉTÉ INTELLECTUELLE
═══════════════════════════════════════════════ --}}
<h2>4. Propriété intellectuelle</h2>
<p>L'ensemble des contenus de la Plateforme (textes, données, cartographies, logo, structure,
base de données) est la propriété de l'association éditrice ou de ses partenaires, sauf mention
contraire.</p>
<p>Le code source de la Plateforme est livré à l'association dans le cadre de la mission
AGORA TERRAIN. Toute reproduction, représentation, modification ou exploitation, totale ou
partielle, sans autorisation préalable écrite de l'éditeur est interdite.</p>

{{-- ═══════════════════════════════════════════════
     5. DONNÉES PERSONNELLES
═══════════════════════════════════════════════ --}}
<h2>5. Données personnelles</h2>
<p>La Plateforme collecte et traite des données à caractère personnel (comptes utilisateurs,
données de recensement des infrastructures).</p>
<ul>
    <li><strong>Responsable de traitement :</strong> l'association éditrice (voir point 1)</li>
    <li><strong>Base légale :</strong> mission d'intérêt public / exécution des missions de l'association, consentement pour les comptes publics</li>
    <li><strong>Finalités :</strong> gestion des comptes, recensement et suivi des infrastructures, statistiques, cartographie publique</li>
    <li><strong>Durée de conservation :</strong> [À COMPLÉTER — ex. durée de vie des comptes + 1 an]</li>
    <li><strong>Droits des personnes :</strong> accès, rectification, effacement, limitation, portabilité, opposition — voir la
        <a href="{{ route('legal.confidentialite') }}">Politique de confidentialité</a></li>
    <li><strong>Contact RGPD :</strong> [À COMPLÉTER — e-mail du référent RGPD]</li>
</ul>

{{-- ═══════════════════════════════════════════════
     6. COOKIES
═══════════════════════════════════════════════ --}}
<h2>6. Cookies</h2>
<p>La Plateforme utilise des <strong>cookies techniques et de session</strong>, strictement
nécessaires à son fonctionnement (authentification, sécurité). Aucun cookie publicitaire n'est
utilisé. Voir la <a href="{{ route('legal.cookies') }}">Politique de gestion des cookies</a>.</p>

{{-- ═══════════════════════════════════════════════
     7. RESPONSABILITÉ
═══════════════════════════════════════════════ --}}
<h2>7. Responsabilité</h2>
<p>L'éditeur s'efforce d'assurer l'exactitude et la mise à jour des informations diffusées, mais
ne peut garantir l'absence d'erreurs ou d'omissions. Les données affichées (notamment issues des
recensements terrain) engagent les communes et agents qui les ont saisies.</p>
<p>L'éditeur ne peut être tenu responsable des dommages résultant de l'utilisation du site ou de
l'impossibilité d'y accéder (interruptions, maintenance, force majeure).</p>

{{-- ═══════════════════════════════════════════════
     8. DROIT APPLICABLE
═══════════════════════════════════════════════ --}}
<h2>8. Droit applicable</h2>
<p>Le présent site est soumis au droit béninois et, le cas échéant, au droit applicable en matière
de protection des données (loi n°2017-20 portant Code du numérique du Bénin, RGPD pour les échanges
avec l'Union européenne). En cas de litige, et à défaut de résolution amiable, les tribunaux
compétents sont ceux de [À COMPLÉTER — Parakou / Cotonou].</p>

@endsection
