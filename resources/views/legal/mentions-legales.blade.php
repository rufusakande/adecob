@extends('legal._layout')

@section('title', 'Mentions légales')
@section('doc_title', 'Mentions légales')
@section('doc_version', '1.0')
@section('doc_date', '27/08/2026')

@section('doc_content')

<div class="alert alert-light border">
    <strong>Présentation.</strong> Les présentes mentions légales encadrent l'accès et l'utilisation de la
    <strong>Plateforme {{ config('app.name') }} de Gestion des Infrastructures Communales</strong>,
    accessible à l'adresse <strong>{{ url('/') }}</strong>. Elles sont établies conformément au
    <em>Code du numérique de la République du Bénin (loi n°2017-20 du 20 avril 2018)</em>.
</div>

{{-- ═══════════════════════════════════════════════
     1. ÉDITEUR DU SITE ET CO-RESPONSABILITÉ
═══════════════════════════════════════════════ --}}
<h2>1. Éditeur du site et co-responsabilité</h2>
<p>La Plateforme {{ config('app.name') }} est <strong>co-gérée par les Communes du Borgou</strong>
et administrée techniquement par :</p>
<ul>
    <li><strong>Propriétaire / Éditeur :</strong> l'Association pour le Développement des Communes du
        Borgou (ADECOB)</li>
    <li><strong>Statut :</strong> association de collectivités locales (loi 1901), regroupant l'ensemble
        des mairies du Borgou</li>
    <li><strong>Siège social :</strong> N'Dali, Département du Borgou, République du Bénin</li>
    <li><strong>Téléphone :</strong> +229 01 95 64 73 73</li>
    <li><strong>E-mail :</strong> secretariatadecob@yahoo.fr</li>
</ul>

{{-- ═══════════════════════════════════════════════
     2. DIRECTEUR DE LA PUBLICATION
═══════════════════════════════════════════════ --}}
<h2>2. Directeur de la publication</h2>
<ul>
    <li><strong>Directeur de publication :</strong> ADECOB</li>
    <li><strong>Contact :</strong> secretariatadecob@yahoo.fr</li>
</ul>

{{-- ═══════════════════════════════════════════════
     3. HÉBERGEMENT
═══════════════════════════════════════════════ --}}
<h2>3. Hébergement</h2>
<p>Le site est hébergé sur le territoire national béninois conformément aux recommandations de
souveraineté numérique :</p>
<ul>
    <li><strong>Hébergeur :</strong> Open.bj (Open Business)</li>
    <li><strong>Type d'hébergement :</strong> hébergement cloud — offre <strong>DIAMOND</strong></li>
    <li><strong>Site web :</strong> <a href="https://open.bj" target="_blank" rel="noopener">https://open.bj</a></li>
</ul>

{{-- ═══════════════════════════════════════════════
     4. PROPRIÉTÉ INTELLECTUELLE
═══════════════════════════════════════════════ --}}
<h2>4. Propriété intellectuelle</h2>
<p>L'ensemble des contenus de la Plateforme (textes, données publiques, cartographies numériques, logos,
structures applicatives, bases de données) est la <strong>propriété conjointe de l'ADECOB et des Communes
membres du Borgou</strong>, ou fait l'objet d'un droit d'utilisation accordé par ses partenaires. Toute
reproduction, représentation ou extraction non autorisée des bases de données géographiques est strictement
interdite sans accord écrit préalable de l'ADECOB.</p>

{{-- ═══════════════════════════════════════════════
     5. PROTECTION DES DONNÉES PERSONNELLES
═══════════════════════════════════════════════ --}}
<h2>5. Protection des données personnelles (Conformité APDP)</h2>
<p>La Plateforme collecte et traite des données à caractère personnel (gestion des comptes des agents
communaux, données de recensement terrain des infrastructures incluant parfois des données nominatives ou
de géolocalisation).</p>
<ul>
    <li><strong>Co-responsables de traitement :</strong> l'ADECOB et les Mairies du Borgou partenaires.</li>
    <li><strong>Conformité légale :</strong> le traitement de ces données est effectué en stricte conformité
        avec le Livre V du <em>Code du numérique du Bénin</em>.</li>
    <li><strong>Formalités APDP :</strong> le traitement fait l'objet des formalités préalables obligatoires
        auprès de l'Autorité de Protection des Données Personnelles (APDP) — numéro en cours d'immatriculation.</li>
    <li><strong>Base légale :</strong> exécution d'une mission d'intérêt public et exercice de l'autorité
        publique dévolue aux communes.</li>
    <li><strong>Droits des personnes :</strong> toute personne inscrite ou recensée dispose d'un droit
        d'accès, de rectification, d'opposition et de suppression de ses données. Pour l'exercer, contactez
        le délégué aux données : <strong>secretariatadecob@yahoo.fr</strong>. Voir également la
        <a href="{{ route('legal.confidentialite') }}">Politique de confidentialité</a>.</li>
</ul>

{{-- ═══════════════════════════════════════════════
     6. COOKIES
═══════════════════════════════════════════════ --}}
<h2>6. Cookies</h2>
<p>La Plateforme utilise exclusivement des <strong>cookies techniques et de session</strong>, strictement
nécessaires à la sécurisation des accès (authentification des agents de mairie, maintien de la connexion).
Aucun traçage publicitaire n'est mis en œuvre. Voir la
<a href="{{ route('legal.cookies') }}">Politique de gestion des cookies</a>.</p>

{{-- ═══════════════════════════════════════════════
     7. RESPONSABILITÉ
═══════════════════════════════════════════════ --}}
<h2>7. Responsabilité</h2>
<p>L'éditeur et les communes partenaires s'efforcent d'assurer l'exactitude des informations cartographiques
et techniques diffusées. Toutefois, les données issues des recensements terrain engagent directement la
responsabilité des communes et des agents assermentés qui les ont saisies.</p>
<p>L'éditeur ne saurait être tenu responsable des interruptions temporaires de la plateforme pour cause de
maintenance ou d'indisponibilité des réseaux de télécommunication locaux.</p>

{{-- ═══════════════════════════════════════════════
     8. DROIT APPLICABLE ET ATTRIBUTION DE JURIDICTION
═══════════════════════════════════════════════ --}}
<h2>8. Droit applicable et attribution de juridiction</h2>
<p>Le présent site et ses mentions légales sont exclusivement soumis au <strong>droit béninois</strong>.
En cas de litige relatif à l'utilisation de la plateforme {{ config('app.name') }}, et à défaut de
résolution amiable, compétence exclusive est attribuée aux <strong>tribunaux compétents de la République
du Bénin</strong>.</p>

@endsection
