@extends('legal._layout')

@section('title', 'Politique de confidentialité')
@section('doc_title', 'Politique de confidentialité et protection des données personnelles')
@section('doc_version', '1.1')
@section('doc_date', '30/06/2026')

@section('doc_content')

<div class="alert alert-light border">
    <strong>Résumé.</strong> L'<strong>ADECOB</strong> s'engage à protéger la vie privée de
    toutes les personnes dont elle traite les données dans le cadre de la Plateforme de gestion
    des infrastructures communales du Borgou. La présente politique explique quelles données
    sont collectées, pourquoi, comment elles sont protégées et quels droits vous pouvez exercer,
    conformément à la <em>loi n°2017-20 du 20 avril 2018 portant Code du numérique en République
    du Bénin</em>.
</div>

{{-- ═══════════════════════════════════════════════
     1. PRÉAMBULE
═══════════════════════════════════════════════ --}}
<h2>1. Préambule</h2>

<p>
    La présente Politique de confidentialité (ci-après « la Politique ») s'applique à la
    <strong>Plateforme ADECOB de Gestion des Infrastructures Communales</strong>
    (ci-après « la Plateforme »), accessible à l'adresse
    <strong>https://adecob-infrastructure-plateforme.org</strong>.
</p>
<p>
    Elle est établie conformément au <strong>Livre V (articles 334 à 412) de la loi n°2017-20
    du 20 avril 2018 portant Code du numérique en République du Bénin</strong>, qui régit
    la protection des données à caractère personnel des personnes physiques résidant
    au Bénin et de celles dont les données sont traitées sur le territoire béninois.
</p>
<p>
    Toute utilisation de la Plateforme implique l'acceptation pleine et entière de la présente
    Politique. Les utilisateurs mineurs de 18 ans ne sont pas destinataires de la Plateforme,
    qui est réservée à un usage professionnel par des agents et administrateurs communaux adultes.
</p>

{{-- ═══════════════════════════════════════════════
     2. RESPONSABLE DU TRAITEMENT
═══════════════════════════════════════════════ --}}
<h2>2. Responsable du traitement</h2>

<table>
    <tr><th>Dénomination</th><td>Association pour le Développement des Communes du Borgou (ADECOB)</td></tr>
    <tr><th>Siège social</th><td>Parakou, Département du Borgou, République du Bénin</td></tr>
    <tr><th>Mission</th><td>Planification, suivi et gestion des infrastructures communautaires des 8 communes du Borgou (eau potable, assainissement, éducation, santé, marchés, culture)</td></tr>
    <tr><th>Financement</th><td>DDC Suisse (Direction du Développement et de la Coopération) — Coopération Suisse au Développement, Contrat N°81074308</td></tr>
    <tr><th>Référent protection des données</th><td>Administrateur général de la Plateforme — joignable via le <a href="{{ route('contact.form') }}">formulaire de contact officiel</a></td></tr>
    <tr><th>Contact APDP</th><td>Autorité de Protection des Données Personnelles du Bénin — Cotonou, République du Bénin</td></tr>
</table>

{{-- ═══════════════════════════════════════════════
     3. DONNÉES COLLECTÉES ET TRAITÉES
═══════════════════════════════════════════════ --}}
<h2>3. Données collectées et traitées</h2>

<p>Nous collectons uniquement les données strictement nécessaires aux finalités décrites
à l'article 4 ci-après (principe de minimisation — art. 340 Code du numérique).</p>

<table>
    <tr>
        <th>Catégorie</th>
        <th>Données spécifiques</th>
        <th>Source</th>
        <th>Obligatoire</th>
        <th>Protection technique</th>
    </tr>
    <tr>
        <td><strong>Identification</strong></td>
        <td>Nom, prénom, adresse e-mail, numéro de téléphone</td>
        <td>Formulaire d'inscription</td>
        <td>Oui</td>
        <td>Numéro de téléphone chiffré <em>at-rest</em> (AES-256 via Laravel Encryption)</td>
    </tr>
    <tr>
        <td><strong>Authentification</strong></td>
        <td>Mot de passe (haché <code>bcrypt</code>, jamais lisible), code MFA à 6 chiffres (haché, durée 10 min), jeton de réinitialisation (haché, durée 60 min), cookie de session</td>
        <td>Inscription / connexion / demande de réinitialisation</td>
        <td>Oui</td>
        <td>Hachage irréversible — aucun accès en clair, même pour les administrateurs</td>
    </tr>
    <tr>
        <td><strong>Rattachement organisationnel</strong></td>
        <td>Commune de rattachement, rôle attribué (Agent, Admin Commune, Super Admin)</td>
        <td>Saisie à l'inscription (commune) + attribution par un administrateur (rôle)</td>
        <td>Oui</td>
        <td>Le rôle ne peut être modifié que par un super-administrateur ; toute modification est journalisée</td>
    </tr>
    <tr>
        <td><strong>Données métier — infrastructures</strong></td>
        <td>Nom de l'enquêteur, coordonnées GPS (latitude, longitude, altitude, précision), photos (jusqu'à 4 par fiche), secteur d'activité, type d'infrastructure, nom, année de réalisation, bailleur, matériaux, état de fonctionnement, niveau de dégradation, mode de gestion, défectuosités, mesures proposées, observations</td>
        <td>Formulaire de saisie par les agents collecteurs</td>
        <td>Partiel (GPS et photos facultatifs)</td>
        <td>Accès scopé par commune ; modifications tracées en audit</td>
    </tr>
    <tr>
        <td><strong>Données de planification</strong></td>
        <td>Activités prévues, responsables désignés, personnes associées, source de financement, montant, périodes cibles (2023–2030), statut de maintenance</td>
        <td>Formulaires de planification mairie</td>
        <td>Partiel</td>
        <td>Accès scopé par commune</td>
    </tr>
    <tr>
        <td><strong>Journaux d'audit</strong></td>
        <td>Identifiant utilisateur, nom d'utilisateur, action effectuée, type et identifiant de l'entité concernée, valeurs avant/après modification (JSON), adresse IP, user-agent, méthode HTTP, URL, statut (succès/erreur), horodatage</td>
        <td>Collecte automatique par le système pour toute opération sensible</td>
        <td>Automatique (non modifiable par l'utilisateur)</td>
        <td>Accessible uniquement aux super-administrateurs ; table en lecture seule pour tous les autres rôles</td>
    </tr>
    <tr>
        <td><strong>Cookies</strong></td>
        <td>Cookie de session technique (<code>laravel_session</code>), jeton CSRF (<code>XSRF-TOKEN</code>), cookie de connexion persistante optionnel (<code>remember_web_*</code>)</td>
        <td>Navigation sur la Plateforme</td>
        <td>Cookie de session : oui. Cookie persistant : uniquement si l'utilisateur coche « Se souvenir de moi »</td>
        <td>Attributs <code>HttpOnly</code>, <code>SameSite=Lax</code>, <code>Secure</code> (production). Aucun cookie publicitaire ni de traçage tiers.</td>
    </tr>
</table>

{{-- ═══════════════════════════════════════════════
     4. FINALITÉS ET BASES LÉGALES
═══════════════════════════════════════════════ --}}
<h2>4. Finalités du traitement et bases légales</h2>

<table>
    <tr>
        <th>N°</th>
        <th>Finalité</th>
        <th>Données utilisées</th>
        <th>Base légale (Code du numérique)</th>
        <th>Conservation</th>
    </tr>
    <tr>
        <td>1</td>
        <td>Gestion des comptes utilisateurs : inscription, validation, authentification, gestion du cycle de vie</td>
        <td>Identification, authentification, rattachement</td>
        <td>Consentement (art. 339) + exécution du service demandé (art. 339 al. 2)</td>
        <td>Durée d'activité + 3 ans après dernière connexion</td>
    </tr>
    <tr>
        <td>2</td>
        <td>Sécurisation des connexions administrateurs par authentification multi-facteurs (MFA)</td>
        <td>E-mail, code MFA haché, IP</td>
        <td>Intérêt légitime — sécurité du SI (art. 339 al. 3)</td>
        <td>Code : 10 minutes. Journal MFA : 12 mois</td>
    </tr>
    <tr>
        <td>3</td>
        <td>Collecte, planification et suivi des infrastructures communales du Borgou</td>
        <td>Données métier, photos, GPS, planifications</td>
        <td>Mission d'intérêt public (art. 339 al. 4) — financement DDC Suisse</td>
        <td>Pérenne (intérêt public) ; anonymisation du nom enquêteur sur demande après 5 ans</td>
    </tr>
    <tr>
        <td>4</td>
        <td>Traitement des demandes de contact</td>
        <td>Nom, e-mail, message, IP</td>
        <td>Consentement (art. 339)</td>
        <td>12 mois après dernière correspondance</td>
    </tr>
    <tr>
        <td>5</td>
        <td>Traçabilité des actions et détection des incidents de sécurité</td>
        <td>Journaux d'audit</td>
        <td>Obligation légale + intérêt légitime sécurité (art. 339 al. 3 et 5)</td>
        <td>12 mois actifs ; archives jusqu'à 36 mois</td>
    </tr>
    <tr>
        <td>6</td>
        <td>Réinitialisation du mot de passe</td>
        <td>E-mail, jeton haché</td>
        <td>Exécution du service demandé (art. 339 al. 2)</td>
        <td>60 minutes puis suppression automatique</td>
    </tr>
    <tr>
        <td>7</td>
        <td>Connexion via Google OAuth (optionnelle)</td>
        <td>Adresse e-mail uniquement (correspondance avec un compte existant)</td>
        <td>Consentement (art. 339)</td>
        <td>Non persisté au-delà de la session</td>
    </tr>
</table>

{{-- ═══════════════════════════════════════════════
     5. DESTINATAIRES DES DONNÉES
═══════════════════════════════════════════════ --}}
<h2>5. Destinataires des données</h2>

<p>L'accès aux données est strictement limité selon le rôle de chaque utilisateur
(principe du moindre privilège — art. 352 Code du numérique) :</p>

<table>
    <tr>
        <th>Destinataire</th>
        <th>Données accessibles</th>
        <th>Cadre</th>
    </tr>
    <tr>
        <td><strong>Agent collecteur</strong></td>
        <td>Ses propres fiches d'infrastructure et planifications ; statistiques agrégées de sa commune</td>
        <td>Accès scopé à sa commune uniquement</td>
    </tr>
    <tr>
        <td><strong>Administrateur communal</strong></td>
        <td>Toutes les données de sa commune (infrastructures, planifications, liste des agents de sa commune) ; statut d'approbation des agents de sa commune</td>
        <td>Accès scopé à sa commune uniquement ; liste des agents masque les mots de passe et codes MFA</td>
    </tr>
    <tr>
        <td><strong>Super-administrateur ADECOB</strong></td>
        <td>Toutes les données de toutes les communes ; journaux d'audit complets ; gestion de tous les utilisateurs</td>
        <td>Accès complet avec MFA obligatoire ; toute action journalisée</td>
    </tr>
    <tr>
        <td><strong>Public (non connecté)</strong></td>
        <td>Statistiques agrégées et anonymisées sur les infrastructures publiées — aucune donnée personnelle</td>
        <td>Lecture seule, données dépersonnalisées</td>
    </tr>
    <tr>
        <td><strong>Hébergeur applicatif</strong></td>
        <td>Accès technique aux serveurs et sauvegardes</td>
        <td>Sous-traitant lié par un contrat de traitement de données (DPA) incluant clauses de confidentialité et de sécurité</td>
    </tr>
    <tr>
        <td><strong>Fournisseur SMTP</strong></td>
        <td>Adresse e-mail destinataire et corps du message pour les e-mails transactionnels (MFA, réinitialisation, notifications d'approbation)</td>
        <td>Données minimales transmises ; TLS imposé</td>
    </tr>
</table>

<p class="mt-2"><strong>Aucune donnée n'est revendue, louée, échangée ni transmise à des tiers
à des fins commerciales, publicitaires ou de profilage.</strong></p>

{{-- ═══════════════════════════════════════════════
     6. TRANSFERTS HORS DE LA REPUBLIQUE DU BENIN
═══════════════════════════════════════════════ --}}
<h2>6. Transferts hors de la République du Bénin</h2>

<p>La Plateforme fait appel à des services tiers dont les serveurs peuvent être situés
hors du territoire béninois. Conformément aux articles 398 à 401 du Code du numérique,
des garanties appropriées sont exigées pour tout transfert transfrontalier :</p>

<table>
    <tr>
        <th>Service</th>
        <th>Données transférées</th>
        <th>Localisation</th>
        <th>Garanties</th>
    </tr>
    <tr>
        <td><strong>Hébergeur applicatif</strong></td>
        <td>Toutes les données de la Plateforme</td>
        <td>À préciser selon le contrat d'hébergement en vigueur</td>
        <td>Contrat DPA — clauses de confidentialité, de sécurité et de restitution des données</td>
    </tr>
    <tr>
        <td><strong>Google reCAPTCHA v3</strong></td>
        <td>Score anti-bot (données comportementales anonymisées)</td>
        <td>Serveurs Google (international)</td>
        <td>Politique de confidentialité Google — usage limité à la détection de bots</td>
    </tr>
    <tr>
        <td><strong>Google OAuth 2.0</strong></td>
        <td>Adresse e-mail uniquement (correspondance de compte)</td>
        <td>Serveurs Google (international)</td>
        <td>Politique de confidentialité Google ; aucun token OAuth persisté au-delà de la session</td>
    </tr>
    <tr>
        <td><strong>OpenStreetMap / Nominatim</strong></td>
        <td>Aucune donnée personnelle — requêtes de géolocalisation d'adresses anonymes</td>
        <td>Serveurs OSM (international)</td>
        <td>Service public libre ; politique d'utilisation équitable OSM</td>
    </tr>
    <tr>
        <td><strong>DDC Suisse (bailleur de fonds)</strong></td>
        <td>Rapports agrégés et anonymisés sur l'avancement du programme</td>
        <td>Suisse</td>
        <td>Accord de financement incluant clauses de protection des données</td>
    </tr>
</table>

{{-- ═══════════════════════════════════════════════
     7. DURÉES DE CONSERVATION
═══════════════════════════════════════════════ --}}
<h2>7. Durées de conservation</h2>

<table>
    <tr>
        <th>Donnée</th>
        <th>Durée de conservation</th>
        <th>Sort à l'échéance</th>
    </tr>
    <tr>
        <td>Compte utilisateur actif</td>
        <td>Toute la durée d'utilisation + 3 ans après la dernière connexion</td>
        <td>Anonymisation (nom et prénom remplacés par des pseudonymes, e-mail et téléphone supprimés)</td>
    </tr>
    <tr>
        <td>Compte refusé ou désactivé</td>
        <td>1 an après la décision de refus ou de désactivation</td>
        <td>Anonymisation complète des données personnelles</td>
    </tr>
    <tr>
        <td>Données métier (infrastructures et planifications)</td>
        <td>Conservation pérenne (intérêt public et patrimonial)</td>
        <td>Anonymisation du nom d'enquêteur sur demande motivée après 5 ans</td>
    </tr>
    <tr>
        <td>Journaux d'audit</td>
        <td>12 mois en accès actif ; archivage jusqu'à 36 mois (sur incident)</td>
        <td>Suppression ou anonymisation définitive</td>
    </tr>
    <tr>
        <td>Codes MFA</td>
        <td>10 minutes ou jusqu'à utilisation (la plus courte de ces deux durées)</td>
        <td>Invalidation automatique (champ <code>consumed_at</code> renseigné)</td>
    </tr>
    <tr>
        <td>Jetons de réinitialisation de mot de passe</td>
        <td>60 minutes à compter de l'émission</td>
        <td>Suppression automatique par le système</td>
    </tr>
    <tr>
        <td>Données de contact (formulaire)</td>
        <td>12 mois après la dernière correspondance</td>
        <td>Suppression manuelle par l'administrateur</td>
    </tr>
    <tr>
        <td>Token Google OAuth</td>
        <td>Durée de la session uniquement — non persisté</td>
        <td>Supprimé à la déconnexion ou fermeture de session</td>
    </tr>
</table>

{{-- ═══════════════════════════════════════════════
     8. SÉCURITÉ DES DONNÉES
═══════════════════════════════════════════════ --}}
<h2>8. Sécurité des données</h2>

<p>L'ADECOB met en œuvre des mesures techniques et organisationnelles appropriées
pour protéger vos données contre tout accès non autorisé, toute altération, divulgation
ou destruction. Le détail complet est disponible dans la
<a href="{{ route('legal.pssi') }}">Politique de Sécurité du Système d'Information (PSSI)</a>.</p>

<h3>Principales mesures en vigueur</h3>
<ul>
    <li><strong>Transport chiffré :</strong> HTTPS/TLS forcé en production, HSTS (1 an, includeSubDomains, preload) — toutes les communications sont chiffrées en transit ;</li>
    <li><strong>En-têtes HTTP de sécurité :</strong> <code>X-Frame-Options</code> (anti-clickjacking), <code>X-Content-Type-Options</code>, <code>Referrer-Policy</code>, <code>Permissions-Policy</code>, <code>Content-Security-Policy</code> ;</li>
    <li><strong>Mots de passe :</strong> hachage <code>bcrypt</code> irréversible — jamais stockés ni transmis en clair ;</li>
    <li><strong>Chiffrement <em>at-rest</em> :</strong> numéro de téléphone et codes d'accès commune chiffrés en base de données (AES-256 via la clé <code>APP_KEY</code> applicative) ;</li>
    <li><strong>Authentification multi-facteurs :</strong> MFA par e-mail obligatoire pour tous les comptes administrateurs à chaque ouverture de session ;</li>
    <li><strong>Sessions sécurisées :</strong> cookies <code>HttpOnly</code>, <code>Secure</code>, <code>SameSite=Lax</code> ; identifiant de session régénéré à la connexion ;</li>
    <li><strong>Protection anti-injection :</strong> ORM Eloquent (requêtes paramétrées), validation systématique via <code>FormRequest</code>, échappement Blade activé par défaut ;</li>
    <li><strong>Protection CSRF :</strong> jeton vérifié sur toutes les requêtes mutatives ;</li>
    <li><strong>Rate-limiting :</strong> limitation des tentatives sur la connexion, l'inscription, la réinitialisation, le MFA et le formulaire de contact ;</li>
    <li><strong>Audit complet :</strong> toutes les opérations sensibles sont journalisées avec l'IP, l'horodatage et les valeurs avant/après modification ;</li>
    <li><strong>Sauvegardes :</strong> exports réguliers de la base de données, conservation 30 jours, copie hors site, tests de restauration semestriels.</li>
</ul>

{{-- ═══════════════════════════════════════════════
     9. DROITS DES PERSONNES CONCERNÉES
═══════════════════════════════════════════════ --}}
<h2>9. Droits des personnes concernées</h2>

<p>Conformément aux <strong>articles 392 à 411 du Code du numérique du Bénin</strong>,
vous disposez des droits suivants sur vos données personnelles :</p>

<table>
    <tr>
        <th>Droit</th>
        <th>Ce que vous pouvez demander</th>
        <th>Limites éventuelles</th>
    </tr>
    <tr>
        <td><strong>Droit d'accès</strong> (art. 395)</td>
        <td>Obtenir une copie de l'ensemble de vos données personnelles détenues par l'ADECOB, ainsi que des informations sur les traitements les concernant</td>
        <td>Vérification préalable de votre identité requise</td>
    </tr>
    <tr>
        <td><strong>Droit de rectification</strong> (art. 396)</td>
        <td>Faire corriger toute donnée inexacte ou incomplète vous concernant</td>
        <td>Données attestées par un tiers (ex. : commune assignée par un admin) : modification via la voie hiérarchique</td>
    </tr>
    <tr>
        <td><strong>Droit à l'effacement</strong> (art. 397)</td>
        <td>Demander la suppression de vos données personnelles lorsque le traitement n'est plus justifié</td>
        <td>Inapplicable aux journaux d'audit (obligation légale) et aux données d'infrastructure (intérêt public pérenne) — anonymisation proposée en substitution</td>
    </tr>
    <tr>
        <td><strong>Droit d'opposition</strong> (art. 398)</td>
        <td>Vous opposer à un traitement fondé sur l'intérêt légitime, pour des motifs tenant à votre situation particulière</td>
        <td>L'ADECOB peut refuser si elle démontre des motifs légitimes impérieux prévalant sur vos intérêts</td>
    </tr>
    <tr>
        <td><strong>Droit à la limitation</strong> (art. 399)</td>
        <td>Demander la suspension temporaire d'un traitement, par exemple pendant l'instruction d'une contestation</td>
        <td>Les données sont conservées mais non utilisées pendant la période de limitation</td>
    </tr>
    <tr>
        <td><strong>Droit à la portabilité</strong></td>
        <td>Recevoir vos données dans un format structuré et lisible par machine (JSON / CSV), pour les données fournies sur base du consentement ou d'un contrat</td>
        <td>Concerne principalement vos données d'identification et vos fiches d'infrastructure</td>
    </tr>
    <tr>
        <td><strong>Droit de réclamation auprès de l'APDP</strong></td>
        <td>Introduire une réclamation auprès de l'Autorité de Protection des Données Personnelles du Bénin si vous estimez que vos droits ne sont pas respectés</td>
        <td>Nous vous invitons à nous contacter préalablement pour tenter de résoudre la situation à l'amiable</td>
    </tr>
</table>

{{-- ═══════════════════════════════════════════════
     10. MODALITÉS D'EXERCICE DES DROITS
═══════════════════════════════════════════════ --}}
<h2>10. Comment exercer vos droits ?</h2>

<p>Pour exercer l'un des droits listés ci-dessus :</p>
<ol>
    <li>Adressez votre demande via le <a href="{{ route('contact.form') }}"><strong>formulaire de contact officiel</strong></a> en précisant :
        <ul>
            <li>votre identité complète (nom, prénom, adresse e-mail de votre compte) ;</li>
            <li>le droit que vous souhaitez exercer ;</li>
            <li>tout élément permettant d'identifier les données concernées.</li>
        </ul>
    </li>
    <li>Une vérification de votre identité pourra être requise avant la communication de données sensibles.</li>
    <li>Vous recevrez une réponse dans un délai d'<strong>un (1) mois</strong> à compter de la réception de votre demande complète. Ce délai peut être prolongé de deux mois supplémentaires pour les demandes complexes, avec information préalable.</li>
    <li>L'exercice de vos droits est <strong>gratuit</strong>. En cas de demandes manifestement infondées ou excessives (notamment répétitives), l'ADECOB se réserve le droit de facturer un coût raisonnable ou de refuser de donner suite.</li>
</ol>

<p>Si vous estimez que la réponse apportée est insatisfaisante, vous pouvez introduire une
réclamation auprès de l'<strong>APDP (Autorité de Protection des Données Personnelles du Bénin)</strong>.</p>

{{-- ═══════════════════════════════════════════════
     11. COOKIES ET TRACEURS
═══════════════════════════════════════════════ --}}
<h2>11. Cookies et traceurs</h2>

<p>La Plateforme utilise uniquement des <strong>cookies techniques strictement nécessaires</strong>
à son fonctionnement. Aucun cookie publicitaire, de mesure d'audience, de profilage ou de
traçage tiers n'est déposé.</p>

<table>
    <tr>
        <th>Nom du cookie</th>
        <th>Finalité</th>
        <th>Durée</th>
        <th>Obligatoire</th>
        <th>Tiers</th>
    </tr>
    <tr>
        <td><code>laravel_session</code></td>
        <td>Maintien de la session authentifiée entre les pages</td>
        <td>Fin de session navigateur</td>
        <td>Oui</td>
        <td>Non</td>
    </tr>
    <tr>
        <td><code>XSRF-TOKEN</code></td>
        <td>Protection contre les attaques CSRF (falsification de requête)</td>
        <td>Fin de session navigateur</td>
        <td>Oui</td>
        <td>Non</td>
    </tr>
    <tr>
        <td><code>remember_web_*</code></td>
        <td>Connexion persistante, uniquement si l'utilisateur coche « Se souvenir de moi »</td>
        <td>5 ans (ou jusqu'à déconnexion)</td>
        <td>Non — opt-in explicite</td>
        <td>Non</td>
    </tr>
</table>

<p>Les cookies sont configurés avec les attributs de sécurité <code>HttpOnly</code>
(inaccessibles au JavaScript), <code>SameSite=Lax</code> (protection anti-CSRF)
et <code>Secure</code> en production (transmission HTTPS uniquement).</p>

{{-- ═══════════════════════════════════════════════
     12. PROTECTION DES MINEURS
═══════════════════════════════════════════════ --}}
<h2>12. Protection des mineurs</h2>

<p>La Plateforme est exclusivement destinée à un usage professionnel par des adultes
(agents communaux, administrateurs) dans le cadre de leurs fonctions.
Elle <strong>n'est pas destinée aux personnes âgées de moins de 18 ans</strong> et
ne collecte pas sciemment de données concernant des mineurs.
Toute inscription par un mineur sera invalidée dès qu'elle sera portée à la connaissance
de l'ADECOB.</p>

{{-- ═══════════════════════════════════════════════
     13. VIOLATIONS DE DONNÉES PERSONNELLES
═══════════════════════════════════════════════ --}}
<h2>13. Violations de données personnelles</h2>

<p>En cas de violation de données à caractère personnel (accès non autorisé, fuite, destruction,
altération accidentelle ou illicite), l'ADECOB applique la procédure suivante :</p>

<ol>
    <li><strong>Détection et qualification</strong> — l'incident est identifié et évalué dans les plus brefs délais (objectif : sous 24 heures) ;</li>
    <li><strong>Notification à l'APDP</strong> — conformément à l'<strong>article 391 du Code du numérique</strong>, l'APDP est notifiée dans un délai de <strong>72 heures</strong> suivant la prise de connaissance de la violation, sauf si la violation est peu susceptible d'engendrer un risque pour les personnes ;</li>
    <li><strong>Notification aux personnes concernées</strong> — si la violation est susceptible d'engendrer un risque élevé pour leurs droits et libertés, les personnes concernées sont informées dans les meilleurs délais ;</li>
    <li><strong>Remédiation</strong> — mesures correctives déployées, secrets compromis renouvelés ;</li>
    <li><strong>Documentation</strong> — l'incident est documenté conformément à l'article 391 al. 5 du Code du numérique.</li>
</ol>

<p>Pour signaler une violation ou une vulnérabilité suspectée, utilisez le
<a href="{{ route('contact.form') }}">formulaire de contact</a> en mentionnant
<strong>[INCIDENT SÉCURITÉ]</strong> dans le sujet.</p>

{{-- ═══════════════════════════════════════════════
     14. MODIFICATIONS DE LA POLITIQUE
═══════════════════════════════════════════════ --}}
<h2>14. Modifications de la présente politique</h2>

<p>L'ADECOB peut mettre à jour la présente Politique pour refléter les évolutions légales,
techniques ou organisationnelles. En cas de modification substantielle :</p>
<ul>
    <li>la date de mise à jour et le numéro de version figurant en tête du document sont actualisés ;</li>
    <li>les utilisateurs disposant d'un compte sont informés par une notification affichée
        lors de leur prochaine connexion ;</li>
    <li>la version antérieure reste disponible sur demande via le
        <a href="{{ route('contact.form') }}">formulaire de contact</a>.</li>
</ul>
<p>La poursuite de l'utilisation de la Plateforme après notification vaut acceptation
de la politique mise à jour.</p>

{{-- ═══════════════════════════════════════════════
     15. TEXTES DE RÉFÉRENCE
═══════════════════════════════════════════════ --}}
<h2>15. Textes de référence applicables</h2>

<ul>
    <li><strong>Loi n°2017-20 du 20 avril 2018</strong> portant Code du numérique en République du Bénin — Livre V (Protection des données à caractère personnel, articles 334 à 412) ;</li>
    <li><strong>Décrets d'application</strong> du Code du numérique en vigueur au Bénin ;</li>
    <li><strong>Délibérations de l'APDP</strong> (Autorité de Protection des Données Personnelles du Bénin) applicables ;</li>
    <li><strong>Loi n°2017-20, Livre VI</strong> — Cybercriminalité (articles 549 et suivants), pour les obligations relatives à la sécurité des systèmes ;</li>
    <li><strong>ISO/IEC 27001:2022</strong> — Référentiel international de management de la sécurité de l'information (référence technique) ;</li>
    <li><strong>OWASP Top 10 2021</strong> — Référentiel des principales vulnérabilités des applications web (référence sécurité) ;</li>
    <li><strong>Accord de financement DDC Suisse n°81074308</strong> — Clauses de protection des données applicables.</li>
</ul>

<div class="alert alert-light border mt-4">
    <strong>Documents complémentaires :</strong>
    <a href="{{ route('legal.pssi') }}">Politique de Sécurité du SI (PSSI)</a> ·
    <a href="{{ route('legal.cgu') }}">Conditions Générales d'Utilisation</a> ·
    <a href="{{ route('legal.registre') }}">Registre des traitements de données</a>
</div>

@endsection
