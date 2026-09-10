@extends('legal._layout')

@section('title', 'Politique de confidentialité')
@section('doc_title', 'Politique de confidentialité et protection des données personnelles')
@section('doc_version', '1.1')
@section('doc_date', '27/08/2026')

@section('doc_content')

<div class="alert alert-light border">
    <strong>Introduction.</strong> L'Association pour le Développement des Communes du Borgou
    (<strong>ADECOB</strong>) et les <strong>Mairies des 8 communes du Borgou</strong> attachent une
    importance capitale à la sécurité et à la confidentialité des données à caractère personnel. La
    présente Politique a pour but de vous informer en toute transparence sur la manière dont nous
    collectons, stockons, protégeons et traitons vos données lors de votre utilisation de la
    <strong>Plateforme {{ config('app.name') }} de Gestion des Infrastructures Communales</strong>,
    conformément au Livre V de la <em>loi n°2017-20 portant Code du numérique en République du Bénin</em>.
</div>

{{-- ═══════════════════════════════════════════════
     1. RESPONSABLE DU TRAITEMENT
═══════════════════════════════════════════════ --}}
<h2>1. Responsable du traitement et cadre institutionnel</h2>
<p>Les traitements de données à caractère personnel effectués via la Plateforme sont <strong>co-gérés</strong> par :</p>
<ul>
    <li><strong>Le responsable de traitement :</strong> l'Association pour le Développement des Communes du
        Borgou (<strong>ADECOB</strong>), dont le siège social est situé à <strong>N'Dali (Bénin)</strong>.</li>
    <li><strong>Les co-responsables de traitement :</strong> les 8 communes du département du Borgou
        (Bembèrèkè, Kalalé, N'Dali, Nikki, Parakou, Pèrèrè, Sinendé, Tchaourou).</li>
    <li><strong>Statut APDP :</strong> les traitements recensés ci-dessous font l'objet d'une déclaration
        formelle en cours d'immatriculation auprès de l'Autorité de Protection des Données Personnelles
        (<strong>APDP</strong>) du Bénin.</li>
</ul>

{{-- ═══════════════════════════════════════════════
     2. DONNÉES COLLECTÉES ET FINALITÉS
═══════════════════════════════════════════════ --}}
<h2>2. Les données collectées et leurs finalités</h2>
<p>La Plateforme {{ config('app.name') }} limite la collecte des données au strict nécessaire
(principe de minimisation). Nous collectons vos données à travers les cas d'usage techniques suivants :</p>
<ul>
    <li><strong>Gestion des comptes et authentification :</strong> lors de votre inscription en tant
        qu'agent ou administrateur, nous collectons votre <em>nom, prénom, adresse e-mail professionnelle
        (identifiant unique), numéro de téléphone et commune de rattachement</em>. Ces données servent
        exclusivement à gérer votre accès sécurisé et vos permissions par commune (modèle RBAC).</li>
    <li><strong>Sécurisation des accès (MFA et OTP) :</strong> pour les rôles d'administrateurs, un code à
        usage unique (OTP) à 6 chiffres est temporairement traité et envoyé par e-mail pour valider la
        double authentification.</li>
    <li><strong>Recensement du terrain (données infrastructures) :</strong> lors de la saisie d'une
        infrastructure, le <em>nom de l'agent enquêteur</em> est enregistré dans la fiche à des fins de
        traçabilité administrative et de contrôle qualité des relevés. Les photographies d'ouvrages
        importées peuvent indirectement inclure des éléments d'environnement.</li>
    <li><strong>Formulaire de contact :</strong> lorsque vous nous écrivez, nous traitons votre
        <em>nom, adresse e-mail, objet et le contenu de votre message</em> afin de répondre à votre demande
        d'assistance.</li>
    <li><strong>Journalisation et audit de sécurité (logs) :</strong> le système enregistre automatiquement
        un journal de vos actions sensibles (<em>date/heure, adresse IP, type d'action : connexion,
        modification, suppression, exportation de fichiers</em>). Ces journaux servent à détecter les
        tentatives d'intrusion et à garantir l'intégrité du patrimoine informatique communal.</li>
</ul>

{{-- ═══════════════════════════════════════════════
     3. BASES LÉGALES
═══════════════════════════════════════════════ --}}
<h2>3. Bases légales des traitements</h2>
<p>Conformément à l'<strong>article 386 du Code du numérique béninois</strong>, les traitements mis en
œuvre reposent sur les bases juridiques suivantes :</p>
<ol>
    <li><strong>L'exécution d'une mission d'intérêt public :</strong> pour le recensement des
        infrastructures, la planification budgétaire communale et la journalisation de sécurité.</li>
    <li><strong>Le consentement de la personne concernée :</strong> pour la création volontaire du compte
        utilisateur et la soumission du formulaire de contact.</li>
    <li><strong>L'obligation légale et l'intérêt légitime :</strong> pour le maintien de la sécurité du
        système d'information et l'implémentation de la MFA.</li>
</ol>

{{-- ═══════════════════════════════════════════════
     4. DURÉES DE CONSERVATION
═══════════════════════════════════════════════ --}}
<h2>4. Durées de conservation des données</h2>
<p>Vos données ne sont pas conservées indéfiniment. Les règles de purge automatique programmées dans notre
système (Laravel Scheduler) sont les suivantes :</p>
<ul>
    <li><strong>Comptes applicatifs actifs :</strong> conservés pendant toute la durée d'affectation de
        l'agent, et supprimés <strong>3 ans</strong> après sa dernière connexion.</li>
    <li><strong>Comptes en attente de validation :</strong> supprimés automatiquement après
        <strong>6 mois</strong> d'inactivité si aucune validation n'a eu lieu.</li>
    <li><strong>Journaux d'audit et de sécurité (logs) :</strong> conservés pendant <strong>12 mois</strong>
        glissants (étendus à 36 mois uniquement en cas d'incident de sécurité avéré ou d'investigation
        judiciaire).</li>
    <li><strong>Données de contact :</strong> supprimées définitivement <strong>12 mois</strong> après le
        dernier échange.</li>
    <li><strong>Fiches d'infrastructures et nom de l'enquêteur :</strong> les fiches d'infrastructure ont
        une valeur patrimoniale et sont conservées de manière pérenne. Le nom de l'enquêteur de terrain
        attaché à la fiche peut toutefois être anonymisé sur demande après un délai de <strong>5 ans</strong>.</li>
</ul>

{{-- ═══════════════════════════════════════════════
     5. DESTINATAIRES ET TRANSFERTS
═══════════════════════════════════════════════ --}}
<h2>5. Destinataires et transferts de données</h2>
<p>Les données collectées sont strictement réservées à l'usage des mairies du Borgou et de l'équipe
technique de l'ADECOB. Le cloisonnement de notre base de données (MySQL) garantit qu'un administrateur
d'une commune ne peut pas consulter les données de comptes d'une autre commune.</p>

<h3>Sous-traitants techniques et transferts internationaux</h3>
<p>Pour des raisons opérationnelles, certaines requêtes techniques transitent par des prestataires tiers
respectant des clauses de confidentialité strictes :</p>
<ul>
    <li><strong>Hébergement applicatif :</strong> les serveurs et sauvegardes chiffrées de la plateforme
        sont hébergés au sein d'infrastructures Cloud sécurisées (normes ISO 27001).</li>
    <li><strong>Flux cartographiques (OpenStreetMap / Nominatim) :</strong> les requêtes de géocodage
        transmettent uniquement des coordonnées GPS ou des noms de lieux vers les serveurs européens d'OSMF,
        <em>à l'exclusion de toute donnée nominative</em>.</li>
    <li><strong>Protection anti-bot (Google reCAPTCHA v3) :</strong> l'adresse IP et les métadonnées de
        navigation de la page de contact font l'objet d'une analyse automatisée par Google pour bloquer
        les spams.</li>
</ul>

{{-- ═══════════════════════════════════════════════
     6. MESURES DE SÉCURITÉ
═══════════════════════════════════════════════ --}}
<h2>6. Mesures de sécurité organisationnelles et techniques</h2>
<p>Conformément aux obligations de sécurité imposées par le droit béninois, l'ADECOB met en œuvre un
arsenal de protections strictes :</p>
<ul>
    <li><strong>Chiffrement des mots de passe :</strong> aucun mot de passe n'est lisible en clair. Ils
        sont hachés via l'algorithme robuste <strong>bcrypt</strong>.</li>
    <li><strong>Chiffrement des données sensibles :</strong> les numéros de téléphone des agents sont
        chiffrés au repos dans la base de données via un algorithme de niveau industriel
        (<strong>AES-256</strong>).</li>
    <li><strong>Protocoles sécurisés :</strong> l'ensemble des flux d'informations transite obligatoirement
        par le protocole <strong>HTTPS</strong> avec activation de la directive de sécurité
        <strong>HSTS</strong>.</li>
    <li><strong>Limitation des requêtes (rate-limiting) :</strong> les routes d'authentification et les
        formulaires font l'objet d'un blocage automatique temporaire (ex. : 5 tentatives infructueuses en
        10 minutes par IP) pour contrer les attaques par force brute.</li>
</ul>

{{-- ═══════════════════════════════════════════════
     7. VOS DROITS
═══════════════════════════════════════════════ --}}
<h2>7. Vos droits et modalités d'exercice</h2>
<p>En application des <strong>articles 393 et suivants du Code du numérique du Bénin</strong>, vous
disposez de droits étendus sur vos données personnelles :</p>
<ul>
    <li><strong>Droit d'accès :</strong> obtenir la confirmation et la copie des données vous concernant.</li>
    <li><strong>Droit de rectification :</strong> corriger ou mettre à jour des informations inexactes.</li>
    <li><strong>Droit à l'effacement (droit à l'oubli) :</strong> demander la suppression de votre compte
        (incompatible avec le maintien de vos fonctions d'accès sur la plateforme).</li>
    <li><strong>Droit à la limitation et d'opposition :</strong> restreindre certains traitements pour des
        motifs légitimes.</li>
</ul>

<h3>Comment exercer vos droits ?</h3>
<p>Vous pouvez adresser votre demande écrite à notre Référent à la Protection des Données (RSSI) à
l'adresse : <strong>secretariatadecob@yahoo.fr</strong>, en mentionnant obligatoirement en objet :
<code>[DONNÉES PERSONNELLES - EXERCICE DE DROITS]</code>. L'administration s'engage à vous répondre sous
un délai maximum de <strong>30 jours ouvrables</strong>.</p>
<p>Si vous estimez que vos droits ne sont pas respectés, vous disposez du droit légal de former un recours
auprès de l'<strong>Autorité de Protection des Données Personnelles (APDP) du Bénin</strong>
(<a href="https://www.apdp.bj" target="_blank" rel="noopener">www.apdp.bj</a>).</p>

@endsection
