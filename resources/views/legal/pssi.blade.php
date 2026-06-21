@extends('legal._layout')

@section('title', 'PSSI — Politique de Sécurité du Système d\'Information')
@section('doc_title', 'Politique de Sécurité du Système d\'Information (PSSI)')
@section('doc_version', '1.0')
@section('doc_date', '21/06/2026')

@section('doc_content')

<div class="alert alert-light border">
    <strong>Objet.</strong> La présente Politique de Sécurité du Système d'Information (PSSI) définit
    les règles, mesures techniques et organisationnelles mises en œuvre par l'ADECOB pour garantir
    la <strong>confidentialité, l'intégrité, la disponibilité et la traçabilité</strong> des données
    traitées par la plateforme « ADECOB Infrastructure Plannification ».
</div>

<h2>1. Périmètre</h2>
<p>La PSSI s'applique :</p>
<ul>
    <li>à l'ensemble des composants techniques de la plateforme (application Laravel, base de données,
        serveurs, sauvegardes, services tiers) ;</li>
    <li>à l'ensemble des utilisateurs disposant d'un compte (super-administrateurs, administrateurs
        communaux, agents de mairie) ainsi qu'aux visiteurs des espaces publics ;</li>
    <li>aux prestataires techniques intervenant sur la solution (hébergeur, mainteneur).</li>
</ul>

<h2>2. Gouvernance de la sécurité</h2>
<p>L'ADECOB désigne en interne :</p>
<ul>
    <li>un <strong>Responsable de la Sécurité du SI (RSSI)</strong> en charge du pilotage de la PSSI ;</li>
    <li>un <strong>référent administrateur de la plateforme</strong> (Super Admin technique) ;</li>
    <li>un <strong>point de contact incidents</strong> joignable via le formulaire de contact officiel.</li>
</ul>
<p>La PSSI est revue au moins une fois par an et après tout incident majeur.</p>

<h2>3. Politique des mots de passe</h2>
<p>La plateforme applique nativement les règles suivantes (vérifiées côté serveur) :</p>
<table>
    <tr><th>Règle</th><th>Valeur</th></tr>
    <tr><td>Longueur minimale</td><td>10 caractères</td></tr>
    <tr><td>Composition obligatoire</td><td>au moins 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial</td></tr>
    <tr><td>Mots de passe interdits</td><td>identiques au nom, prénom, e-mail ou liste publique connue</td></tr>
    <tr><td>Stockage</td><td>hachage <code>bcrypt</code> avec sel unique (jamais en clair)</td></tr>
    <tr><td>Tentatives de connexion</td><td>limitées à 5/min par e-mail+IP, 20/min par IP (rate-limit)</td></tr>
    <tr><td>Réinitialisation</td><td>par lien à usage unique envoyé par e-mail, valable 60 minutes</td></tr>
    <tr><td>Renouvellement recommandé</td><td>tous les 6 mois pour les comptes administrateurs</td></tr>
</table>

<h2>4. Gestion des accès</h2>

<h3>4.1 Modèle de rôles</h3>
<table>
    <tr><th>Rôle</th><th>Périmètre</th></tr>
    <tr><td>Public</td><td>Consultation en lecture seule des infrastructures publiées</td></tr>
    <tr><td>Agent de mairie</td><td>Saisie/planification sur sa <em>commune de rattachement uniquement</em></td></tr>
    <tr><td>Administrateur communal</td><td>Gestion des données et utilisateurs de sa commune</td></tr>
    <tr><td>Super-administrateur</td><td>Gestion globale, audit, paramétrage de la plateforme</td></tr>
</table>

<h3>4.2 Cycle de vie des comptes</h3>
<ul>
    <li><strong>Création</strong> : auto-inscription puis <em>validation manuelle</em> par un administrateur ;</li>
    <li><strong>Modification</strong> : les changements de rôle sont tracés dans le journal d'audit ;</li>
    <li><strong>Désactivation</strong> : un compte inactif ou refusé est désactivé sans suppression
        afin de conserver la traçabilité historique ;</li>
    <li><strong>Revue périodique</strong> : revue annuelle des comptes administrateurs.</li>
</ul>

<h3>4.3 Authentification multi-facteurs (MFA)</h3>
<p>L'authentification multi-facteurs par e-mail (code à 6 chiffres, validité 10 minutes,
3 tentatives maximum) est <strong>obligatoire</strong> pour tous les comptes
super-administrateurs et administrateurs communaux à chaque ouverture de session.</p>

<h2>5. Classification des données</h2>
<table>
    <tr><th>Niveau</th><th>Exemples</th><th>Mesures</th></tr>
    <tr><td><strong>Public</strong></td>
        <td>Infrastructures publiées, statistiques agrégées</td>
        <td>Lecture libre, indexation autorisée</td></tr>
    <tr><td><strong>Interne</strong></td>
        <td>Planifications, données de saisie agent</td>
        <td>Accès restreint à la commune concernée, journalisation</td></tr>
    <tr><td><strong>Confidentiel</strong></td>
        <td>Données personnelles (nom, prénom, e-mail, téléphone), codes d'accès commune</td>
        <td>Chiffrement <em>at-rest</em> (cast <code>encrypted</code> Laravel), accès tracé, transit HTTPS forcé</td></tr>
    <tr><td><strong>Secret</strong></td>
        <td>Mots de passe, jetons de session, clés d'API</td>
        <td>Hachage ou stockage chiffré, jamais affichés, jamais loggés</td></tr>
</table>

<h2>6. Mesures techniques de protection</h2>
<ul>
    <li><strong>Transport</strong> : HTTPS forcé en production, HSTS (1 an, includeSubDomains, preload) ;</li>
    <li><strong>En-têtes HTTP</strong> : <code>X-Frame-Options</code>, <code>X-Content-Type-Options</code>,
        <code>Referrer-Policy</code>, <code>Permissions-Policy</code>, <code>Content-Security-Policy</code> ;</li>
    <li><strong>Sessions</strong> : cookies <code>HttpOnly</code>, <code>SameSite=Lax</code>,
        <code>Secure</code> en production, régénération de l'ID de session à la connexion ;</li>
    <li><strong>Protection CSRF</strong> : jeton vérifié sur toutes les requêtes mutatives ;</li>
    <li><strong>Anti-injection</strong> : ORM Eloquent (requêtes paramétrées), validation systématique
        via <code>FormRequest</code>, échappement Blade par défaut ;</li>
    <li><strong>Anti brute-force</strong> : <em>rate-limiting</em> sur login, register, reset password
        et formulaire de contact ;</li>
    <li><strong>reCAPTCHA v3</strong> sur les formulaires sensibles ;</li>
    <li><strong>Chiffrement at-rest</strong> des champs sensibles (téléphone, code d'accès commune)
        via la clé <code>APP_KEY</code> applicative ;</li>
    <li><strong>Sauvegardes</strong> : exports réguliers de la base de données, conservation
        d'au moins 30 jours, restitution testée semestriellement.</li>
</ul>

<h2>7. Journalisation et audit</h2>
<p>Sont enregistrés dans la table <code>audit_logs</code> et conservés au moins
<strong>12 mois</strong> :</p>
<ul>
    <li>connexions, déconnexions et échecs d'authentification ;</li>
    <li>validations et rejets de comptes ;</li>
    <li>créations, modifications et suppressions sur les entités sensibles
        (utilisateurs, communes, infrastructures, planifications) ;</li>
    <li>déclenchements de MFA et tentatives d'accès non autorisé.</li>
</ul>
<p>Les journaux sont consultables uniquement par les super-administrateurs.</p>

<h2>8. Gestion des incidents de sécurité</h2>
<h3>8.1 Définition</h3>
<p>Est considéré comme incident tout événement compromettant la confidentialité,
l'intégrité ou la disponibilité de la plateforme : intrusion, fuite de données,
indisponibilité majeure, compromission de compte administrateur, code malveillant.</p>

<h3>8.2 Procédure</h3>
<ol>
    <li><strong>Détection &amp; signalement</strong> — par l'utilisateur, le RSSI ou un outil
        de surveillance, via le canal contact officiel ou par e-mail au RSSI.</li>
    <li><strong>Qualification</strong> (sous 24 h) — gravité faible / modérée / élevée / critique.</li>
    <li><strong>Confinement</strong> — désactivation du compte ou du composant touché,
        rotation des secrets compromis (<code>APP_KEY</code>, mots de passe administrateurs).</li>
    <li><strong>Éradication &amp; remédiation</strong> — correctif déployé, scan complet.</li>
    <li><strong>Notification</strong> — en cas d'atteinte à des données personnelles,
        information de l'<strong>APDP (Autorité de Protection des Données Personnelles)</strong>
        du Bénin <em>dans les 72 heures</em>, et information des personnes concernées
        si le risque est élevé (art. 391 du Code du numérique).</li>
    <li><strong>Retour d'expérience</strong> — analyse post-incident, mise à jour de la PSSI.</li>
</ol>

<h2>9. Sensibilisation et formation</h2>
<p>Chaque nouvel administrateur reçoit une note d'usage rappelant : confidentialité du mot
de passe, vigilance phishing, signalement des incidents et bon usage de la MFA.</p>

<h2>10. Sanctions</h2>
<p>Tout manquement aux règles de la présente PSSI peut entraîner la suspension immédiate
du compte concerné et, le cas échéant, des poursuites conformément à la législation béninoise.</p>

<h2>11. Révision</h2>
<p>La PSSI est revue annuellement par le RSSI de l'ADECOB. La présente version 1.0
prend effet au 21 juin 2026.</p>

@endsection
