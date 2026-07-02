@extends('legal._layout')

@section('title', 'Conditions Générales d\'Utilisation')
@section('doc_title', 'Conditions Générales d\'Utilisation (CGU)')
@section('doc_version', '1.1')
@section('doc_date', '30/06/2026')

@section('doc_content')

{{-- ══════════════════════════════════════════════════════════════════════════
     PRÉAMBULE
═══════════════════════════════════════════════════════════════════════════ --}}

<div class="alert alert-light border">
    <strong>Important — Acceptation des présentes conditions.</strong>
    Les présentes Conditions Générales d'Utilisation (ci-après « <strong>CGU</strong> »)
    constituent le contrat conclu entre l'<strong>Association pour le Développement des
    Communes du Borgou (ADECOB)</strong>, dont le siège social est établi à Parakou,
    République du Bénin, et toute personne physique ou morale (ci-après
    « <strong>l'Utilisateur</strong> ») accédant ou utilisant la
    <strong>Plateforme ADECOB de Gestion des Infrastructures Communales</strong>
    (ci-après « <strong>la Plateforme</strong> »).
    <br><br>
    Tout accès à la Plateforme, qu'il soit public ou authentifié, vaut
    <strong>acceptation pleine, entière et sans réserve</strong> des présentes CGU,
    dans leur version en vigueur à la date de la connexion. L'Utilisateur qui n'accepte
    pas ces conditions est invité à ne pas utiliser la Plateforme.
</div>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 1 — DÉFINITIONS
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 1 — Définitions</h2>

<p>Dans les présentes CGU, les termes ci-dessous ont la signification suivante :</p>

<table>
    <thead>
        <tr>
            <th style="width:22%">Terme</th>
            <th>Définition</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Plateforme</strong></td>
            <td>Application web dénommée « Plateforme ADECOB de Gestion des Infrastructures
            Communales », hébergée et exploitée par l'ADECOB, permettant la collecte, la
            gestion, le suivi et la consultation des infrastructures communales
            géo-référencées du département du Borgou (Bénin).</td>
        </tr>
        <tr>
            <td><strong>ADECOB</strong></td>
            <td>Association pour le Développement des Communes du Borgou, personne morale
            de droit béninois, éditrice et responsable de traitement de la Plateforme,
            dont le siège est situé à Parakou, Bénin. L'ADECOB est financée dans le cadre
            de ce projet par la Direction du Développement et de la Coopération suisse
            (DDC Suisse).</td>
        </tr>
        <tr>
            <td><strong>Utilisateur</strong></td>
            <td>Toute personne physique accédant à la Plateforme, que ce soit à titre
            public (sans authentification) ou à titre authentifié (avec un Compte).</td>
        </tr>
        <tr>
            <td><strong>Public</strong></td>
            <td>Catégorie d'Utilisateurs accédant à la Plateforme sans authentification,
            bénéficiant d'un droit de consultation en lecture seule des données et
            statistiques publiques.</td>
        </tr>
        <tr>
            <td><strong>Agent collecteur</strong></td>
            <td>Utilisateur authentifié, agent d'une commune du Borgou, habilité à saisir,
            modifier et suivre les données relatives aux infrastructures de sa commune
            d'affectation.</td>
        </tr>
        <tr>
            <td><strong>Administrateur communal</strong></td>
            <td>Utilisateur authentifié disposant de droits élevés sur une commune donnée :
            validation des agents, supervision et correction des données de sa commune,
            génération de rapports communaux.</td>
        </tr>
        <tr>
            <td><strong>Super-administrateur</strong></td>
            <td>Utilisateur authentifié disposant des droits les plus étendus sur la
            Plateforme ; habilité à gérer l'ensemble des communes, des comptes utilisateurs,
            des paramètres système et de l'audit global.</td>
        </tr>
        <tr>
            <td><strong>Compte</strong></td>
            <td>Espace personnel sécurisé, accessible par identifiant et mot de passe
            (et le cas échéant par authentification multi-facteurs), créé à la suite
            d'une demande d'inscription validée par un administrateur.</td>
        </tr>
        <tr>
            <td><strong>Données métier</strong></td>
            <td>Données relatives aux infrastructures communales saisies dans la Plateforme
            (localisation géographique, type, état, capacité, photos, indicateurs, etc.),
            propriété des communes concernées, hébergées et traitées par l'ADECOB.</td>
        </tr>
        <tr>
            <td><strong>Infrastructure</strong></td>
            <td>Tout équipement ou ouvrage communal géo-référencé inventorié dans la
            Plateforme : établissements scolaires, points d'eau, latrines/blocs sanitaires,
            marchés, centres et cases de santé, et tout autre équipement public
            des 8 communes du Borgou concernées.</td>
        </tr>
        <tr>
            <td><strong>CGU</strong></td>
            <td>Les présentes Conditions Générales d'Utilisation, dans leur version
            en vigueur au moment de chaque connexion.</td>
        </tr>
        <tr>
            <td><strong>PSSI</strong></td>
            <td>Politique de Sécurité des Systèmes d'Information de la Plateforme,
            document décrivant les mesures techniques et organisationnelles de sécurité
            applicables. Consultable à l'adresse : <a href="{{ route('legal.pssi') }}">Politique de sécurité (PSSI)</a>.</td>
        </tr>
    </tbody>
</table>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 2 — PRÉSENTATION DE LA PLATEFORME
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 2 — Présentation de la Plateforme</h2>

<h3>2.1 Objet et finalités</h3>
<p>
    La Plateforme ADECOB de Gestion des Infrastructures Communales est un système
    d'information à vocation d'intérêt général, dont les finalités principales sont :
</p>
<ul>
    <li><strong>La collecte et la centralisation</strong> des données relatives aux
        infrastructures communales (localisation GPS, caractéristiques techniques, état,
        indicateurs de service) ;</li>
    <li><strong>La gestion et le suivi</strong> du cycle de vie des infrastructures
        (création, mise à jour, signalement de dysfonctionnements, planification) ;</li>
    <li><strong>La valorisation et la diffusion</strong> de données agrégées et de
        statistiques au bénéfice des décideurs locaux, des partenaires et du public ;</li>
    <li><strong>L'aide à la décision</strong> en matière de planification des
        investissements communaux dans le département du Borgou.</li>
</ul>

<h3>2.2 Périmètre géographique</h3>
<p>
    La Plateforme couvre l'ensemble des <strong>8 communes du département du Borgou</strong>,
    à savoir : Bembèrèkè, Kalalé, N'Dali, Nikki, Parakou, Pèrèrè, Sinendé et Tchaourou.
    Elle répertorie à ce jour <strong>10 402 infrastructures géo-référencées</strong>
    réparties entre ces communes.
</p>

<h3>2.3 Financement et intérêt public</h3>
<p>
    La Plateforme est développée et exploitée par l'ADECOB dans le cadre d'un projet
    financé par la <strong>Direction du Développement et de la Coopération suisse
    (DDC Suisse)</strong>. Elle poursuit une mission d'intérêt général et ne génère
    aucun bénéfice commercial pour l'ADECOB. Les données publiques sont mises à la
    disposition de toute personne intéressée à titre gratuit, dans le respect des
    présentes CGU.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 3 — ACCÈS À LA PLATEFORME
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 3 — Accès à la Plateforme</h2>

<h3>3.1 Accès public (sans authentification)</h3>
<p>
    Toute personne disposant d'une connexion Internet peut accéder librement et gratuitement
    aux sections publiques de la Plateforme, sans création de compte. Cet accès est limité
    à la <strong>consultation en lecture seule</strong> des données et statistiques rendues
    publiques par les communes et l'ADECOB (cartographie, indicateurs agrégés, tableau de bord
    public, etc.).
</p>

<h3>3.2 Accès authentifié</h3>
<p>
    L'accès aux fonctionnalités de saisie, de gestion et d'administration requiert la
    création d'un <strong>Compte utilisateur</strong> préalablement validé. La procédure
    d'inscription est décrite à l'article 4. L'accès authentifié est réservé aux agents
    des communes du Borgou, aux personnels de l'ADECOB et, le cas échéant, aux partenaires
    expressément autorisés.
</p>

<h3>3.3 Prérequis techniques</h3>
<p>
    L'accès à la Plateforme nécessite :
</p>
<ul>
    <li>une connexion Internet fonctionnelle ;</li>
    <li>un navigateur web récent et à jour (Chrome, Firefox, Edge ou équivalent) ;</li>
    <li>l'activation de JavaScript dans le navigateur ;</li>
    <li>pour les fonctionnalités cartographiques, l'accès aux tuiles de carte
        (réseau non restreint).</li>
</ul>
<p>
    L'ADECOB ne garantit pas la compatibilité avec les navigateurs obsolètes ou
    les environnements non standards.
</p>

<h3>3.4 Gratuité</h3>
<p>
    L'accès à la Plateforme est <strong>entièrement gratuit</strong> pour tous les
    Utilisateurs. Aucun abonnement, frais d'inscription ou paiement n'est requis.
    Les coûts de connexion Internet restent à la charge de l'Utilisateur.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 4 — CRÉATION ET GESTION DU COMPTE
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 4 — Création et gestion du compte</h2>

<h3>4.1 Procédure d'inscription</h3>
<p>
    La création d'un Compte s'effectue en trois étapes :
</p>
<ol>
    <li><strong>Soumission du formulaire d'inscription :</strong> le demandeur renseigne
        les informations suivantes :
        <ul>
            <li>Nom de famille ;</li>
            <li>Prénom(s) ;</li>
            <li>Adresse électronique professionnelle (email) — sert d'identifiant unique ;</li>
            <li>Numéro de téléphone ;</li>
            <li>Commune d'affectation (parmi les 8 communes du Borgou) ;</li>
            <li>Mot de passe (soumis aux exigences de robustesse définies dans la PSSI).</li>
        </ul>
    </li>
    <li><strong>Mise en attente (statut <em>« en attente »</em>) :</strong> le compte
        n'est pas encore actif. Le demandeur reçoit un email de confirmation de réception
        de sa demande.</li>
    <li><strong>Validation par un administrateur :</strong> l'Administrateur communal ou
        le Super-administrateur examine la demande, vérifie l'identité et les droits du
        demandeur, puis approuve ou rejette le compte. Le demandeur est notifié par email
        de la décision.</li>
</ol>
<p>
    L'ADECOB et les administrateurs communaux se réservent le droit de
    <strong>refuser toute demande d'inscription</strong> sans avoir à en justifier
    les motifs, notamment si le demandeur ne satisfait pas aux conditions requises
    (appartenance à une commune du Borgou, identité non vérifiable, etc.).
</p>

<h3>4.2 Unicité du compte</h3>
<p>
    Chaque personne physique ne peut détenir qu'<strong>un seul et unique Compte</strong>
    sur la Plateforme. La création de comptes multiples est expressément interdite et
    constitue un motif de suspension immédiate de l'ensemble des comptes concernés.
</p>

<h3>4.3 Responsabilités de l'Utilisateur sur son compte</h3>
<p>L'Utilisateur s'engage à :</p>
<ul>
    <li>fournir des informations <strong>exactes, sincères et à jour</strong> lors de
        l'inscription et tout au long de l'utilisation de la Plateforme ;</li>
    <li>maintenir la <strong>confidentialité absolue</strong> de ses identifiants
        (email, mot de passe, code MFA le cas échéant) et à ne les divulguer à aucun tiers ;</li>
    <li>ne <strong>pas partager son Compte</strong> ni ses identifiants avec quiconque,
        quelle qu'en soit la raison ;</li>
    <li>choisir un mot de passe robuste et le renouveler régulièrement conformément
        aux recommandations de la PSSI ;</li>
    <li><strong>signaler sans délai</strong> à l'ADECOB, via le
        <a href="{{ route('contact.form') }}">formulaire de contact</a>, tout accès non
        autorisé, perte, vol ou compromission de ses identifiants ;</li>
    <li>mettre à jour ses informations personnelles en cas de changement (notamment
        l'adresse email et le numéro de téléphone).</li>
</ul>
<p>
    Toute action effectuée depuis un Compte est <strong>présumée réalisée par son
    titulaire</strong> et lui est pleinement imputable, sauf preuve contraire d'une
    compromission signalée sans délai conformément à l'alinéa ci-dessus.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 5 — RÔLES ET PERMISSIONS
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 5 — Rôles et permissions</h2>

<p>
    La Plateforme est organisée selon un modèle de contrôle d'accès basé sur les rôles
    (<em>Role-Based Access Control</em>). Chaque Utilisateur se voit attribuer un rôle
    lors de la validation de son compte, déterminant les fonctionnalités auxquelles il
    a accès.
</p>

<table>
    <thead>
        <tr>
            <th style="width:22%">Rôle</th>
            <th>Périmètre</th>
            <th>Permissions principales</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Public</strong><br><em>(non authentifié)</em></td>
            <td>Toutes communes — lecture seule</td>
            <td>
                <ul>
                    <li>Consultation du tableau de bord public et de la carte des
                        infrastructures ;</li>
                    <li>Accès aux statistiques agrégées publiées ;</li>
                    <li>Téléchargement des rapports publics mis à disposition.</li>
                </ul>
                <em>Aucune modification, saisie ou exportation avancée.</em>
            </td>
        </tr>
        <tr>
            <td><strong>Agent collecteur</strong></td>
            <td>Commune d'affectation uniquement</td>
            <td>
                <ul>
                    <li>Saisie et mise à jour des fiches d'infrastructure de sa commune
                        (création, modification, suppression logique) ;</li>
                    <li>Ajout de photos et de coordonnées GPS ;</li>
                    <li>Suivi de l'état et des indicateurs des infrastructures ;</li>
                    <li>Consultation des données de sa commune ;</li>
                    <li>Exportation des données de sa commune (Excel, PDF).</li>
                </ul>
                <em>Accès restreint à sa commune. Aucun accès aux données des
                autres communes ni aux fonctions d'administration.</em>
            </td>
        </tr>
        <tr>
            <td><strong>Administrateur communal</strong></td>
            <td>Commune désignée</td>
            <td>
                <ul>
                    <li>Toutes les permissions de l'Agent collecteur pour sa commune ;</li>
                    <li>Validation et gestion des comptes des Agents collecteurs
                        de sa commune ;</li>
                    <li>Validation, correction et supervision des données saisies par
                        les agents de sa commune ;</li>
                    <li>Génération et exportation des rapports communaux ;</li>
                    <li>Consultation des journaux d'activité de sa commune.</li>
                </ul>
                <em>Aucun accès aux données des autres communes ni aux
                paramètres système globaux.</em>
            </td>
        </tr>
        <tr>
            <td><strong>Super-administrateur</strong></td>
            <td>Toutes communes — accès complet</td>
            <td>
                <ul>
                    <li>Gestion complète des comptes Utilisateurs (création, modification,
                        activation, suspension, suppression) ;</li>
                    <li>Accès en lecture et en écriture sur toutes les communes ;</li>
                    <li>Configuration des paramètres système et des référentiels ;</li>
                    <li>Accès aux journaux d'audit complets (<em>logs</em>) ;</li>
                    <li>Génération de rapports globaux multi-communes ;</li>
                    <li>Gestion des demandes d'exercice des droits (données
                        personnelles) ;</li>
                    <li>Supervision de la sécurité de la Plateforme.</li>
                </ul>
            </td>
        </tr>
    </tbody>
</table>

<p>
    L'ADECOB se réserve le droit de modifier les permissions associées à chaque rôle
    à tout moment, notamment pour des raisons de sécurité ou d'évolution fonctionnelle.
    Les Utilisateurs concernés en seront informés dans les meilleurs délais.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 6 — OBLIGATIONS DE L'UTILISATEUR
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 6 — Obligations de l'Utilisateur</h2>

<h3>6.1 Usage conforme et loyal</h3>
<p>
    L'Utilisateur s'engage à utiliser la Plateforme <strong>exclusivement aux fins
    prévues</strong> par les présentes CGU, de manière loyale et en conformité avec
    les lois et règlements en vigueur en République du Bénin.
</p>

<h3>6.2 Comportements interdits</h3>
<p>Il est expressément interdit à tout Utilisateur de :</p>
<ul>
    <li><strong>Accès non autorisé :</strong> tenter d'accéder à des espaces, modules,
        données ou fonctionnalités de la Plateforme qui ne lui sont pas attribués par
        son rôle, par quelque moyen que ce soit (manipulation d'URL, exploitation
        de failles, élévation de privilèges, etc.) ;</li>
    <li><strong>Atteinte à l'intégrité du système :</strong> introduire, propager ou
        tenter d'introduire tout code malveillant, virus, cheval de Troie, logiciel
        espion, robot (<em>bot</em>) ou tout autre programme susceptible de nuire au
        fonctionnement de la Plateforme ou à l'intégrité des données ;</li>
    <li><strong>Tests d'intrusion non autorisés :</strong> effectuer tout test de
        pénétration, audit de sécurité offensif, fuzzing ou toute autre action
        susceptible de perturber la disponibilité ou la sécurité de la Plateforme,
        sans <strong>autorisation écrite préalable et explicite</strong> de l'ADECOB
        (voir article 13 pour la procédure de divulgation responsable) ;</li>
    <li><strong>Extraction massive de données (<em>scraping</em>) :</strong> procéder
        à toute collecte automatisée, extraction en masse ou aspiration systématique
        des données de la Plateforme par des robots, scripts ou tout autre moyen
        automatique, en dehors des exports prévus à l'article 12 ;</li>
    <li><strong>Saisie de données fausses ou fictives :</strong> entrer sciemment
        des données inexactes, inventées, ou volontairement erronées relatives aux
        infrastructures, dans un but de manipulation, de sabotage ou pour tout autre
        motif malveillant ;</li>
    <li><strong>Partage de compte :</strong> communiquer ses identifiants à un tiers,
        permettre à une autre personne d'utiliser son Compte, ou utiliser le Compte
        d'un autre Utilisateur ;</li>
    <li><strong>Usurpation d'identité :</strong> se faire passer pour un autre
        Utilisateur, un agent de l'ADECOB, un représentant d'une commune ou tout autre
        tiers, que ce soit lors de l'inscription ou dans l'utilisation de la
        Plateforme ;</li>
    <li><strong>Contournement des mesures de sécurité :</strong> tenter de désactiver,
        contourner ou compromettre tout mécanisme de sécurité de la Plateforme
        (authentification, chiffrement, contrôle d'accès, journalisation, etc.) ;</li>
    <li><strong>Déni de service :</strong> soumettre des requêtes en volume anormalement
        élevé susceptibles de dégrader les performances ou la disponibilité de la
        Plateforme ;</li>
    <li><strong>Utilisation contraire à l'ordre public :</strong> utiliser la Plateforme
        à des fins illicites, diffamatoires, discriminatoires ou portant atteinte
        à la dignité des personnes ou aux intérêts de l'État béninois.</li>
</ul>

<h3>6.3 Sanctions pénales applicables</h3>
<p>
    Conformément aux <strong>articles 549 et suivants de la loi n°2017-20 du 20 avril 2018
    portant Code du numérique en République du Bénin</strong>, toute atteinte portée à un
    système d'information, à l'intégrité, la confidentialité ou la disponibilité des données
    informatiques est constitutive d'une infraction pénale passible de poursuites judiciaires,
    de peines d'emprisonnement et d'amendes. L'ADECOB se réserve le droit de porter plainte
    contre tout Utilisateur contrevenant à ces dispositions.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 7 — OBLIGATIONS DE L'ADECOB
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 7 — Obligations de l'ADECOB</h2>

<p>Dans le cadre de l'exploitation de la Plateforme, l'ADECOB s'engage à :</p>
<ul>
    <li><strong>Maintenir la disponibilité du service</strong> dans les conditions
        définies à l'article 10, en mettant en œuvre les moyens techniques et humains
        raisonnables à cet effet ;</li>
    <li><strong>Informer préalablement</strong> les Utilisateurs authentifiés de toute
        maintenance planifiée susceptible d'entraîner une interruption de service, dans
        un délai de préavis raisonnable (sauf urgence ou incident de sécurité imprévu),
        via une notification sur la Plateforme ou par email ;</li>
    <li><strong>Protéger les données</strong> traitées sur la Plateforme par des mesures
        techniques et organisationnelles appropriées, conformément à la PSSI publiée à
        l'adresse <a href="{{ route('legal.pssi') }}">Politique de sécurité (PSSI)</a> ;</li>
    <li><strong>Traiter les demandes d'exercice des droits</strong> des personnes
        concernées (accès, rectification, suppression, limitation) dans un délai maximum
        d'<strong>un (1) mois</strong> à compter de la réception d'une demande complète,
        conformément aux dispositions de la
        <a href="{{ route('legal.confidentialite') }}">Politique de confidentialité</a> ;</li>
    <li><strong>Notifier les incidents de sécurité</strong> majeurs aux Utilisateurs
        concernés et aux autorités compétentes dans les délais prévus par la
        réglementation béninoise applicable ;</li>
    <li><strong>Tenir à jour le registre des traitements</strong>, consultable à
        l'adresse <a href="{{ route('legal.registre') }}">Registre des traitements</a> ;</li>
    <li><strong>Répondre aux signalements de vulnérabilités</strong> dans les conditions
        prévues à l'article 13 des présentes CGU.</li>
</ul>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 8 — PROPRIÉTÉ INTELLECTUELLE
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 8 — Propriété intellectuelle</h2>

<h3>8.1 Droits de l'ADECOB</h3>
<p>
    L'ensemble des éléments constitutifs de la Plateforme — notamment le code source
    applicatif, l'architecture logicielle, la charte graphique, le logo, les pictogrammes,
    les maquettes, les textes, la documentation et les algorithmes de traitement —
    est la <strong>propriété exclusive de l'ADECOB</strong> ou fait l'objet d'une licence
    d'utilisation accordée à l'ADECOB par ses partenaires.
</p>
<p>
    Toute reproduction, représentation, adaptation, traduction, diffusion, commercialisation
    ou extraction, totale ou partielle, par quelque procédé et sur quelque support que ce
    soit, sans l'<strong>autorisation écrite préalable de l'ADECOB</strong>, est strictement
    interdite et constitue une contrefaçon susceptible d'engager la responsabilité civile
    et pénale de son auteur.
</p>

<h3>8.2 Propriété des données métier</h3>
<p>
    Les <strong>Données métier</strong> saisies dans la Plateforme (fiches d'infrastructures,
    coordonnées GPS, indicateurs, photos, etc.) <strong>restent la propriété des communes
    concernées</strong>. L'ADECOB en assure l'hébergement, la conservation et la valorisation
    dans le cadre exclusif de sa mission d'intérêt général, sans en revendiquer aucune
    propriété commerciale.
</p>
<p>
    L'ADECOB dispose d'une <strong>licence non exclusive, gratuite et irrévocable</strong>
    sur ces données aux seules fins de l'exploitation de la Plateforme, de la production
    de rapports et de statistiques, et de la mise à disposition au public, dans les limites
    prévues par les présentes CGU et la politique de confidentialité.
</p>

<h3>8.3 Contenus des Utilisateurs</h3>
<p>
    En saisissant des données dans la Plateforme, l'Utilisateur garantit qu'il dispose
    de l'ensemble des droits nécessaires sur ces contenus (notamment les photographies)
    et accorde à l'ADECOB le droit de les utiliser dans le cadre de la Plateforme.
    L'Utilisateur est seul responsable de l'exactitude et de la légalité des contenus
    qu'il saisit.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 9 — DONNÉES PERSONNELLES
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 9 — Données personnelles</h2>

<p>
    La Plateforme traite des données à caractère personnel dans le cadre de la gestion
    des comptes Utilisateurs et du suivi des activités de saisie. L'ADECOB agit en qualité
    de <strong>responsable de traitement</strong> au sens de la réglementation applicable.
</p>
<p>
    L'ensemble des informations relatives à la collecte, aux finalités, aux droits des
    personnes concernées et aux durées de conservation est détaillé dans la
    <a href="{{ route('legal.confidentialite') }}"><strong>Politique de confidentialité</strong></a>
    de la Plateforme, qui fait partie intégrante des présentes CGU.
</p>
<p>
    Tout Utilisateur dispose du droit d'accéder à ses données personnelles, de les rectifier,
    de demander leur effacement ou la limitation de leur traitement, en adressant une demande
    via le <a href="{{ route('contact.form') }}">formulaire de contact</a>. Un recours peut
    également être formé auprès de l'<strong>Autorité de Protection des Données Personnelles
    (APDP)</strong> du Bénin.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 10 — DISPONIBILITÉ ET QUALITÉ DE SERVICE
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 10 — Disponibilité et qualité de service</h2>

<h3>10.1 Engagement de disponibilité</h3>
<p>
    L'ADECOB s'efforce d'assurer la disponibilité de la Plateforme <strong>24 heures sur 24,
    7 jours sur 7</strong>, dans la limite des moyens techniques dont elle dispose (<em>best
    effort</em>). Cet engagement ne constitue pas une obligation de résultat.
</p>

<h3>10.2 Maintenances planifiées</h3>
<p>
    Des interruptions temporaires de service peuvent être programmées pour les besoins de
    maintenance, de mise à jour ou d'amélioration de la Plateforme. L'ADECOB s'engage à
    en informer les Utilisateurs authentifiés <strong>au moins 48 heures à l'avance</strong>,
    sauf en cas d'urgence technique ou de menace de sécurité nécessitant une intervention
    immédiate.
</p>

<h3>10.3 Incidents non planifiés et force majeure</h3>
<p>
    L'ADECOB ne saurait être tenue responsable des interruptions ou dégradations du
    service résultant de :
</p>
<ul>
    <li>pannes ou défaillances des réseaux de télécommunications tiers ;</li>
    <li>défaillances de l'hébergeur ou des infrastructures cloud utilisées ;</li>
    <li>coupures d'électricité ;</li>
    <li>événements de force majeure au sens du droit béninois (catastrophes naturelles,
        troubles à l'ordre public, pandémies, etc.) ;</li>
    <li>attaques informatiques externes (déni de service distribué, etc.) malgré
        les mesures de protection mises en place.</li>
</ul>
<p>
    En cas d'incident majeur, l'ADECOB communiquera dans les meilleurs délais sur
    l'état du service et les mesures prises pour rétablir la disponibilité.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 11 — QUALITÉ DES DONNÉES
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 11 — Qualité des données</h2>

<h3>11.1 Responsabilité des Utilisateurs sur les données saisies</h3>
<p>
    Chaque Utilisateur est <strong>personnellement responsable de l'exactitude, de
    l'exhaustivité et de la mise à jour</strong> des données qu'il saisit dans la
    Plateforme. Il doit s'assurer que les informations renseignées correspondent à la
    réalité de terrain et sont conformes aux référentiels et nomenclatures définis par
    l'ADECOB.
</p>

<h3>11.2 Contrôle et correction par l'ADECOB</h3>
<p>
    L'ADECOB et les Administrateurs communaux se réservent le droit de :
</p>
<ul>
    <li>corriger les <strong>erreurs manifestes</strong> (fautes de saisie évidentes,
        coordonnées GPS aberrantes, valeurs incohérentes) sans notification préalable
        de l'Utilisateur ;</li>
    <li>alerter l'Utilisateur concerné en cas de détection d'anomalies nécessitant
        une vérification de terrain ;</li>
    <li>suspendre la visibilité d'une fiche d'infrastructure en attente de
        vérification.</li>
</ul>

<h3>11.3 Données fausses — sanction</h3>
<p>
    La saisie <strong>délibérée</strong> de données fausses, fictives ou volontairement
    erronées constitue une violation grave des présentes CGU et est susceptible d'entraîner
    la <strong>suspension immédiate du compte</strong> de l'Utilisateur concerné,
    sans préjudice des poursuites judiciaires qui pourraient en découler, notamment
    en vertu du Code du numérique béninois.
</p>

<h3>11.4 Absence de garantie sur les données publiques</h3>
<p>
    L'ADECOB met en œuvre les contrôles raisonnables pour assurer la qualité des données
    publiées, mais ne garantit pas l'exactitude, l'exhaustivité ou la mise à jour en temps
    réel de l'ensemble des données disponibles sur la Plateforme. Toute décision fondée
    sur les données publiées relève de la seule responsabilité de l'utilisateur de ces données.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 12 — EXPORTATION ET UTILISATION DES DONNÉES
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 12 — Exportation et utilisation des données</h2>

<h3>12.1 Fonctionnalités d'export autorisées</h3>
<p>
    Les Utilisateurs authentifiés disposant des permissions appropriées peuvent exporter
    les données dans les formats proposés par la Plateforme
    (<strong>Excel (.xlsx), PDF</strong> et, selon les modules, CSV), dans la limite
    des données auxquelles leur rôle leur donne accès.
</p>

<h3>12.2 Usages autorisés des données exportées</h3>
<p>
    Les données exportées ne peuvent être utilisées qu'à des fins :
</p>
<ul>
    <li>de rapportage interne aux communes ou à l'ADECOB ;</li>
    <li>d'aide à la décision et de planification des investissements communaux ;</li>
    <li>de production de documents officiels dans le cadre des missions des communes
        et de l'ADECOB ;</li>
    <li>de recherche et d'études académiques, sous réserve de citer la source
        (« Plateforme ADECOB de Gestion des Infrastructures Communales ») ;</li>
    <li>de communication institutionnelle des communes et partenaires autorisés.</li>
</ul>

<h3>12.3 Usages interdits</h3>
<p>Il est expressément interdit d'utiliser les données exportées pour :</p>
<ul>
    <li>toute finalité commerciale sans autorisation écrite préalable de l'ADECOB ;</li>
    <li>la constitution de bases de données concurrentes ou substituables à la
        Plateforme ;</li>
    <li>la revente, la cession ou la mise à disposition à titre onéreux à des tiers ;</li>
    <li>toute publication décontextualisée susceptible de nuire à l'image des
        communes, de l'ADECOB ou de ses partenaires.</li>
</ul>

<h3>12.4 Extraction automatisée — interdiction</h3>
<p>
    Toute <strong>extraction automatisée et massive</strong> des données de la Plateforme
    par des scripts, robots, outils de <em>scraping</em> ou tout autre procédé non prévu
    par les fonctionnalités officielles de la Plateforme est <strong>strictement interdite</strong>,
    que l'Utilisateur soit authentifié ou non. Cette interdiction s'applique indépendamment
    de l'usage final envisagé. Des dispositions techniques limitant le débit des requêtes
    (<em>rate limiting</em>) sont mises en place pour détecter et bloquer ces comportements.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 13 — SIGNALEMENT DE VULNÉRABILITÉS (DIVULGATION RESPONSABLE)
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 13 — Signalement de vulnérabilités</h2>

<h3>13.1 Politique de divulgation responsable</h3>
<p>
    L'ADECOB encourage toute personne identifiant une vulnérabilité de sécurité sur la
    Plateforme à la signaler de manière <strong>responsable et confidentielle</strong>,
    selon la procédure décrite au présent article, plutôt que de l'exploiter ou de la
    divulguer publiquement.
</p>

<h3>13.2 Procédure de signalement</h3>
<p>
    Toute vulnérabilité présumée doit être signalée <strong>exclusivement</strong>
    via le <a href="{{ route('contact.form') }}">formulaire de contact</a> de la Plateforme,
    en précisant dans l'objet du message : <code>[SECURITE - DIVULGATION RESPONSABLE]</code>.
    Le signalement doit inclure :
</p>
<ul>
    <li>une description claire et précise de la vulnérabilité ;</li>
    <li>les étapes permettant de la reproduire (<em>proof of concept</em>) ;</li>
    <li>l'impact potentiel estimé sur la confidentialité, l'intégrité ou la
        disponibilité des données ;</li>
    <li>les coordonnées du déclarant pour permettre le suivi.</li>
</ul>

<h3>13.3 Engagements de l'ADECOB</h3>
<p>En réponse à un signalement conforme à la procédure définie ci-dessus, l'ADECOB
s'engage à :</p>
<ul>
    <li>accuser réception du signalement dans un délai de <strong>5 jours ouvrés</strong>
        à compter de sa réception ;</li>
    <li>analyser la vulnérabilité et communiquer une première évaluation au déclarant ;</li>
    <li>traiter la correction dans les meilleurs délais, selon la criticité ;</li>
    <li>informer le déclarant de la résolution de la vulnérabilité ;</li>
    <li>ne pas engager de poursuites judiciaires contre le déclarant ayant agi de bonne
        foi, dans le strict respect des limites du présent article.</li>
</ul>

<h3>13.4 Limites de la divulgation responsable</h3>
<p>
    La présente politique de divulgation responsable ne constitue <strong>pas une
    autorisation</strong> de procéder à des tests d'intrusion, des attaques ou toute
    action susceptible de compromettre la disponibilité, l'intégrité ou la confidentialité
    de la Plateforme ou des données qu'elle contient. Tout acte allant au-delà de la
    simple identification et notification d'une vulnérabilité existante reste susceptible
    de sanctions pénales.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 14 — SUSPENSION ET RÉSILIATION DU COMPTE
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 14 — Suspension et résiliation du compte</h2>

<h3>14.1 Motifs de suspension ou de résiliation par l'ADECOB</h3>
<p>L'ADECOB peut <strong>suspendre ou résilier</strong> un Compte, sans préavis ni
indemnité, dans les cas suivants :</p>
<ul>
    <li>violation des présentes CGU, notamment des interdictions listées à
        l'article 6 ;</li>
    <li>saisie délibérée de données fausses ou fictives ;</li>
    <li>usurpation d'identité ou fourniture d'informations mensongères à
        l'inscription ;</li>
    <li>utilisation du Compte à des fins non conformes à la mission de la
        Plateforme ;</li>
    <li>inactivité du compte pendant une durée supérieure à <strong>deux (2) ans</strong>
        consécutifs, après notification préalable de l'Utilisateur ;</li>
    <li>décision judiciaire ou injonction d'une autorité compétente ;</li>
    <li>cessation des activités de l'ADECOB ou arrêt définitif de la Plateforme.</li>
</ul>

<h3>14.2 Suspension préventive</h3>
<p>
    En cas de suspicion sérieuse de violation des CGU ou d'atteinte à la sécurité de
    la Plateforme, l'ADECOB peut procéder à une <strong>suspension immédiate et
    conservatoire</strong> du Compte dans l'attente des résultats d'une investigation.
    L'Utilisateur en est informé dans les meilleurs délais, sauf si cette notification
    est susceptible de compromettre l'investigation.
</p>

<h3>14.3 Résiliation à l'initiative de l'Utilisateur</h3>
<p>
    Tout Utilisateur peut demander la clôture de son Compte à tout moment, sans avoir
    à en justifier les motifs, en adressant une demande via le
    <a href="{{ route('contact.form') }}">formulaire de contact</a>. La clôture prend effet
    dans un délai de <strong>30 jours ouvrés</strong> à compter de la réception de la demande,
    délai pendant lequel l'Utilisateur peut se rétracter.
</p>

<h3>14.4 Sort des données après clôture</h3>
<p>
    Lors de la clôture d'un Compte :
</p>
<ul>
    <li>les <strong>données personnelles</strong> liées au Compte (nom, prénom, email,
        téléphone) sont supprimées ou anonymisées conformément à la politique de
        confidentialité ;</li>
    <li>les <strong>données métier</strong> (fiches d'infrastructures, saisies) saisies
        par l'Utilisateur sont <strong>conservées</strong> dans la Plateforme, car elles
        constituent des données communales ne lui appartenant pas en propre ; elles sont
        désattribuées du Compte clôturé ;</li>
    <li>les <strong>journaux d'audit</strong> relatifs aux actions de l'Utilisateur
        sont conservés pour la durée prévue dans le registre des traitements, à des
        fins de traçabilité et de sécurité.</li>
</ul>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 15 — LIMITATION DE RESPONSABILITÉ
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 15 — Limitation de responsabilité</h2>

<p>Dans les limites autorisées par le droit de la République du Bénin,
l'ADECOB ne saurait être tenue responsable :</p>
<ul>
    <li>des <strong>dommages indirects</strong>, immatériels, consécutifs ou accessoires
        résultant de l'utilisation ou de l'impossibilité d'utiliser la Plateforme,
        notamment toute perte d'exploitation, perte de données, atteinte à la réputation
        ou manque à gagner ;</li>
    <li>des <strong>défaillances des réseaux de télécommunications tiers</strong>
        (opérateurs Internet, réseau mobile, etc.) affectant l'accès à la Plateforme ;</li>
    <li>des <strong>usages non conformes</strong> de la Plateforme ou des données
        exportées par les Utilisateurs, en violation des présentes CGU ;</li>
    <li>des <strong>dommages causés par des tiers</strong> (attaques informatiques
        externes, intrusions, actes malveillants) malgré les mesures de sécurité
        mises en place ;</li>
    <li>de l'<strong>inexactitude ou de l'obsolescence</strong> des données saisies par
        les Utilisateurs, l'ADECOB n'étant pas en mesure de vérifier en temps réel
        l'ensemble des données de terrain ;</li>
    <li>des <strong>décisions prises</strong> par des tiers sur la base des données
        consultées ou exportées depuis la Plateforme.</li>
</ul>
<p>
    La responsabilité de l'ADECOB ne peut en aucun cas excéder le montant des sommes
    effectivement versées par l'Utilisateur à l'ADECOB dans les douze (12) mois précédant
    le fait générateur du dommage. Compte tenu de la gratuité de la Plateforme, cette
    limitation s'entend comme une <strong>exclusion totale de la responsabilité
    financière</strong> de l'ADECOB envers les Utilisateurs du domaine public.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 16 — MODIFICATION DES CGU
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 16 — Modification des Conditions Générales d'Utilisation</h2>

<h3>16.1 Droit de modification</h3>
<p>
    L'ADECOB se réserve le droit de modifier les présentes CGU à tout moment, notamment
    pour tenir compte de l'évolution de la Plateforme, des réglementations applicables,
    ou des recommandations des autorités compétentes.
</p>

<h3>16.2 Information des Utilisateurs</h3>
<p>
    En cas de modification <strong>substantielle</strong> des CGU (modification des droits
    ou obligations des Utilisateurs, changement de finalité de traitement, etc.), les
    Utilisateurs authentifiés sont informés :
</p>
<ul>
    <li>par notification dans l'interface de la Plateforme lors de leur prochaine
        connexion ;</li>
    <li>et/ou par email à l'adresse enregistrée dans leur Compte.</li>
</ul>
<p>
    La notification est effectuée dans un délai minimum de <strong>30 jours avant
    l'entrée en vigueur</strong> de la nouvelle version des CGU, sauf en cas de
    modification imposée par une réglementation applicable à effet immédiat ou en cas
    d'urgence de sécurité.
</p>

<h3>16.3 Refus des nouvelles CGU</h3>
<p>
    L'Utilisateur qui n'accepte pas les nouvelles CGU dispose du droit de demander la
    clôture de son Compte selon la procédure décrite à l'article 14.3, avant la date
    d'entrée en vigueur de la version modifiée. La poursuite de l'utilisation de la
    Plateforme après cette date vaut <strong>acceptation sans réserve</strong> de la
    nouvelle version des CGU.
</p>

<h3>16.4 Historique des versions</h3>
<p>
    La date et le numéro de version des CGU en vigueur sont indiqués en en-tête de ce
    document. Les versions antérieures des CGU peuvent être communiquées sur demande
    adressée via le <a href="{{ route('contact.form') }}">formulaire de contact</a>.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 17 — LOI APPLICABLE ET JURIDICTION COMPÉTENTE
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 17 — Loi applicable et juridiction compétente</h2>

<h3>17.1 Loi applicable</h3>
<p>
    Les présentes CGU sont régies, interprétées et exécutées conformément au
    <strong>droit de la République du Bénin</strong>, notamment :
</p>
<ul>
    <li>la <strong>loi n°2017-20 du 20 avril 2018</strong> portant Code du numérique
        en République du Bénin ;</li>
    <li>la législation relative à la protection des données à caractère personnel
        applicable au Bénin ;</li>
    <li>le Code civil béninois et les principes généraux du droit des obligations ;</li>
    <li>toute autre réglementation nationale applicable en raison de l'objet
        de la Plateforme.</li>
</ul>

<h3>17.2 Résolution amiable</h3>
<p>
    En cas de litige relatif à l'interprétation ou à l'exécution des présentes CGU,
    l'Utilisateur est invité à adresser en premier lieu une réclamation à l'ADECOB via
    le <a href="{{ route('contact.form') }}">formulaire de contact</a>. L'ADECOB s'engage
    à examiner toute réclamation et à apporter une réponse dans un délai de
    <strong>30 jours ouvrés</strong>. Les parties s'efforceront de bonne foi de
    trouver une <strong>résolution amiable</strong> avant tout recours judiciaire.
</p>

<h3>17.3 Juridiction compétente</h3>
<p>
    À défaut de résolution amiable dans le délai prévu, tout litige relatif à la
    validité, l'interprétation ou l'exécution des présentes CGU est soumis à la
    <strong>compétence exclusive des juridictions béninoises</strong> compétentes.
</p>


{{-- ══════════════════════════════════════════════════════════════════════════
     ARTICLE 18 — CONTACT ET MÉDIATION
═══════════════════════════════════════════════════════════════════════════ --}}

<h2>Article 18 — Contact et médiation</h2>

<h3>18.1 Contact général</h3>
<p>
    Pour toute question relative aux présentes CGU, à l'utilisation de la Plateforme
    ou à l'exercice de droits, l'Utilisateur peut contacter l'ADECOB via le
    <a href="{{ route('contact.form') }}"><strong>formulaire de contact</strong></a>
    disponible sur la Plateforme.
</p>
<p>
    <strong>ADECOB — Association pour le Développement des Communes du Borgou</strong><br>
    Siège social : Parakou, Département du Borgou, République du Bénin
</p>

<h3>18.2 Demandes relatives aux données personnelles</h3>
<p>
    Pour toute demande d'exercice de droits sur les données personnelles (accès,
    rectification, effacement, opposition, portabilité), l'Utilisateur peut :
</p>
<ol>
    <li>adresser sa demande à l'ADECOB via le
        <a href="{{ route('contact.form') }}">formulaire de contact</a>, en précisant
        dans l'objet : <code>[DONNÉES PERSONNELLES - EXERCICE DE DROITS]</code> ;</li>
    <li>en cas d'absence de réponse satisfaisante dans le délai d'un mois, former
        un recours auprès de l'<strong>Autorité de Protection des Données Personnelles
        (APDP) de la République du Bénin</strong>.</li>
</ol>
<p>
    Les modalités détaillées d'exercice des droits sont décrites dans la
    <a href="{{ route('legal.confidentialite') }}">Politique de confidentialité</a>.
</p>

<h3>18.3 Documents complémentaires</h3>
<p>Les présentes CGU doivent être lues conjointement avec les documents suivants,
accessibles sur la Plateforme :</p>
<ul>
    <li><a href="{{ route('legal.confidentialite') }}">Politique de confidentialité</a>
        — traitements de données personnelles, droits des personnes ;</li>
    <li><a href="{{ route('legal.pssi') }}">Politique de Sécurité des Systèmes
        d'Information (PSSI)</a> — mesures de sécurité techniques et organisationnelles ;</li>
    <li><a href="{{ route('legal.registre') }}">Registre des traitements</a>
        — liste des traitements de données opérés par l'ADECOB.</li>
</ul>

@endsection
