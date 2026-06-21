@extends('legal._layout')

@section('title', 'Politique de confidentialité')
@section('doc_title', 'Politique de confidentialité')
@section('doc_version', '1.0')
@section('doc_date', '21/06/2026')

@section('doc_content')

<div class="alert alert-light border">
    La présente politique décrit comment l'<strong>ADECOB</strong> collecte, utilise et protège
    les données à caractère personnel des utilisateurs de la plateforme
    « ADECOB Infrastructure Plannification », conformément à la
    <em>loi n°2017-20 du 20 avril 2018 portant Code du numérique en République du Bénin</em>
    (Livre V — Protection des données à caractère personnel).
</div>

<h2>1. Responsable du traitement</h2>
<p><strong>Association pour le Développement des Communes du Borgou (ADECOB)</strong><br>
Siège : Parakou, République du Bénin<br>
Contact : <a href="{{ route('contact') }}">Formulaire de contact</a></p>

<h2>2. Données collectées</h2>
<table>
    <tr><th>Catégorie</th><th>Données</th><th>Source</th></tr>
    <tr><td>Identification</td><td>nom, prénom, e-mail, téléphone</td><td>Formulaire d'inscription</td></tr>
    <tr><td>Authentification</td><td>mot de passe (haché), code MFA (haché et temporaire)</td><td>Inscription / connexion</td></tr>
    <tr><td>Rattachement</td><td>commune, rôle</td><td>Validation administrateur</td></tr>
    <tr><td>Données métier</td><td>infrastructures saisies, planifications, photos</td><td>Saisie par l'agent</td></tr>
    <tr><td>Journaux techniques</td><td>adresse IP, date/heure de connexion, action effectuée</td><td>Automatique (audit)</td></tr>
    <tr><td>Cookies</td><td>cookie de session (technique, obligatoire)</td><td>Navigation</td></tr>
</table>

<h2>3. Finalités du traitement</h2>
<ul>
    <li>Gestion des comptes utilisateurs et authentification ;</li>
    <li>Planification et suivi des infrastructures communales ;</li>
    <li>Production de rapports agrégés à destination des communes et de l'ADECOB ;</li>
    <li>Sécurité de la plateforme (détection d'intrusion, traçabilité, audit) ;</li>
    <li>Réponse aux demandes formulées via le formulaire de contact.</li>
</ul>

<h2>4. Base légale</h2>
<ul>
    <li><strong>Exécution d'une mission d'intérêt public</strong> pour les données métier
        (planification communale) ;</li>
    <li><strong>Consentement</strong> de la personne lors de l'inscription, pour les données
        d'identification ;</li>
    <li><strong>Obligation légale et intérêt légitime</strong> pour la journalisation
        des accès (sécurité).</li>
</ul>

<h2>5. Destinataires</h2>
<p>Les données sont accessibles, dans la stricte limite de leur besoin :</p>
<ul>
    <li>aux <strong>agents et administrateurs de la commune</strong> de rattachement ;</li>
    <li>aux <strong>super-administrateurs ADECOB</strong> pour l'administration et la sécurité ;</li>
    <li>à l'<strong>hébergeur technique</strong> (sous-traitant tenu par contrat à la confidentialité).</li>
</ul>
<p>Aucune donnée n'est revendue, échangée ou transférée à des tiers à des fins commerciales.</p>

<h2>6. Transferts hors du Bénin</h2>
<p>L'hébergement principal est assuré sur des infrastructures conformes aux exigences
du Code du numérique. En cas de recours à un sous-traitant situé hors du Bénin,
des garanties contractuelles équivalentes sont exigées (clauses types de confidentialité
et de sécurité).</p>

<h2>7. Durées de conservation</h2>
<table>
    <tr><th>Donnée</th><th>Durée</th></tr>
    <tr><td>Compte utilisateur actif</td><td>Toute la durée d'utilisation + 3 ans après dernière connexion</td></tr>
    <tr><td>Compte refusé / désactivé</td><td>1 an puis anonymisation</td></tr>
    <tr><td>Données métier (infrastructures)</td><td>Conservation pérenne (intérêt public)</td></tr>
    <tr><td>Journaux d'audit</td><td>12 mois minimum, 36 mois maximum</td></tr>
    <tr><td>Codes MFA</td><td>10 minutes (purgés après usage ou expiration)</td></tr>
    <tr><td>Jetons de réinitialisation de mot de passe</td><td>60 minutes</td></tr>
</table>

<h2>8. Sécurité</h2>
<p>Les données sont protégées par : HTTPS forcé, en-têtes HTTP de sécurité,
hachage des mots de passe (bcrypt), chiffrement <em>at-rest</em> des champs sensibles
(téléphone, codes d'accès), authentification multi-facteurs pour les comptes administrateurs,
<em>rate-limiting</em> et journalisation des accès. Voir la
<a href="{{ route('legal.pssi') }}">PSSI</a> pour le détail.</p>

<h2>9. Droits des personnes concernées</h2>
<p>Conformément aux articles 392 et suivants du Code du numérique, vous disposez :</p>
<ul>
    <li>d'un droit d'<strong>accès</strong> à vos données ;</li>
    <li>d'un droit de <strong>rectification</strong> des données inexactes ;</li>
    <li>d'un droit à l'<strong>effacement</strong> (sous réserve des obligations légales) ;</li>
    <li>d'un droit d'<strong>opposition</strong> pour motif légitime ;</li>
    <li>d'un droit à la <strong>limitation</strong> du traitement ;</li>
    <li>du droit d'introduire une <strong>réclamation auprès de l'APDP</strong>
        (Autorité de Protection des Données Personnelles du Bénin).</li>
</ul>
<p>Pour exercer ces droits, contactez-nous via le
<a href="{{ route('contact') }}">formulaire de contact</a> ; une réponse vous sera apportée
dans un délai d'<strong>un mois</strong>.</p>

<h2>10. Cookies</h2>
<p>La plateforme n'utilise qu'un <strong>cookie de session technique strictement nécessaire</strong>
au fonctionnement (maintien de la connexion). Aucun cookie publicitaire ni de mesure d'audience
tierce n'est déposé.</p>

<h2>11. Modifications</h2>
<p>La présente politique peut être mise à jour. La date de dernière mise à jour
figure en tête du document. Les modifications substantielles sont notifiées
aux utilisateurs lors de leur prochaine connexion.</p>

@endsection
