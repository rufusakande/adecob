@extends('legal._layout')

@section('title', 'PSSI — Politique de Sécurité du Système d\'Information')
@section('doc_title', 'Politique de Sécurité du Système d\'Information (PSSI)')
@section('doc_version', '1.1')
@section('doc_date', '30/06/2026')

@section('doc_content')

{{-- ══════════════════════════════════════════════════════════════════
     ALERTE RÉSUMÉ EXÉCUTIF
     ══════════════════════════════════════════════════════════════════ --}}
<div class="alert alert-light border">
    <strong>Objet.</strong> La présente Politique de Sécurité du Système d'Information (PSSI)
    définit l'ensemble des règles, responsabilités, mesures techniques et organisationnelles
    mises en œuvre par l'<strong>ADECOB</strong> (Association pour le Développement des Communes
    du Borgou) pour garantir la <strong>confidentialité, l'intégrité, la disponibilité et la
    traçabilité</strong> des données traitées par la plateforme numérique « ADECOB Infrastructure
    Plannification ». Elle est opposable à toute personne accédant au système d'information,
    quel que soit son rôle ou son statut.
</div>

<p>
    Ce document est élaboré en conformité avec la <em>loi n°2017-20 du 20 avril 2018 portant
    Code du numérique en République du Bénin</em>, le référentiel international
    <em>ISO/IEC 27001:2022</em>, les recommandations <em>OWASP Top 10 2021</em> et le
    cadre de gouvernance <em>COBIT 2019</em>. Il complète la
    <a href="{{ route('legal.confidentialite') }}">Politique de confidentialité</a>,
    les <a href="{{ route('legal.cgu') }}">Conditions Générales d'Utilisation</a>
    et le <a href="{{ route('legal.registre') }}">Registre des traitements</a>.
</p>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 1 — PRÉAMBULE ET OBJET
     ══════════════════════════════════════════════════════════════════ --}}
<h2>1. Préambule et objet</h2>

<h3>1.1 Contexte et enjeux</h3>
<p>
    L'ADECOB gère, pour le compte des huit (8) communes du département du Borgou au Bénin
    et avec le soutien financier de la <strong>DDC Suisse</strong> (Direction du Développement
    et de la Coopération), une plateforme numérique centralisant
    <strong>10 402 infrastructures communautaires géo-référencées</strong> : écoles, points
    d'eau, latrines, marchés et centres de santé. La criticité de ces données — indispensables
    à la planification du développement local et aux décisions d'investissement — impose un
    niveau de sécurité rigoureux.
</p>
<p>
    La plateforme est une application web développée en <strong>Laravel 10</strong>, adossée
    à un SGBD <strong>MariaDB</strong>, hébergée en ligne et accessible depuis l'ensemble du
    territoire national et des partenaires internationaux. Elle traite des données à caractère
    personnel (identités, coordonnées des agents et administrateurs) ainsi que des données
    sensibles relatives aux plans de développement communaux.
</p>

<h3>1.2 Fondements juridiques</h3>
<p>La PSSI repose notamment sur les textes et standards suivants :</p>
<ul>
    <li><strong>Loi n°2017-20 du 20 avril 2018</strong> portant Code du numérique en République
        du Bénin — obligations de sécurité, notification des violations de données
        (art. 391), sanctions pénales (art. 549 et suivants) ;</li>
    <li><strong>ISO/IEC 27001:2022</strong> — Système de Management de la Sécurité de
        l'Information (SMSI) ;</li>
    <li><strong>OWASP Top 10 2021</strong> — classification des risques applicatifs les plus
        critiques et contre-mesures associées ;</li>
    <li><strong>COBIT 2019</strong> — cadre de gouvernance et de gestion du système
        d'information ;</li>
    <li>Politiques internes de l'ADECOB et exigences contractuelles de la DDC Suisse.</li>
</ul>

<h3>1.3 Objectifs de la PSSI</h3>
<p>La PSSI vise à :</p>
<ol>
    <li>Définir les règles de sécurité applicables à l'ensemble du périmètre ;</li>
    <li>Attribuer clairement les responsabilités en matière de sécurité ;</li>
    <li>Réduire les risques d'intrusion, de fuite de données, d'indisponibilité et de
        corruption d'informations ;</li>
    <li>Assurer la conformité légale et réglementaire ;</li>
    <li>Garantir la continuité de service et la résilience du système ;</li>
    <li>Fournir un cadre de réponse aux incidents de sécurité.</li>
</ol>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 2 — PÉRIMÈTRE D'APPLICATION
     ══════════════════════════════════════════════════════════════════ --}}
<h2>2. Périmètre d'application</h2>

<h3>2.1 Systèmes et composants couverts</h3>
<p>La PSSI s'applique à l'intégralité du système d'information de la plateforme ADECOB, qui comprend :</p>
<table>
    <tr>
        <th>Composant</th>
        <th>Description</th>
        <th>Criticité</th>
    </tr>
    <tr>
        <td>Application web Laravel 10</td>
        <td>Interface utilisateur, logique métier, API internes</td>
        <td><strong>Critique</strong></td>
    </tr>
    <tr>
        <td>Base de données MariaDB</td>
        <td>Stockage de l'ensemble des données (infrastructures, utilisateurs, audits)</td>
        <td><strong>Critique</strong></td>
    </tr>
    <tr>
        <td>Infrastructure d'hébergement</td>
        <td>Serveur(s) web, reverse-proxy, certificats TLS</td>
        <td><strong>Critique</strong></td>
    </tr>
    <tr>
        <td>Système de messagerie transactionnelle</td>
        <td>Envoi des codes MFA, notifications, réinitialisations</td>
        <td>Élevée</td>
    </tr>
    <tr>
        <td>Sauvegardes</td>
        <td>Exports automatisés de la base de données, stockage distant</td>
        <td>Élevée</td>
    </tr>
    <tr>
        <td>Journaux d'audit (<code>audit_logs</code>)</td>
        <td>Traçabilité de toutes les actions sensibles</td>
        <td>Élevée</td>
    </tr>
    <tr>
        <td>Postes de travail des administrateurs</td>
        <td>Terminaux utilisés pour administrer la plateforme</td>
        <td>Modérée</td>
    </tr>
</table>

<h3>2.2 Utilisateurs et parties prenantes couverts</h3>
<p>La PSSI s'impose à :</p>
<ul>
    <li><strong>L'ensemble des utilisateurs authentifiés</strong> : super-administrateurs,
        administrateurs communaux, agents de mairie ;</li>
    <li><strong>Les visiteurs publics</strong> pour ce qui concerne les usages des espaces
        en accès libre ;</li>
    <li><strong>L'équipe ADECOB</strong> (direction, personnel technique, chargés de suivi) ;</li>
    <li><strong>Les prestataires et partenaires tiers</strong> : hébergeur, prestataires de
        maintenance, intégrateurs, auditeurs ;</li>
    <li><strong>La DDC Suisse</strong> et ses représentants dans le cadre de leurs droits
        d'accès aux données agrégées.</li>
</ul>

<h3>2.3 Exclusions</h3>
<p>
    La PSSI ne couvre pas les systèmes d'information internes des communes elles-mêmes
    (mairies, directions techniques locales) sauf dans la mesure où ces systèmes se connectent
    directement à la plateforme ADECOB.
</p>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 3 — GOUVERNANCE DE LA SÉCURITÉ
     ══════════════════════════════════════════════════════════════════ --}}
<h2>3. Gouvernance de la sécurité</h2>

<h3>3.1 Structure de gouvernance</h3>
<p>
    L'ADECOB met en place une gouvernance de la sécurité articulée autour des acteurs
    suivants, conformément aux principes du cadre COBIT 2019 :
</p>
<table>
    <tr>
        <th>Acteur</th>
        <th>Rôle et responsabilités</th>
    </tr>
    <tr>
        <td><strong>Direction de l'ADECOB</strong></td>
        <td>
            Approbation de la PSSI et des ressources allouées à la sécurité ;
            arbitrage en cas d'incident critique ; engagement institutionnel.
        </td>
    </tr>
    <tr>
        <td><strong>Responsable de la Sécurité du SI (RSSI)</strong></td>
        <td>
            Pilotage et mise à jour de la PSSI ; supervision des mesures de sécurité ;
            coordination de la réponse aux incidents ; formation des utilisateurs ;
            interface avec les autorités (APDP) ; production du rapport annuel de sécurité.
        </td>
    </tr>
    <tr>
        <td><strong>Super-Administrateur technique</strong></td>
        <td>
            Application opérationnelle des mesures techniques ; gestion des comptes et
            des droits ; supervision des journaux ; déploiement des correctifs.
        </td>
    </tr>
    <tr>
        <td><strong>Administrateurs communaux</strong></td>
        <td>
            Respect et relais de la PSSI dans leur commune ; signalement des incidents
            locaux ; supervision des agents rattachés.
        </td>
    </tr>
    <tr>
        <td><strong>Tous les utilisateurs</strong></td>
        <td>
            Respect des règles définies dans la présente PSSI ; signalement immédiat
            de tout comportement suspect ou incident.
        </td>
    </tr>
</table>

<h3>3.2 Canal de contact sécurité</h3>
<p>
    Tout incident, vulnérabilité ou question relative à la sécurité doit être signalé
    via le <a href="{{ route('contact.form') }}">formulaire de contact officiel de l'ADECOB</a>
    en mentionnant explicitement « Sécurité — PSSI » dans l'objet du message.
    Ce canal est surveillé par le RSSI et le Super-Administrateur technique.
</p>

<h3>3.3 Cycles de révision et de pilotage</h3>
<ul>
    <li>La PSSI est <strong>revue formellement au moins une fois par an</strong>, en principe
        au cours du premier trimestre de chaque exercice ;</li>
    <li>Elle est également revue dans les <strong>30 jours suivant tout incident de sécurité
        majeur</strong> ou toute évolution significative du système d'information ;</li>
    <li>Chaque révision donne lieu à une montée de version et une mise à jour de la date de
        publication visible en en-tête du présent document ;</li>
    <li>Un <strong>rapport de sécurité annuel</strong> est produit par le RSSI à destination
        de la direction de l'ADECOB et des bailleurs (DDC Suisse), synthétisant les incidents
        survenus, les mesures prises et les axes d'amélioration.</li>
</ul>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 4 — POLITIQUE DES MOTS DE PASSE
     ══════════════════════════════════════════════════════════════════ --}}
<h2>4. Politique des mots de passe</h2>

<h3>4.1 Règles par profil utilisateur</h3>
<p>
    La plateforme impose les règles suivantes, vérifiées <em>côté serveur</em> lors de
    la création, la modification ou la réinitialisation du mot de passe :
</p>
<table>
    <tr>
        <th>Règle</th>
        <th>Super Admin / Admin Commune</th>
        <th>Agent de mairie</th>
    </tr>
    <tr>
        <td>Longueur minimale</td>
        <td><strong>12 caractères</strong></td>
        <td><strong>10 caractères</strong></td>
    </tr>
    <tr>
        <td>Majuscules</td>
        <td>Au moins 1 lettre majuscule</td>
        <td>Au moins 1 lettre majuscule</td>
    </tr>
    <tr>
        <td>Minuscules</td>
        <td>Au moins 1 lettre minuscule</td>
        <td>Au moins 1 lettre minuscule</td>
    </tr>
    <tr>
        <td>Chiffres</td>
        <td>Au moins 2 chiffres</td>
        <td>Au moins 1 chiffre</td>
    </tr>
    <tr>
        <td>Caractères spéciaux</td>
        <td>Au moins 1 parmi : <code>! @ # $ % ^ &amp; * ( ) _ + - = [ ] { } | ; ' : " , . &lt; &gt; ?</code></td>
        <td>Au moins 1 caractère spécial</td>
    </tr>
    <tr>
        <td>Renouvellement obligatoire</td>
        <td>Tous les <strong>6 mois</strong></td>
        <td>Recommandé tous les 12 mois</td>
    </tr>
    <tr>
        <td>Historique</td>
        <td>Les 5 derniers mots de passe interdits</td>
        <td>Les 3 derniers mots de passe interdits</td>
    </tr>
</table>

<h3>4.2 Mots de passe interdits</h3>
<p>Sont systématiquement rejetés les mots de passe :</p>
<ul>
    <li>Identiques ou dérivés du nom, prénom, nom d'utilisateur ou adresse e-mail ;</li>
    <li>Figurant dans les listes publiques de mots de passe compromis (bases de type
        <em>HaveIBeenPwned</em> ou équivalent) ;</li>
    <li>Constitués de séquences évidentes : <code>123456789</code>, <code>password</code>,
        <code>azerty</code>, <code>adecob2026</code>, etc. ;</li>
    <li>Identiques au code d'accès communal ou à tout autre identifiant de la plateforme ;</li>
    <li>Contenant le nom « ADECOB » ou « Borgou » seul ou combiné à un chiffre simple.</li>
</ul>

<h3>4.3 Stockage et hachage</h3>
<p>
    Aucun mot de passe n'est jamais stocké en clair. La plateforme utilise exclusivement
    l'algorithme <strong><code>bcrypt</code></strong> avec facteur de coût adapté (≥ 12),
    via la fonction native de Laravel, garantissant un sel aléatoire unique par empreinte.
    Les mots de passe ne sont pas affichés, loggés, ni transmis par e-mail à aucun moment.
</p>

<h3>4.4 Tentatives de connexion et verrouillage</h3>
<table>
    <tr>
        <th>Mécanisme</th>
        <th>Paramètre</th>
    </tr>
    <tr>
        <td>Rate-limit par e-mail + IP</td>
        <td>5 tentatives / minute → blocage temporaire 15 minutes</td>
    </tr>
    <tr>
        <td>Rate-limit global par IP</td>
        <td>20 tentatives / minute → blocage 1 heure</td>
    </tr>
    <tr>
        <td>Délai progressif (<em>backoff</em>)</td>
        <td>Délai croissant entre tentatives successives échouées</td>
    </tr>
    <tr>
        <td>Alerte RSSI</td>
        <td>Notification automatique au-delà de 10 échecs consécutifs sur un compte admin</td>
    </tr>
</table>

<h3>4.5 Réinitialisation du mot de passe</h3>
<p>Le processus de réinitialisation respecte les étapes suivantes :</p>
<ol>
    <li>L'utilisateur demande une réinitialisation depuis la page de connexion ;</li>
    <li>Un <strong>lien à usage unique</strong>, signé cryptographiquement et valable
        <strong>60 minutes</strong>, est envoyé à l'adresse e-mail associée au compte ;</li>
    <li>Le lien est invalidé après utilisation ou expiration ;</li>
    <li>En cas de suspicion d'incident, le RSSI peut invalider tous les liens en cours
        par rotation de la clé <code>APP_KEY</code> ;</li>
    <li>La réinitialisation génère automatiquement une entrée dans les journaux d'audit.</li>
</ol>

<h3>4.6 Sessions utilisateur</h3>
<table>
    <tr>
        <th>Paramètre</th>
        <th>Valeur</th>
    </tr>
    <tr>
        <td>Durée de session (agents)</td>
        <td>8 heures d'inactivité → déconnexion automatique</td>
    </tr>
    <tr>
        <td>Durée de session (admins)</td>
        <td>4 heures d'inactivité → déconnexion automatique</td>
    </tr>
    <tr>
        <td>Régénération de l'ID de session</td>
        <td>Systématique à chaque connexion réussie (anti <em>session fixation</em>)</td>
    </tr>
    <tr>
        <td>Sessions simultanées</td>
        <td>Limitées à 2 par compte ; toute nouvelle connexion invalide la plus ancienne</td>
    </tr>
</table>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 5 — GESTION DES ACCÈS ET DES IDENTITÉS
     ══════════════════════════════════════════════════════════════════ --}}
<h2>5. Gestion des accès et des identités</h2>

<h3>5.1 Modèle de rôles (RBAC)</h3>
<p>
    La plateforme implémente un contrôle d'accès basé sur les rôles (<em>Role-Based Access
    Control</em>) avec quatre (4) niveaux distincts, appliquant le <strong>principe du
    moindre privilège</strong> : chaque utilisateur ne dispose que des droits strictement
    nécessaires à l'exercice de ses fonctions.
</p>
<table>
    <tr>
        <th>Rôle</th>
        <th>Périmètre d'accès</th>
        <th>Données accessibles</th>
        <th>MFA obligatoire</th>
    </tr>
    <tr>
        <td><strong>Super Admin</strong></td>
        <td>Toute la plateforme, toutes les communes</td>
        <td>
            Toutes les infrastructures, tous les utilisateurs, journaux d'audit complets,
            paramétrage système, imports/exports globaux, gestion des communes
        </td>
        <td><strong>Oui</strong></td>
    </tr>
    <tr>
        <td><strong>Admin Commune</strong></td>
        <td>Sa commune de rattachement uniquement</td>
        <td>
            Infrastructures de sa commune, utilisateurs de sa commune, planifications,
            exports limités à sa commune
        </td>
        <td><strong>Oui</strong></td>
    </tr>
    <tr>
        <td><strong>Agent</strong></td>
        <td>Sa commune de rattachement uniquement</td>
        <td>
            Saisie et modification des infrastructures de sa commune, consultation des
            données publiées ; aucun accès aux autres communes ni aux journaux d'audit
        </td>
        <td>Non (recommandé)</td>
    </tr>
    <tr>
        <td><strong>Public</strong></td>
        <td>Espaces publics uniquement</td>
        <td>
            Consultation en lecture seule des infrastructures publiées et des statistiques
            agrégées ; aucune donnée personnelle accessible
        </td>
        <td>Sans objet</td>
    </tr>
</table>

<h3>5.2 Cycle de vie des comptes</h3>
<p>
    Le cycle de vie d'un compte utilisateur sur la plateforme ADECOB suit obligatoirement
    les étapes ci-après :
</p>
<ol>
    <li>
        <strong>Inscription (auto-inscription)</strong> — L'utilisateur soumet une demande
        de création de compte en renseignant ses informations personnelles et sa commune
        de rattachement. Le compte est créé avec le statut <em>en attente</em> et aucune
        action n'est possible avant validation.
    </li>
    <li>
        <strong>Validation de l'e-mail</strong> — Un lien de vérification est envoyé à
        l'adresse e-mail fournie. Sans validation, l'accès reste impossible. Ce mécanisme
        réduit les inscriptions abusives et les fausses identités.
    </li>
    <li>
        <strong>Approbation manuelle</strong> — L'administrateur communal (ou le Super Admin)
        examine la demande, vérifie l'identité et le rattachement communal, puis approuve
        ou rejette le compte. Cette validation humaine est obligatoire pour tous les
        profils et ne peut pas être contournée.
    </li>
    <li>
        <strong>Attribution du rôle</strong> — Le rôle définitif est attribué par un
        administrateur lors de l'approbation. Toute modification ultérieure du rôle est
        tracée dans les journaux d'audit.
    </li>
    <li>
        <strong>Utilisation active</strong> — Le compte est actif tant que l'utilisateur
        exerce ses fonctions et respecte la PSSI.
    </li>
    <li>
        <strong>Révision périodique</strong> — Une revue annuelle des comptes actifs est
        effectuée par les administrateurs, avec vérification de l'adéquation des rôles
        et suppression des accès obsolètes.
    </li>
    <li>
        <strong>Désactivation / Révocation</strong> — En cas de départ, de faute ou de
        compromission, le compte est <em>désactivé</em> (et non supprimé) afin de préserver
        la traçabilité historique. La réactivation est soumise à une nouvelle approbation.
    </li>
</ol>

<h3>5.3 Principe du moindre privilège</h3>
<p>
    Chaque fonctionnalité de la plateforme est protégée par des règles d'autorisation
    (<em>policies</em> Laravel) vérifiées côté serveur à chaque requête. Il est techniquement
    impossible pour un utilisateur d'accéder aux données d'une autre commune que la sienne,
    quand bien même il connaîtrait l'identifiant de la ressource. Cette isolation est assurée
    au niveau des requêtes de base de données et non uniquement via l'interface utilisateur.
</p>

<h3>5.4 Comptes de service et comptes partagés</h3>
<p>
    Les comptes de service (utilisés pour les imports automatisés, les scripts de sauvegarde,
    etc.) sont nominatifs, disposent uniquement des droits strictement nécessaires, et ne
    sont jamais partagés entre plusieurs processus. Aucun compte générique ou partagé entre
    plusieurs personnes physiques n'est autorisé.
</p>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 6 — AUTHENTIFICATION MULTI-FACTEURS (MFA)
     ══════════════════════════════════════════════════════════════════ --}}
<h2>6. Authentification multi-facteurs (MFA)</h2>

<h3>6.1 Principe et obligation</h3>
<p>
    La MFA est <strong>obligatoire et non désactivable</strong> pour les profils
    <em>Super Admin</em> et <em>Admin Commune</em>. Elle constitue un second facteur
    d'authentification qui s'ajoute au mot de passe et s'active systématiquement à
    chaque nouvelle ouverture de session, y compris depuis un terminal déjà connu.
</p>

<h3>6.2 Fonctionnement du code MFA</h3>
<table>
    <tr>
        <th>Paramètre</th>
        <th>Valeur</th>
    </tr>
    <tr>
        <td>Type de code</td>
        <td>Code numérique à <strong>6 chiffres</strong>, généré aléatoirement (CSPRNG)</td>
    </tr>
    <tr>
        <td>Canal de transmission</td>
        <td>E-mail envoyé à l'adresse associée au compte (connexion chiffrée TLS)</td>
    </tr>
    <tr>
        <td>Durée de validité</td>
        <td><strong>10 minutes</strong> à compter de l'envoi</td>
    </tr>
    <tr>
        <td>Tentatives maximales</td>
        <td><strong>5 tentatives</strong> avant invalidation du code et déconnexion forcée</td>
    </tr>
    <tr>
        <td>Renvoi du code</td>
        <td>Limité à <strong>3 renvois par période de 5 minutes</strong> par utilisateur</td>
    </tr>
    <tr>
        <td>Rate-limiting par IP</td>
        <td>10 tentatives de soumission de code / minute par adresse IP</td>
    </tr>
    <tr>
        <td>Stockage du code</td>
        <td>Haché en base de données ; jamais stocké en clair ni journalisé en clair</td>
    </tr>
    <tr>
        <td>Invalidation automatique</td>
        <td>Après utilisation réussie, expiration du délai ou dépassement du nombre de tentatives</td>
    </tr>
</table>

<h3>6.3 Résistance aux attaques</h3>
<p>Le dispositif MFA est conçu pour résister aux attaques suivantes :</p>
<ul>
    <li><strong>Brute-force</strong> : le rate-limiting par utilisateur et par IP, combiné
        à la limitation des renvois, rend toute attaque par force brute infaisable dans
        la fenêtre de validité de 10 minutes ;</li>
    <li><strong>Interception</strong> : le code est transmis par e-mail via une connexion TLS ;
        il est à usage unique et invalide après expiration ;</li>
    <li><strong>Rejeu</strong> : un code déjà utilisé est immédiatement invalidé en base ;</li>
    <li><strong>Fixation de session</strong> : l'ID de session est régénéré après validation
        réussie du code MFA.</li>
</ul>

<h3>6.4 Procédure en cas de perte d'accès à la boîte e-mail</h3>
<p>
    Si un administrateur ne peut plus accéder à son adresse e-mail, il doit contacter le
    Super Admin (ou le RSSI) par un canal alternatif préalablement enregistré. Après
    vérification d'identité, le Super Admin peut modifier l'adresse e-mail associée au
    compte et déclencher une réinitialisation du mot de passe. Cette opération est tracée
    dans les journaux d'audit.
</p>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 7 — CLASSIFICATION DES DONNÉES
     ══════════════════════════════════════════════════════════════════ --}}
<h2>7. Classification des données</h2>

<p>
    Toutes les données traitées par la plateforme sont classifiées selon quatre (4) niveaux
    de sensibilité. Cette classification détermine les mesures de protection à appliquer.
</p>

<table>
    <tr>
        <th>Niveau</th>
        <th>Définition</th>
        <th>Exemples sur la plateforme</th>
        <th>Mesures de protection</th>
    </tr>
    <tr>
        <td><strong>Public</strong></td>
        <td>Données librement accessibles, sans restriction</td>
        <td>
            Fiches d'infrastructures publiées (école, puits, marché), statistiques
            agrégées par commune, cartes publiques
        </td>
        <td>
            Lecture libre, indexation autorisée par les moteurs de recherche,
            aucune authentification requise, transit HTTPS
        </td>
    </tr>
    <tr>
        <td><strong>Interne</strong></td>
        <td>Données réservées aux utilisateurs authentifiés de l'organisation</td>
        <td>
            Planifications en cours, données de saisie en attente de validation,
            statistiques internes, communications inter-communes
        </td>
        <td>
            Accès restreint aux utilisateurs authentifiés de la commune concernée,
            transit HTTPS, journalisation des accès, interdiction d'export non autorisé
        </td>
    </tr>
    <tr>
        <td><strong>Confidentiel</strong></td>
        <td>Données sensibles dont la divulgation causerait un préjudice significatif</td>
        <td>
            Données personnelles des utilisateurs (nom, prénom, e-mail, numéro de
            téléphone), codes d'accès communaux, rapports d'audit internes,
            données financières des projets
        </td>
        <td>
            Chiffrement <em>at-rest</em> (cast <code>encrypted</code> via la clé
            <code>APP_KEY</code> Laravel), accès tracé dans les journaux, transit HTTPS
            forcé, accès limité aux rôles autorisés, masquage à l'affichage si inutile
        </td>
    </tr>
    <tr>
        <td><strong>Secret</strong></td>
        <td>Données dont la compromission aurait des conséquences critiques</td>
        <td>
            Mots de passe (hachés), codes MFA, jetons de session, clés API tierces,
            clé <code>APP_KEY</code> Laravel, credentials de base de données
        </td>
        <td>
            Hachage irréversible (<code>bcrypt</code>) ou chiffrement fort ; stockage
            exclusivement dans des variables d'environnement (<code>.env</code>) hors
            dépôt de code ; jamais affichés, jamais loggés, jamais transmis en clair ;
            rotation obligatoire en cas de compromission suspectée
        </td>
    </tr>
</table>

<h3>7.1 Règles transverses de traitement</h3>
<ul>
    <li>Aucune donnée de niveau <em>Confidentiel</em> ou <em>Secret</em> ne doit être
        transmise par e-mail en clair ;</li>
    <li>Les exports de données (CSV, Excel) sont réservés aux profils autorisés et tracés
        dans les journaux d'audit ;</li>
    <li>Toute copie de données sur un support amovible (clé USB) est interdite sans
        autorisation explicite du RSSI ;</li>
    <li>Les données de niveau <em>Confidentiel</em> et <em>Secret</em> doivent être
        effacées de manière sécurisée (non récupérable) lors de leur suppression.</li>
</ul>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 8 — MESURES TECHNIQUES DE PROTECTION
     ══════════════════════════════════════════════════════════════════ --}}
<h2>8. Mesures techniques de protection</h2>

<h3>8.1 Sécurité du transport (couche réseau)</h3>
<table>
    <tr>
        <th>Mesure</th>
        <th>Détail</th>
    </tr>
    <tr>
        <td>HTTPS obligatoire</td>
        <td>
            Tout trafic HTTP est redirigé vers HTTPS en production ; aucune donnée
            sensible ne transite en clair.
        </td>
    </tr>
    <tr>
        <td>TLS 1.3 minimum</td>
        <td>
            Seules les versions TLS 1.2 et 1.3 sont acceptées. TLS 1.0 et 1.1 sont
            désactivés. Les suites cryptographiques faibles (RC4, 3DES, etc.) sont rejetées.
        </td>
    </tr>
    <tr>
        <td>HSTS</td>
        <td>
            En-tête <code>Strict-Transport-Security: max-age=31536000; includeSubDomains; preload</code>
            — force le navigateur à utiliser HTTPS pour un an, avec héritage sur tous
            les sous-domaines et soumission au preload list.
        </td>
    </tr>
    <tr>
        <td>Certificat TLS</td>
        <td>
            Certificat valide, émis par une autorité reconnue, renouvelé avant expiration.
            L'expiration est surveillée et déclenche une alerte au RSSI 30 jours avant.
        </td>
    </tr>
</table>

<h3>8.2 En-têtes de sécurité HTTP</h3>
<p>Les en-têtes HTTP suivants sont appliqués sur toutes les réponses de l'application :</p>
<table>
    <tr>
        <th>En-tête</th>
        <th>Valeur / Configuration</th>
        <th>Protection visée</th>
    </tr>
    <tr>
        <td><code>X-Frame-Options</code></td>
        <td><code>SAMEORIGIN</code></td>
        <td>Prévention du <em>clickjacking</em></td>
    </tr>
    <tr>
        <td><code>X-Content-Type-Options</code></td>
        <td><code>nosniff</code></td>
        <td>Prévention du <em>MIME sniffing</em></td>
    </tr>
    <tr>
        <td><code>Referrer-Policy</code></td>
        <td><code>strict-origin-when-cross-origin</code></td>
        <td>Contrôle des informations transmises dans le référent</td>
    </tr>
    <tr>
        <td><code>Permissions-Policy</code></td>
        <td>Désactivation des API non utilisées (géolocalisation, caméra, microphone…)</td>
        <td>Réduction de la surface d'attaque</td>
    </tr>
    <tr>
        <td><code>Content-Security-Policy</code></td>
        <td>
            Restriction des sources de scripts, styles, images, iframes aux origines
            explicitement autorisées ; interdiction des scripts <em>inline</em> non noncés
        </td>
        <td>Prévention des attaques XSS et injections de contenu</td>
    </tr>
    <tr>
        <td><code>X-XSS-Protection</code></td>
        <td><code>1; mode=block</code></td>
        <td>Filtre XSS des navigateurs anciens</td>
    </tr>
    <tr>
        <td><code>Strict-Transport-Security</code></td>
        <td>Voir section 8.1</td>
        <td>Forçage HTTPS</td>
    </tr>
</table>

<h3>8.3 Protection applicative contre les injections (OWASP A03:2021)</h3>
<ul>
    <li><strong>ORM Eloquent et requêtes paramétrées</strong> : toutes les interactions avec
        la base de données passent par l'ORM Laravel (Eloquent) ou des requêtes préparées
        (<code>DB::select()</code> avec bindings). Aucune concaténation directe de données
        utilisateur dans une requête SQL n'est autorisée ;</li>
    <li><strong>Validation systématique des entrées</strong> : chaque formulaire dispose d'une
        classe <code>FormRequest</code> définissant les règles de validation (type, taille,
        format, liste blanche). Les données invalides sont rejetées avant tout traitement ;</li>
    <li><strong>Échappement automatique Blade</strong> : le moteur de templates Blade échappe
        automatiquement toutes les variables affichées via <code>@{{ }}</code>. L'opérateur
        <code>@{!! !!}</code> (non échappé) n'est utilisé que dans des contextes contrôlés
        avec des données exclusivement internes ;</li>
    <li><strong>Protection XSS</strong> : combinaison de l'échappement Blade, de la
        Content-Security-Policy et de la validation des entrées ;</li>
    <li><strong>Protection CSRF</strong> : un jeton CSRF unique par session est vérifié sur
        toutes les requêtes HTTP mutatives (POST, PUT, PATCH, DELETE) via le middleware
        <code>VerifyCsrfToken</code> de Laravel.</li>
</ul>

<h3>8.4 Protection contre les attaques par force brute et abus</h3>
<ul>
    <li><strong>Rate-limiting</strong> appliqué sur tous les formulaires sensibles :
        connexion, inscription, réinitialisation de mot de passe, MFA, contact, et
        recherche — avec des seuils adaptés à chaque action ;</li>
    <li><strong>reCAPTCHA v3</strong> intégré sur les formulaires d'inscription,
        de connexion et de contact, avec seuil de score configurable par le Super Admin ;</li>
    <li><strong>Délais progressifs (<em>exponential backoff</em>)</strong> entre les
        tentatives d'authentification échouées ;</li>
    <li><strong>Alertes automatiques</strong> en cas de dépassement de seuils critiques
        (voir section 4.4).</li>
</ul>

<h3>8.5 Chiffrement des données sensibles</h3>
<ul>
    <li>Les champs sensibles de la base de données (numéro de téléphone, code d'accès
        communal) sont chiffrés à l'aide du cast <code>encrypted</code> de Laravel,
        utilisant la clé <code>APP_KEY</code> (AES-256-CBC) ;</li>
    <li>La clé <code>APP_KEY</code> est stockée exclusivement dans le fichier
        <code>.env</code>, jamais dans le code source ni dans le dépôt Git ;</li>
    <li>La rotation de la clé <code>APP_KEY</code> est effectuée après tout incident
        de compromission potentielle ;</li>
    <li>Les mots de passe sont hachés avec <code>bcrypt</code> (voir section 4.3).</li>
</ul>

<h3>8.6 Sécurité des sessions</h3>
<table>
    <tr>
        <th>Paramètre</th>
        <th>Configuration</th>
        <th>Protection visée</th>
    </tr>
    <tr>
        <td><code>HttpOnly</code></td>
        <td>Activé</td>
        <td>Inaccessibilité du cookie de session depuis JavaScript (protection XSS)</td>
    </tr>
    <tr>
        <td><code>Secure</code></td>
        <td>Activé en production</td>
        <td>Transmission du cookie uniquement sur HTTPS</td>
    </tr>
    <tr>
        <td><code>SameSite</code></td>
        <td><code>Lax</code></td>
        <td>Protection contre les attaques CSRF cross-origin</td>
    </tr>
    <tr>
        <td>Régénération de l'ID</td>
        <td>À chaque connexion réussie et après validation MFA</td>
        <td>Prévention de la <em>session fixation</em></td>
    </tr>
    <tr>
        <td>Durée de vie</td>
        <td>Limitée (voir section 4.6)</td>
        <td>Réduction de la fenêtre d'exploitation d'une session volée</td>
    </tr>
</table>

<h3>8.7 Sécurité des dépendances</h3>
<p>
    Les dépendances Composer (PHP) et NPM (JavaScript) font l'objet d'une surveillance
    régulière. Le Super Admin technique vérifie mensuellement les avis de sécurité publiés
    pour les composants utilisés et applique les correctifs de sécurité dans les meilleurs
    délais (voir section 12).
</p>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 9 — JOURNALISATION ET TRAÇABILITÉ
     ══════════════════════════════════════════════════════════════════ --}}
<h2>9. Journalisation et traçabilité</h2>

<h3>9.1 Événements journalisés</h3>
<p>
    La plateforme enregistre dans la table <code>audit_logs</code> l'ensemble des événements
    sensibles suivants, avec horodatage UTC, identifiant de l'utilisateur, adresse IP source
    et description de l'action :
</p>
<table>
    <tr>
        <th>Catégorie</th>
        <th>Événements enregistrés</th>
    </tr>
    <tr>
        <td>Authentification</td>
        <td>
            Connexion réussie, déconnexion, échec de connexion (avec nombre de tentatives),
            réinitialisation de mot de passe (demande et utilisation du lien), expiration
            de session, connexion depuis une nouvelle adresse IP
        </td>
    </tr>
    <tr>
        <td>MFA</td>
        <td>
            Envoi d'un code MFA, validation réussie, échec de validation, dépassement du
            nombre de tentatives, renvoi de code, invalidation manuelle
        </td>
    </tr>
    <tr>
        <td>Gestion des comptes</td>
        <td>
            Inscription, validation d'e-mail, approbation ou rejet d'un compte, modification
            de profil, changement de rôle, désactivation, réactivation, toute modification
            d'adresse e-mail ou de commune de rattachement
        </td>
    </tr>
    <tr>
        <td>Données — CRUD complet</td>
        <td>
            Création, modification et suppression de : infrastructures, communes,
            planifications, catégories, types d'infrastructure ; avec capture de l'état
            avant/après pour les modifications (<em>before/after</em>)
        </td>
    </tr>
    <tr>
        <td>Imports / Exports</td>
        <td>
            Tout import de fichier (type, nombre de lignes, statut), tout export de données
            (format, périmètre, nombre de lignes exportées)
        </td>
    </tr>
    <tr>
        <td>Accès aux journaux</td>
        <td>
            Toute consultation des journaux d'audit par un Super Admin
        </td>
    </tr>
    <tr>
        <td>Paramétrage système</td>
        <td>
            Modifications de la configuration de la plateforme, changements de clés,
            déploiements applicatifs
        </td>
    </tr>
    <tr>
        <td>Incidents et alertes</td>
        <td>
            Déclenchement d'un rate-limit, tentative d'accès non autorisé détectée,
            alerte de sécurité automatique
        </td>
    </tr>
</table>

<h3>9.2 Durée de conservation et accès</h3>
<ul>
    <li>Les journaux d'audit sont conservés <strong>au minimum 12 mois</strong> en base
        de données active, puis archivés pour une durée totale de <strong>36 mois</strong>
        conformément aux exigences légales du Code du numérique béninois ;</li>
    <li>L'accès aux journaux est <strong>réservé exclusivement aux Super Admins</strong> ;
        aucun autre profil ne peut consulter les journaux d'audit ;</li>
    <li>Les journaux sont en <em>lecture seule</em> pour tous les utilisateurs, y compris
        les Super Admins : ils ne peuvent ni être modifiés, ni être supprimés via l'interface
        applicative ;</li>
    <li>Toute consultation des journaux est elle-même consignée dans les journaux (méta-audit).</li>
</ul>

<h3>9.3 Ce qui n'est jamais journalisé</h3>
<div class="alert alert-warning">
    <strong>Interdiction absolue.</strong> Les éléments suivants ne sont <em>jamais</em>
    enregistrés dans les journaux, quelles que soient les circonstances :
</div>
<ul>
    <li>Mots de passe, même hachés ;</li>
    <li>Codes MFA en clair ;</li>
    <li>Jetons de session, tokens de réinitialisation en clair ;</li>
    <li>Clés API et secrets d'application ;</li>
    <li>Contenu intégral des champs chiffrés (téléphone, code commune) ;</li>
    <li>Données bancaires ou financières individuelles.</li>
</ul>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 10 — SAUVEGARDES ET PLAN DE CONTINUITÉ
     ══════════════════════════════════════════════════════════════════ --}}
<h2>10. Sauvegardes et plan de continuité d'activité</h2>

<h3>10.1 Politique de sauvegarde</h3>
<table>
    <tr>
        <th>Paramètre</th>
        <th>Valeur cible</th>
    </tr>
    <tr>
        <td>Fréquence des sauvegardes</td>
        <td>Quotidienne (nuit, heure creuse)</td>
    </tr>
    <tr>
        <td>Périmètre de la sauvegarde</td>
        <td>Base de données MariaDB complète (schéma + données) + fichiers uploadés</td>
    </tr>
    <tr>
        <td>Durée de rétention</td>
        <td><strong>30 jours</strong> de sauvegardes journalières conservées</td>
    </tr>
    <tr>
        <td>Copie hors site</td>
        <td>
            Au moins une copie stockée dans un emplacement géographiquement distinct
            du serveur principal (serveur distant ou service de stockage cloud chiffré)
        </td>
    </tr>
    <tr>
        <td>Chiffrement des sauvegardes</td>
        <td>
            Les archives de sauvegarde sont chiffrées avant transfert et stockage
            hors site (AES-256 ou équivalent)
        </td>
    </tr>
    <tr>
        <td>Vérification de l'intégrité</td>
        <td>
            Contrôle d'intégrité (somme de contrôle SHA-256) automatisé après chaque
            sauvegarde ; alerte en cas d'anomalie
        </td>
    </tr>
    <tr>
        <td>Tests de restauration</td>
        <td>
            <strong>Semestriels</strong> — restauration complète sur environnement de test
            avec vérification de la cohérence des données
        </td>
    </tr>
</table>

<h3>10.2 Objectifs de continuité</h3>
<table>
    <tr>
        <th>Indicateur</th>
        <th>Définition</th>
        <th>Cible</th>
    </tr>
    <tr>
        <td><strong>RTO</strong> (<em>Recovery Time Objective</em>)</td>
        <td>Durée maximale acceptable d'indisponibilité après un incident</td>
        <td><strong>8 heures</strong> ouvrables</td>
    </tr>
    <tr>
        <td><strong>RPO</strong> (<em>Recovery Point Objective</em>)</td>
        <td>Perte de données maximale acceptable (ancienneté de la dernière sauvegarde)</td>
        <td><strong>24 heures</strong></td>
    </tr>
    <tr>
        <td><strong>Disponibilité cible</strong></td>
        <td>Taux de disponibilité annuel de la plateforme</td>
        <td><strong>99 %</strong> (≈ 87 h d'indisponibilité max/an)</td>
    </tr>
</table>

<h3>10.3 Procédure de restauration</h3>
<ol>
    <li>Déclenchement par le RSSI ou le Super Admin après qualification de l'incident ;</li>
    <li>Identification de la sauvegarde la plus récente et intègre ;</li>
    <li>Restauration sur l'environnement cible avec vérification des intégrités ;</li>
    <li>Tests fonctionnels post-restauration (connexion, lecture, écriture, MFA) ;</li>
    <li>Communication aux utilisateurs sur la période de données éventuellement perdue ;</li>
    <li>Documentation de l'incident et de la restauration dans le registre des incidents.</li>
</ol>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 11 — GESTION DES INCIDENTS DE SÉCURITÉ
     ══════════════════════════════════════════════════════════════════ --}}
<h2>11. Gestion des incidents de sécurité</h2>

<h3>11.1 Définition d'un incident de sécurité</h3>
<p>
    Est considéré comme incident de sécurité tout événement, avéré ou suspecté, compromettant
    la <strong>confidentialité</strong>, l'<strong>intégrité</strong> ou la
    <strong>disponibilité</strong> du système d'information, notamment :
</p>
<ul>
    <li>Intrusion avérée ou tentée dans le système ;</li>
    <li>Fuite, accès non autorisé ou divulgation de données personnelles ou confidentielles ;</li>
    <li>Compromission d'un compte administrateur (phishing, vol de credentials) ;</li>
    <li>Injection de code malveillant (malware, ransomware, backdoor) ;</li>
    <li>Indisponibilité majeure ou non planifiée de la plateforme ;</li>
    <li>Corruption ou destruction de données ;</li>
    <li>Usurpation d'identité d'un utilisateur de la plateforme ;</li>
    <li>Violation des règles définies dans la présente PSSI par un utilisateur interne.</li>
</ul>

<h3>11.2 Niveaux de gravité</h3>
<table>
    <tr>
        <th>Niveau</th>
        <th>Critères</th>
        <th>Délai de réponse initial</th>
    </tr>
    <tr>
        <td><strong>Critique</strong></td>
        <td>
            Fuite de données personnelles à grande échelle, compromission d'un compte
            Super Admin, indisponibilité totale de la plateforme, injection malveillante active
        </td>
        <td><strong>1 heure</strong></td>
    </tr>
    <tr>
        <td><strong>Élevé</strong></td>
        <td>
            Accès non autorisé à des données confidentielles, compromission d'un compte
            Admin Commune, indisponibilité partielle affectant plusieurs communes
        </td>
        <td><strong>4 heures</strong></td>
    </tr>
    <tr>
        <td><strong>Modéré</strong></td>
        <td>
            Tentative d'intrusion détectée et bloquée, anomalie comportementale d'un compte,
            dégradation de performance significative
        </td>
        <td><strong>24 heures</strong></td>
    </tr>
    <tr>
        <td><strong>Faible</strong></td>
        <td>
            Violation mineure de la PSSI sans impact sur les données, signalement d'une
            vulnérabilité sans exploitation confirmée
        </td>
        <td><strong>72 heures</strong></td>
    </tr>
</table>

<h3>11.3 Procédure de réponse aux incidents (6 étapes)</h3>
<ol>
    <li>
        <strong>Détection et signalement.</strong> L'incident est détecté par un utilisateur,
        un outil de surveillance automatisé, le RSSI ou un tiers. Il est signalé immédiatement
        via le <a href="{{ route('contact.form') }}">formulaire de contact officiel</a> (mention
        « INCIDENT SÉCURITÉ » dans l'objet) ou par e-mail direct au RSSI et au Super Admin.
        Un numéro de dossier est attribué et la date/heure de détection est enregistrée.
    </li>
    <li>
        <strong>Qualification et évaluation.</strong> Dans le délai correspondant au niveau de
        gravité (voir tableau 11.2), le RSSI qualifie l'incident : nature, périmètre affecté,
        données concernées, vecteur d'attaque probable. Il détermine s'il s'agit d'un incident
        de violation de données à caractère personnel au sens de l'article 391 du Code du
        numérique béninois.
    </li>
    <li>
        <strong>Confinement.</strong> Actions immédiates visant à limiter la propagation :
        désactivation du ou des comptes compromis, blocage des adresses IP suspectes,
        isolation du composant affecté si nécessaire, invalidation des jetons de session
        actifs, rotation des secrets suspectés d'être compromis (<code>APP_KEY</code>,
        mots de passe administrateurs, credentials de base de données).
    </li>
    <li>
        <strong>Éradication et remédiation.</strong> Identification et suppression de la
        cause racine : déploiement d'un correctif, restauration à partir d'une sauvegarde
        saine, scan complet de l'application et de la base de données, vérification de
        l'intégrité de tous les composants. Les actions de remédiation sont documentées.
    </li>
    <li>
        <strong>Notification.</strong>
        <ul>
            <li>
                En cas de violation de données à caractère personnel, l'<strong>APDP
                (Autorité de Protection des Données Personnelles du Bénin)</strong> est
                notifiée <strong>dans les 72 heures</strong> suivant la découverte de
                l'incident, conformément à l'article 391 du Code du numérique
                (loi n°2017-20 du 20 avril 2018) ;
            </li>
            <li>
                Si le risque pour les personnes concernées est élevé, ces dernières sont
                informées directement dans les meilleurs délais ;
            </li>
            <li>
                La direction de l'ADECOB et la DDC Suisse sont informées de tout incident
                de niveau Élevé ou Critique dans les 24 heures.
            </li>
        </ul>
    </li>
    <li>
        <strong>Retour d'expérience (<em>Post-mortem</em>).</strong> Dans les 15 jours
        suivant la clôture de l'incident, le RSSI produit un rapport d'analyse post-incident
        comprenant : chronologie, cause racine, impact réel, mesures de remédiation prises
        et recommandations pour prévenir la récidive. Ce rapport entraîne si nécessaire une
        révision de la PSSI.
    </li>
</ol>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 12 — GESTION DES VULNÉRABILITÉS
     ══════════════════════════════════════════════════════════════════ --}}
<h2>12. Gestion des vulnérabilités</h2>

<h3>12.1 Politique de divulgation responsable</h3>
<p>
    L'ADECOB encourage la divulgation responsable des vulnérabilités. Toute personne
    découvrant une vulnérabilité affectant la plateforme est invitée à la signaler
    de manière confidentielle via le <a href="{{ route('contact.form') }}">formulaire de contact</a>
    (mention « DIVULGATION RESPONSABLE — SÉCURITÉ »), en fournissant :
</p>
<ul>
    <li>Une description précise de la vulnérabilité ;</li>
    <li>Les étapes pour la reproduire ;</li>
    <li>L'impact potentiel estimé ;</li>
    <li>Les éventuelles preuves de concept (<em>Proof of Concept</em>).</li>
</ul>
<p>
    L'ADECOB s'engage à accuser réception dans un délai de <strong>72 heures</strong>,
    à analyser le signalement dans un délai de <strong>7 jours ouvrables</strong> et à
    informer le déclarant de la suite donnée. En contrepartie, le déclarant s'engage à ne
    pas divulguer publiquement la vulnérabilité avant qu'un correctif ait été déployé ou
    qu'un délai de <strong>90 jours</strong> se soit écoulé depuis le signalement initial.
</p>

<h3>12.2 Gestion des correctifs (<em>Patch Management</em>)</h3>
<table>
    <tr>
        <th>Catégorie</th>
        <th>Délai de traitement cible</th>
    </tr>
    <tr>
        <td>Correctif critique (CVSS ≥ 9.0)</td>
        <td><strong>48 heures</strong> après publication ou notification</td>
    </tr>
    <tr>
        <td>Correctif élevé (CVSS 7.0–8.9)</td>
        <td><strong>7 jours</strong> après publication</td>
    </tr>
    <tr>
        <td>Correctif modéré (CVSS 4.0–6.9)</td>
        <td><strong>30 jours</strong> après publication</td>
    </tr>
    <tr>
        <td>Correctif faible (CVSS < 4.0)</td>
        <td>Intégré à la prochaine release planifiée</td>
    </tr>
</table>
<p>
    Le Super Admin technique surveille mensuellement les bulletins de sécurité de Laravel,
    MariaDB, PHP, des dépendances Composer/NPM, et du système d'exploitation du serveur.
    Toute mise à jour de sécurité est testée sur un environnement de préproduction avant
    déploiement en production.
</p>

<h3>12.3 Test d'intrusion (Pentest)</h3>
<p>
    Un test d'intrusion applicatif et d'infrastructure est <strong>recommandé annuellement</strong>,
    réalisé par un prestataire indépendant qualifié. Les conclusions du pentest font l'objet
    d'un plan de remédiation priorisé, dont le suivi est assuré par le RSSI. Les rapports
    de pentest sont classifiés <em>Confidentiel</em> et conservés 3 ans.
</p>

<h3>12.4 Surveillance continue</h3>
<p>
    Des outils de surveillance automatisée (journaux applicatifs, alertes d'anomalie)
    sont mis en place pour détecter les comportements anormaux : pics de trafic inhabituels,
    multiplication d'erreurs 4xx/5xx, tentatives d'authentification en masse, accès à des
    URLs inhabituelles. Les alertes sont transmises au RSSI et au Super Admin.
</p>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 13 — SÉCURITÉ DES ACCÈS PHYSIQUES ET DES TIERS
     ══════════════════════════════════════════════════════════════════ --}}
<h2>13. Sécurité des accès physiques et des tiers</h2>

<h3>13.1 Infrastructure d'hébergement</h3>
<p>
    La plateforme est hébergée chez un prestataire tiers. L'ADECOB exige de son hébergeur
    les garanties minimales suivantes, formalisées dans un contrat de service :
</p>
<ul>
    <li>Sécurité physique des centres de données (contrôle d'accès, vidéosurveillance,
        alimentation redondante, anti-incendie) ;</li>
    <li>Isolation des environnements (virtualisation sécurisée, cloisonnement réseau) ;</li>
    <li>Surveillance réseau 24h/24 et 7j/7 avec alertes en cas d'anomalie ;</li>
    <li>Sauvegardes indépendantes de niveau hébergeur ;</li>
    <li>Procédures documentées de gestion des incidents physiques ;</li>
    <li>Notification de l'ADECOB dans un délai de 4 heures en cas d'incident
        affectant son environnement.</li>
</ul>

<h3>13.2 Accords de traitement des données avec les tiers</h3>
<p>
    Conformément à la loi n°2017-20 du 20 avril 2018, tout prestataire traitant des données
    personnelles pour le compte de l'ADECOB (hébergeur, mainteneur, auditeur) doit signer
    un <strong>accord de traitement des données</strong> (équivalent DPA — <em>Data Processing
    Agreement</em>), précisant :
</p>
<ul>
    <li>La nature et la finalité des traitements sous-traités ;</li>
    <li>Les mesures de sécurité techniques et organisationnelles appliquées ;</li>
    <li>L'obligation de confidentialité du personnel du prestataire ;</li>
    <li>L'interdiction de sous-traiter sans accord préalable de l'ADECOB ;</li>
    <li>Les modalités de notification des incidents de sécurité à l'ADECOB ;</li>
    <li>Les conditions d'audit et de contrôle par l'ADECOB ou un tiers mandaté.</li>
</ul>

<h3>13.3 Accès des prestataires à la plateforme</h3>
<p>
    Tout accès technique d'un prestataire externe (mainteneur, auditeur, développeur tiers)
    à la plateforme ou à ses données est soumis aux règles suivantes :
</p>
<ul>
    <li>Autorisation préalable écrite du RSSI ;</li>
    <li>Création d'un compte nominatif temporaire avec droits limités au strict nécessaire ;</li>
    <li>Traçabilité complète de toutes les actions dans les journaux d'audit ;</li>
    <li>Révocation immédiate du compte à la fin de l'intervention ;</li>
    <li>Interdiction d'emporter des données hors du périmètre sans accord explicite.</li>
</ul>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 14 — SENSIBILISATION ET FORMATION
     ══════════════════════════════════════════════════════════════════ --}}
<h2>14. Sensibilisation et formation</h2>

<h3>14.1 Accueil des nouveaux administrateurs</h3>
<p>
    Tout nouvel utilisateur disposant d'un rôle <em>Admin Commune</em> ou <em>Super Admin</em>
    reçoit, préalablement à l'activation de son compte, une <strong>note de sécurité
    d'intégration</strong> couvrant les points suivants :
</p>
<ul>
    <li>Présentation synthétique de la PSSI et des obligations associées ;</li>
    <li>Règles de création et de gestion du mot de passe ;</li>
    <li>Fonctionnement et importance de la MFA ;</li>
    <li>Identification des tentatives de phishing et de l'ingénierie sociale ;</li>
    <li>Procédure de signalement d'un incident ou d'une anomalie ;</li>
    <li>Règles de confidentialité des données accessibles via la plateforme ;</li>
    <li>Interdiction de partager ses credentials avec quiconque, y compris le RSSI.</li>
</ul>
<p>
    La réception de cette note est attestée par signature (physique ou électronique) avant
    toute attribution de droits d'administration.
</p>

<h3>14.2 Formation annuelle de rappel</h3>
<p>
    Une session de sensibilisation à la sécurité informatique est organisée
    <strong>chaque année</strong> à destination de l'ensemble des utilisateurs ayant un
    accès authentifié. Elle aborde notamment :
</p>
<ul>
    <li>Le bilan des incidents de l'année écoulée et les leçons tirées ;</li>
    <li>Les évolutions de la PSSI depuis la dernière formation ;</li>
    <li>Les nouvelles menaces et techniques d'attaque émergentes ;</li>
    <li>Les bonnes pratiques d'hygiène informatique au quotidien ;</li>
    <li>Un rappel des sanctions encourues en cas de manquement.</li>
</ul>

<h3>14.3 Communication continue</h3>
<p>
    Le RSSI peut, à tout moment, diffuser des alertes de sécurité ponctuelles aux
    administrateurs (via l'interface de la plateforme ou par e-mail) en cas de menace
    émergente, de correctif critique à appliquer sur leurs postes de travail, ou de
    modification importante des règles de sécurité.
</p>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 15 — SANCTIONS
     ══════════════════════════════════════════════════════════════════ --}}
<h2>15. Sanctions</h2>

<h3>15.1 Manquements visés</h3>
<p>Constituent des manquements à la présente PSSI, de manière non exhaustive :</p>
<ul>
    <li>Le partage de ses identifiants de connexion avec un tiers ;</li>
    <li>L'utilisation de la plateforme à des fins étrangères à sa mission ;</li>
    <li>La tentative d'accès à des données hors de son périmètre autorisé ;</li>
    <li>L'export non autorisé de données confidentielles ;</li>
    <li>Le contournement délibéré d'une mesure de sécurité (MFA, rate-limit, CSRF) ;</li>
    <li>La dissimulation d'un incident de sécurité ;</li>
    <li>La modification non autorisée des journaux d'audit ;</li>
    <li>L'installation de logiciels malveillants ou de backdoors.</li>
</ul>

<h3>15.2 Sanctions administratives internes</h3>
<p>
    Tout manquement aux règles de la PSSI peut entraîner, selon la gravité et la nature
    de la violation, les mesures suivantes décidées par la direction de l'ADECOB :
</p>
<ul>
    <li>Avertissement écrit ;</li>
    <li>Suspension temporaire de l'accès à la plateforme ;</li>
    <li>Révocation définitive du compte et des droits d'accès ;</li>
    <li>Signalement à l'autorité de tutelle concernée (commune, ministère) ;</li>
    <li>Résiliation du contrat ou de la convention de collaboration.</li>
</ul>

<h3>15.3 Sanctions légales</h3>
<p>
    Indépendamment des sanctions administratives, tout manquement constitutif d'une
    infraction pénale en vertu de la législation béninoise peut faire l'objet de poursuites
    judiciaires. Notamment :
</p>
<ul>
    <li>
        <strong>Accès frauduleux à un système informatique</strong> — Code du numérique
        du Bénin, art. 549 et suivants : peines d'emprisonnement et amendes ;
    </li>
    <li>
        <strong>Atteinte à l'intégrité des données</strong> — art. 554 et suivants :
        sanctions pour toute modification, suppression ou altération non autorisée
        de données ;
    </li>
    <li>
        <strong>Violation des données personnelles</strong> — art. 391 combiné avec les
        dispositions répressives du Code du numérique : obligation de notification et
        sanctions en cas de manquement délibéré ;
    </li>
    <li>
        <strong>Escroquerie et usurpation d'identité numérique</strong> — dispositions
        pénales du Code du numérique et du Code pénal béninois.
    </li>
</ul>
<p>
    L'ADECOB se réserve le droit de porter plainte auprès des autorités compétentes
    (APDP, CRIET — Cour de Répression des Infractions Économiques et du Terrorisme)
    pour tout acte malveillant avéré.
</p>


{{-- ══════════════════════════════════════════════════════════════════
     SECTION 16 — RÉVISION ET MISE À JOUR
     ══════════════════════════════════════════════════════════════════ --}}
<h2>16. Révision et mise à jour</h2>

<h3>16.1 Déclencheurs de révision</h3>
<p>La PSSI est révisée dans les cas suivants :</p>
<ul>
    <li>Révision annuelle planifiée (au plus tard au 31 mars de chaque année) ;</li>
    <li>Dans les 30 jours suivant tout incident de sécurité de niveau Élevé ou Critique ;</li>
    <li>Lors de toute évolution technologique majeure de la plateforme (nouveau framework,
        changement de fournisseur d'hébergement, intégration d'un nouveau service tiers) ;</li>
    <li>En cas d'évolution du cadre légal ou réglementaire applicable ;</li>
    <li>Sur recommandation suite à un audit ou un test d'intrusion.</li>
</ul>

<h3>16.2 Processus de révision</h3>
<ol>
    <li>Le RSSI prépare une proposition de révision documentant les changements envisagés ;</li>
    <li>La direction de l'ADECOB valide les modifications ;</li>
    <li>La version révisée est publiée sur la plateforme avec un numéro de version incrémenté
        et une nouvelle date d'entrée en vigueur ;</li>
    <li>Les utilisateurs concernés (administrateurs) sont informés des modifications
        significatives par notification dans la plateforme ou par e-mail.</li>
</ol>

<h3>16.3 Historique des versions</h3>
<table>
    <tr>
        <th>Version</th>
        <th>Date</th>
        <th>Auteur</th>
        <th>Modifications principales</th>
    </tr>
    <tr>
        <td>1.0</td>
        <td>21/06/2026</td>
        <td>RSSI ADECOB</td>
        <td>Création initiale de la PSSI</td>
    </tr>
    <tr>
        <td>1.1</td>
        <td>30/06/2026</td>
        <td>RSSI ADECOB</td>
        <td>
            Extension complète : ajout des sections MFA détaillée, classification des données,
            gestion des vulnérabilités, continuité d'activité (RTO/RPO), sécurité des tiers,
            historique des versions ; mise à jour des paramètres de mots de passe par rôle
        </td>
    </tr>
</table>

<h3>16.4 Entrée en vigueur</h3>
<p>
    La présente version 1.1 de la PSSI entre en vigueur le <strong>30 juin 2026</strong>.
    Elle annule et remplace toutes les versions antérieures. Tout utilisateur de la plateforme
    est réputé avoir pris connaissance de ce document et en accepter les termes dès lors
    qu'il accède à la plateforme après cette date.
</p>

<div class="alert alert-light border mt-4">
    <strong>Documents connexes.</strong> La présente PSSI doit être lue conjointement avec :
    la <a href="{{ route('legal.confidentialite') }}">Politique de confidentialité</a>,
    les <a href="{{ route('legal.cgu') }}">Conditions Générales d'Utilisation</a>,
    et le <a href="{{ route('legal.registre') }}">Registre des traitements de données personnelles</a>.
    Pour toute question, utilisez le <a href="{{ route('contact.form') }}">formulaire de contact</a>.
</div>

@endsection
