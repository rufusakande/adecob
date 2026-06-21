@extends('legal._layout')

@section('title', 'Registre des traitements de données')
@section('doc_title', 'Registre des traitements de données à caractère personnel')
@section('doc_version', '1.0')
@section('doc_date', '21/06/2026')

@section('doc_content')

<div class="alert alert-light border">
    Document tenu en application des articles 412 et suivants de la <em>loi n°2017-20
    portant Code du numérique en République du Bénin</em>. Il recense l'ensemble des
    traitements de données à caractère personnel mis en œuvre par l'ADECOB sur la Plateforme.
</div>

<h3>Responsable du traitement</h3>
<p><strong>ADECOB</strong> — Association pour le Développement des Communes du Borgou,
Parakou, République du Bénin · Contact : <a href="{{ route('contact') }}">formulaire de contact</a>.</p>

<h2>Traitement n°1 — Gestion des comptes utilisateurs</h2>
<table>
    <tr><th>Finalité</th><td>Création, authentification, validation et administration des comptes</td></tr>
    <tr><th>Base légale</th><td>Consentement + mission d'intérêt public</td></tr>
    <tr><th>Catégories de personnes</th><td>Agents communaux, administrateurs, super-administrateurs</td></tr>
    <tr><th>Catégories de données</th><td>Nom, prénom, e-mail, téléphone (chiffré), mot de passe (haché), rôle, commune, statut</td></tr>
    <tr><th>Destinataires</th><td>Super-administrateurs ADECOB, administrateurs de la commune concernée</td></tr>
    <tr><th>Durée de conservation</th><td>Compte actif + 3 ans après dernière connexion ; anonymisation après 1 an pour comptes refusés</td></tr>
    <tr><th>Transferts hors Bénin</th><td>Aucun en principe ; sous-traitants tenus par contrat le cas échéant</td></tr>
    <tr><th>Mesures de sécurité</th><td>Hachage bcrypt, chiffrement at-rest du téléphone, MFA admin, rate-limit, journalisation</td></tr>
</table>

<h2>Traitement n°2 — Authentification multi-facteurs (MFA)</h2>
<table>
    <tr><th>Finalité</th><td>Renforcer la sécurité des connexions des comptes administrateurs</td></tr>
    <tr><th>Base légale</th><td>Intérêt légitime (sécurité du SI)</td></tr>
    <tr><th>Catégories de personnes</th><td>Administrateurs communaux, super-administrateurs</td></tr>
    <tr><th>Catégories de données</th><td>E-mail, code à 6 chiffres haché, IP, horodatage, nombre de tentatives</td></tr>
    <tr><th>Destinataires</th><td>Aucun — usage interne automatique</td></tr>
    <tr><th>Durée de conservation</th><td>10 minutes (code), 90 jours pour les journaux d'utilisation MFA</td></tr>
    <tr><th>Mesures de sécurité</th><td>Hachage du code, expiration courte, limite à 3 tentatives, rate-limit sur le renvoi</td></tr>
</table>

<h2>Traitement n°3 — Planification et saisie des infrastructures</h2>
<table>
    <tr><th>Finalité</th><td>Collecte et planification des infrastructures communales (eau, assainissement, équipements)</td></tr>
    <tr><th>Base légale</th><td>Mission d'intérêt public</td></tr>
    <tr><th>Catégories de personnes</th><td>Agents enquêteurs (nom de l'enquêteur)</td></tr>
    <tr><th>Catégories de données</th><td>Nom de l'enquêteur, identifiant agent, données techniques d'infrastructure, géolocalisation, photos</td></tr>
    <tr><th>Destinataires</th><td>Agents et administrateurs de la commune, super-administrateurs ADECOB</td></tr>
    <tr><th>Durée de conservation</th><td>Pérenne (intérêt public) ; nom d'enquêteur anonymisable sur demande après 5 ans</td></tr>
    <tr><th>Mesures de sécurité</th><td>Scoping par commune, audit des écritures, sauvegardes chiffrées</td></tr>
</table>

<h2>Traitement n°4 — Formulaire de contact</h2>
<table>
    <tr><th>Finalité</th><td>Répondre aux demandes des utilisateurs et visiteurs</td></tr>
    <tr><th>Base légale</th><td>Consentement</td></tr>
    <tr><th>Catégories de personnes</th><td>Toute personne contactant l'ADECOB</td></tr>
    <tr><th>Catégories de données</th><td>Nom, e-mail, sujet, message, IP, horodatage</td></tr>
    <tr><th>Destinataires</th><td>Service communication ADECOB</td></tr>
    <tr><th>Durée de conservation</th><td>12 mois après dernière correspondance</td></tr>
    <tr><th>Mesures de sécurité</th><td>reCAPTCHA v3, rate-limit, transmission HTTPS</td></tr>
</table>

<h2>Traitement n°5 — Journalisation et audit de sécurité</h2>
<table>
    <tr><th>Finalité</th><td>Traçabilité des accès et actions sensibles, détection d'incidents</td></tr>
    <tr><th>Base légale</th><td>Obligation légale + intérêt légitime (sécurité)</td></tr>
    <tr><th>Catégories de personnes</th><td>Tous les utilisateurs authentifiés</td></tr>
    <tr><th>Catégories de données</th><td>Identifiant utilisateur, action, entité concernée, IP, user-agent, horodatage, valeurs avant/après</td></tr>
    <tr><th>Destinataires</th><td>Super-administrateurs uniquement</td></tr>
    <tr><th>Durée de conservation</th><td>12 mois (extensible à 36 mois sur incident)</td></tr>
    <tr><th>Mesures de sécurité</th><td>Accès restreint, table en lecture seule pour les rôles non-super-admin</td></tr>
</table>

<h2>Traitement n°6 — Réinitialisation de mot de passe</h2>
<table>
    <tr><th>Finalité</th><td>Permettre à l'utilisateur de récupérer l'accès à son compte</td></tr>
    <tr><th>Base légale</th><td>Exécution du service demandé par l'utilisateur</td></tr>
    <tr><th>Catégories de données</th><td>E-mail, jeton de réinitialisation haché, horodatage</td></tr>
    <tr><th>Destinataires</th><td>Aucun (traitement automatique)</td></tr>
    <tr><th>Durée de conservation</th><td>Jeton : 60 minutes ; demande tracée 30 jours</td></tr>
    <tr><th>Mesures de sécurité</th><td>Jeton à usage unique, rate-limit (3/min IP, 5/h e-mail), envoi sur l'e-mail enregistré</td></tr>
</table>

<h3>Sous-traitants</h3>
<table>
    <tr><th>Sous-traitant</th><th>Service</th><th>Garanties</th></tr>
    <tr><td>Hébergeur applicatif</td><td>Hébergement de la plateforme</td><td>Contrat de service incluant clauses de confidentialité et de sécurité</td></tr>
    <tr><td>Fournisseur SMTP</td><td>Envoi des e-mails transactionnels (MFA, reset, notifications)</td><td>TLS imposé, données minimales transmises</td></tr>
    <tr><td>Google reCAPTCHA</td><td>Protection anti-bot des formulaires</td><td>Politique de confidentialité Google ; usage limité au score anti-bot</td></tr>
    <tr><td>OpenStreetMap / Nominatim</td><td>Géolocalisation et cartographie</td><td>Service public, requêtes sans donnée personnelle</td></tr>
</table>

<p class="text-muted small mt-3">Ce registre est mis à jour à chaque évolution significative
de la Plateforme. Toute personne concernée peut en demander communication via le
<a href="{{ route('contact') }}">formulaire de contact</a>.</p>

@endsection
