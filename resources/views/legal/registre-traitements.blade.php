@extends('legal._layout')

@section('title', 'Registre des traitements de données')
@section('doc_title', 'Registre des traitements de données à caractère personnel')
@section('doc_version', '1.1')
@section('doc_date', '30/06/2026')

@section('doc_content')

<div class="alert alert-warning border">
    <strong>Document à usage réglementaire.</strong> Ce registre est tenu en application des
    <strong>articles 412 et suivants de la loi n°2017-20 portant Code du numérique en République
    du Bénin</strong>. Il recense l'ensemble des traitements de données à caractère personnel mis
    en œuvre par l'ADECOB dans le cadre de l'exploitation de la Plateforme de gestion des
    infrastructures communales du Borgou. Il doit être déclaré à l'<strong>Autorité de Protection
    des Données Personnelles (APDP)</strong> et tenu à la disposition de toute autorité de
    contrôle compétente.
</div>

{{-- ============================================================ --}}
{{-- SECTION 0 : IDENTITÉ DU RESPONSABLE ET PÉRIMÈTRE            --}}
{{-- ============================================================ --}}

<h2>Identité du responsable du traitement et périmètre</h2>

<table>
    <tr>
        <th style="width:35%">Dénomination</th>
        <td><strong>ADECOB</strong> — Association pour le Développement des Communes du Borgou</td>
    </tr>
    <tr>
        <th>Siège social</th>
        <td>Parakou, République du Bénin</td>
    </tr>
    <tr>
        <th>Statut juridique</th>
        <td>Association à but non lucratif, à vocation d'intérêt public</td>
    </tr>
    <tr>
        <th>Financeur principal</th>
        <td>DDC Suisse — Direction du Développement et de la Coopération (Berne, Suisse)</td>
    </tr>
    <tr>
        <th>Contact du responsable de traitement</th>
        <td><a href="{{ route('contact.form') }}">Formulaire de contact de la Plateforme</a></td>
    </tr>
    <tr>
        <th>Référent Protection des Données (RSSI)</th>
        <td>Responsable Sécurité du Système d'Information désigné par l'ADECOB —
            joignable via <a href="{{ route('contact.form') }}">le formulaire de contact</a></td>
    </tr>
    <tr>
        <th>Périmètre de la Plateforme</th>
        <td>Gestion géo-référencée de <strong>10 402 infrastructures communales</strong>
            (eau potable, assainissement, voirie, équipements collectifs) réparties sur les
            <strong>8 communes du département du Borgou</strong> :
            Parakou, N'Dali, Pèrèrè, Nikki, Kalalé, Bembèrèkè, Sinendé, Tchaourou</td>
    </tr>
    <tr>
        <th>Technologies utilisées</th>
        <td>Laravel 10 (PHP 8.x), MariaDB, OpenStreetMap / Leaflet, Google OAuth 2.0,
            Google reCAPTCHA v3</td>
    </tr>
    <tr>
        <th>Date de création du registre</th>
        <td>21/06/2026</td>
    </tr>
    <tr>
        <th>Dernière mise à jour</th>
        <td>30/06/2026 — version 1.1</td>
    </tr>
    <tr>
        <th>Statut de déclaration APDP</th>
        <td>Déclaration en cours auprès de l'<strong>Autorité de Protection des Données
            Personnelles du Bénin (APDP)</strong> — cf. section dédiée en fin de document</td>
    </tr>
</table>

{{-- ============================================================ --}}
{{-- TRAITEMENT N°1 — GESTION DES COMPTES ET AUTHENTIFICATION    --}}
{{-- ============================================================ --}}

<h2>Traitement n°1 — Gestion des comptes et authentification</h2>

<table>
    <tr>
        <th style="width:35%">Responsable du traitement</th>
        <td>ADECOB — Parakou, Bénin</td>
    </tr>
    <tr>
        <th>Finalité(s) précise(s)</th>
        <td>
            <ul class="mb-0">
                <li>Création et gestion du cycle de vie des comptes utilisateurs (inscription, validation, activation, suspension, suppression)</li>
                <li>Authentification des agents communaux, administrateurs et super-administrateurs à la Plateforme</li>
                <li>Gestion des rôles et habilitations par commune (contrôle d'accès basé sur les rôles — RBAC)</li>
                <li>Envoi des notifications de compte (confirmation d'inscription, validation, refus, suspension)</li>
                <li>Traçabilité des connexions réussies et échouées à des fins de sécurité</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Base légale</th>
        <td>Mission d'intérêt public (gestion du patrimoine communal) et consentement de la personne
            concernée lors de l'inscription
            <em>(art. 386 loi n°2017-20 portant Code du numérique)</em></td>
    </tr>
    <tr>
        <th>Catégories de personnes concernées</th>
        <td>
            <ul class="mb-0">
                <li>Agents enquêteurs de terrain (rôle <code>agent_enquêteur</code>)</li>
                <li>Agents de mairie (rôle <code>agent_mairie</code>)</li>
                <li>Administrateurs communaux (rôle <code>admin_commune</code>)</li>
                <li>Super-administrateurs ADECOB (rôle <code>super_admin</code>)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Catégories de données à caractère personnel</th>
        <td>
            <ul class="mb-0">
                <li><strong>Identité :</strong> nom, prénom</li>
                <li><strong>Contact :</strong> adresse e-mail professionnelle, numéro de téléphone (chiffré AES-256 en base de données)</li>
                <li><strong>Authentification :</strong> mot de passe haché (bcrypt, facteur de coût 12), jeton de session <code>remember_token</code></li>
                <li><strong>Organisation :</strong> rôle (enum), commune rattachée (<code>commune_id</code>)</li>
                <li><strong>Statut :</strong> actif / inactif / en attente / refusé, <code>email_verified_at</code></li>
                <li><strong>Métadonnées :</strong> date de création du compte (<code>created_at</code>), date de dernière connexion (<code>last_login_at</code>)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Source des données</th>
        <td>Déclaration directe de l'utilisateur via le formulaire d'inscription en ligne ;
            validation manuelle par l'administrateur communal compétent avant activation</td>
    </tr>
    <tr>
        <th>Destinataires internes</th>
        <td>
            <ul class="mb-0">
                <li>Super-administrateurs ADECOB (accès complet en lecture sur toutes les communes)</li>
                <li>Administrateurs de la commune concernée (accès aux comptes de leur commune uniquement)</li>
                <li>Composant d'authentification Laravel (Guard <code>web</code>)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Destinataires externes / sous-traitants</th>
        <td>Fournisseur SMTP (acheminement des e-mails de confirmation, de notification et d'alerte) —
            données transmises : adresse e-mail destinataire et corps du message uniquement</td>
    </tr>
    <tr>
        <th>Transferts hors Bénin</th>
        <td><strong>Potentiellement oui</strong> via le fournisseur SMTP (selon localisation des serveurs).
            Garanties : clause de confidentialité contractuelle, transmission chiffrée TLS 1.2+.
            Aucun transfert de mot de passe ou de numéro de téléphone hors de la base de données
            hébergée.</td>
    </tr>
    <tr>
        <th>Durée de conservation</th>
        <td>
            <ul class="mb-0">
                <li><strong>Comptes actifs :</strong> durée d'activité + 3 ans après la dernière connexion</li>
                <li><strong>Comptes en attente de validation :</strong> 6 mois, puis suppression automatique si non validés</li>
                <li><strong>Comptes refusés :</strong> anonymisation au bout de 12 mois</li>
                <li><strong>Comptes supprimés à la demande :</strong> suppression effective sous 30 jours
                    (hors données de journalisation conservées selon le Traitement n°7)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Mesures de sécurité techniques et organisationnelles</th>
        <td>
            <ul class="mb-0">
                <li>Hachage bcrypt des mots de passe (facteur de coût 12)</li>
                <li>Chiffrement AES-256 du numéro de téléphone en base de données</li>
                <li>Authentification multi-facteurs obligatoire pour <code>admin_commune</code> et <code>super_admin</code> (voir Traitement n°2)</li>
                <li>Rate-limiting sur les routes de connexion (5 tentatives / 10 min par IP)</li>
                <li>Journalisation de toutes les connexions réussies et échouées (voir Traitement n°7)</li>
                <li>Validation de l'adresse e-mail obligatoire avant activation du compte</li>
                <li>Accès aux profils utilisateurs restreint par rôle et par commune (scoping MariaDB)</li>
                <li>HTTPS obligatoire sur l'ensemble de la Plateforme (HSTS activé)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Droit d'opposition / opt-out</th>
        <td>La personne peut demander la suppression ou la rectification de ses données via
            <a href="{{ route('contact.form') }}">le formulaire de contact</a>. La suppression du compte
            entraîne la perte immédiate d'accès à la Plateforme. L'opposition au traitement est
            incompatible avec la détention d'un compte actif (traitement nécessaire à l'accès
            au service).</td>
    </tr>
</table>

{{-- ============================================================ --}}
{{-- TRAITEMENT N°2 — AUTHENTIFICATION MULTI-FACTEURS (MFA)      --}}
{{-- ============================================================ --}}

<h2>Traitement n°2 — Authentification multi-facteurs (MFA)</h2>

<table>
    <tr>
        <th style="width:35%">Responsable du traitement</th>
        <td>ADECOB — Parakou, Bénin</td>
    </tr>
    <tr>
        <th>Finalité(s) précise(s)</th>
        <td>
            <ul class="mb-0">
                <li>Renforcement de la sécurité des connexions pour les comptes à privilèges élevés</li>
                <li>Génération, envoi par e-mail et vérification d'un code OTP (One-Time Password) à 6 chiffres</li>
                <li>Limitation du risque de compromission de compte par vol ou divulgation de mot de passe</li>
                <li>Journalisation des tentatives MFA (réussies et échouées) à des fins d'audit de sécurité</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Base légale</th>
        <td>Intérêt légitime du responsable de traitement — protection de la sécurité du système
            d'information et intégrité des données du patrimoine communal
            <em>(art. 386 loi n°2017-20)</em></td>
    </tr>
    <tr>
        <th>Catégories de personnes concernées</th>
        <td>Administrateurs communaux (<code>admin_commune</code>) et super-administrateurs
            (<code>super_admin</code>) uniquement — la MFA n'est pas imposée aux rôles
            agents pour ne pas compromettre l'accessibilité terrain</td>
    </tr>
    <tr>
        <th>Catégories de données à caractère personnel</th>
        <td>
            <ul class="mb-0">
                <li><strong>Identifiant :</strong> adresse e-mail du compte (pour l'envoi du code OTP)</li>
                <li><strong>Code OTP :</strong> code à 6 chiffres stocké sous forme hachée (SHA-256) en base de données — jamais en clair</li>
                <li><strong>Session :</strong> adresse IP de la tentative de connexion</li>
                <li><strong>Horodatage :</strong> date/heure de génération (<code>created_at</code>), date d'expiration calculée (<code>expires_at</code>)</li>
                <li><strong>Compteur :</strong> nombre de tentatives de saisie infructueuses consécutives</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Source des données</th>
        <td>Code généré automatiquement par le système lors de la phase de connexion ; adresse IP
            collectée côté serveur ; adresse e-mail issue du compte utilisateur</td>
    </tr>
    <tr>
        <th>Destinataires internes</th>
        <td>Composant MFA interne de la Plateforme (traitement entièrement automatisé) —
            aucun accès humain direct en dehors des super-administrateurs en cas
            d'investigation de sécurité</td>
    </tr>
    <tr>
        <th>Destinataires externes / sous-traitants</th>
        <td>Fournisseur SMTP — le code OTP en clair (avant hachage) est transmis dans le corps
            de l'e-mail destiné à l'utilisateur ; seule l'adresse e-mail du destinataire et
            le contenu du message sont communiqués au service d'envoi</td>
    </tr>
    <tr>
        <th>Transferts hors Bénin</th>
        <td><strong>Potentiellement oui</strong> via le fournisseur SMTP. Garanties : transmission
            TLS 1.2+, code à durée de vie très courte (10 min), aucune donnée d'identité
            nominative transmise hormis l'adresse e-mail.</td>
    </tr>
    <tr>
        <th>Durée de conservation</th>
        <td>
            <ul class="mb-0">
                <li><strong>Code OTP en base :</strong> suppression automatique à expiration (10 minutes) ou immédiatement après validation réussie</li>
                <li><strong>Journaux de tentatives MFA :</strong> 90 jours glissants</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Mesures de sécurité techniques et organisationnelles</th>
        <td>
            <ul class="mb-0">
                <li>Code OTP stocké sous forme hachée (SHA-256) — jamais persisté en clair en base de données</li>
                <li>Expiration automatique stricte à 10 minutes</li>
                <li>Blocage du compte après 3 tentatives de saisie incorrectes consécutives</li>
                <li>Rate-limit sur le renvoi de nouveaux codes (1 renvoi maximum par minute par utilisateur)</li>
                <li>Usage unique : le code est invalidé immédiatement après une validation réussie</li>
                <li>Transmission de l'e-mail via TLS</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Droit d'opposition / opt-out</th>
        <td>Traitement non opposable : la MFA est une obligation de sécurité non négociable pour
            les comptes à privilèges. Il n'est pas possible de s'y soustraire sans changement
            de rôle vers un profil non soumis à la MFA.</td>
    </tr>
</table>

{{-- ============================================================ --}}
{{-- TRAITEMENT N°3 — COLLECTE ET GESTION DES INFRASTRUCTURES    --}}
{{-- ============================================================ --}}

<h2>Traitement n°3 — Collecte et gestion des infrastructures communales</h2>

<table>
    <tr>
        <th style="width:35%">Responsable du traitement</th>
        <td>ADECOB — Parakou, Bénin</td>
    </tr>
    <tr>
        <th>Finalité(s) précise(s)</th>
        <td>
            <ul class="mb-0">
                <li>Recensement et géo-référencement des infrastructures communales (eau potable, assainissement, voirie, équipements collectifs, marchés, centres de santé, écoles) dans les 8 communes du Borgou</li>
                <li>Constitution d'une base de données de référence pour la planification du développement communal et l'allocation des ressources</li>
                <li>Suivi longitudinal de l'état et de la qualité des infrastructures dans le temps</li>
                <li>Traçabilité des campagnes de collecte de terrain (agent enquêteur, date, localisation)</li>
                <li>Production de rapports, indicateurs et cartographies à destination des mairies et du bailleur DDC Suisse</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Base légale</th>
        <td>Mission d'intérêt public — gestion et valorisation du patrimoine communal dans le cadre
            du programme de développement territorial financé par DDC Suisse
            <em>(art. 386 loi n°2017-20)</em></td>
    </tr>
    <tr>
        <th>Catégories de personnes concernées</th>
        <td>
            <ul class="mb-0">
                <li>Agents enquêteurs de terrain — leur nom figure comme attribut de la fiche d'infrastructure au titre de la traçabilité de la collecte</li>
                <li>Agents de saisie et administrateurs des communes (en tant qu'auteurs de modifications ultérieures)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Catégories de données à caractère personnel</th>
        <td>
            <ul class="mb-0">
                <li><strong>Identifiant enquêteur :</strong> nom complet de l'agent ayant réalisé le relevé (<code>enquêteur_nom</code>), identifiant utilisateur (<code>user_id</code>)</li>
                <li><strong>Localisation :</strong> coordonnées GPS de l'infrastructure (latitude / longitude) — données de localisation de l'ouvrage, non de l'enquêteur</li>
                <li><strong>Photos :</strong> photographies de l'infrastructure pouvant indirectement inclure des personnes présentes lors de la prise de vue</li>
                <li><strong>Données techniques :</strong> secteur, arrondissement, village / quartier, type d'infrastructure, sous-type, état général (enum), qualité (enum), observations textuelles libres</li>
                <li><strong>Métadonnées :</strong> date de collecte (<code>date_collecte</code>), date de dernière modification (<code>updated_at</code>), identifiant de l'agent modificateur</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Source des données</th>
        <td>Saisie directe sur la Plateforme par les agents enquêteurs lors des campagnes de terrain ;
            import possible par fichier CSV après validation par un administrateur communal</td>
    </tr>
    <tr>
        <th>Destinataires internes</th>
        <td>
            <ul class="mb-0">
                <li>Agents et administrateurs de la commune concernée (accès scopé par commune)</li>
                <li>Super-administrateurs ADECOB (accès toutes communes)</li>
                <li>Équipe technique ADECOB pour la production de rapports et de cartographies</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Destinataires externes / sous-traitants</th>
        <td>
            <ul class="mb-0">
                <li>DDC Suisse (bailleur) : accès aux rapports agrégés et cartographies — aucune donnée nominative individuelle sur les enquêteurs</li>
                <li>Mairies des 8 communes : accès à leurs propres données via la Plateforme</li>
                <li>OpenStreetMap / Nominatim (géocodage) : coordonnées GPS ou noms de lieux uniquement, sans données nominatives</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Transferts hors Bénin</th>
        <td><strong>Oui — limité</strong> : les requêtes de géocodage vers Nominatim (serveurs
            OSMF en Europe) ne contiennent que des coordonnées GPS ou des noms de lieux, sans
            donnée nominative. Les rapports transmis à DDC Suisse sont agrégés et ne permettent
            pas l'identification d'enquêteurs individuels. Garanties : politique d'utilisation OSMF,
            accord de coopération DDC/ADECOB.</td>
    </tr>
    <tr>
        <th>Durée de conservation</th>
        <td>
            <ul class="mb-0">
                <li><strong>Fiches d'infrastructure :</strong> conservation pérenne (intérêt public patrimonial)</li>
                <li><strong>Nom de l'enquêteur :</strong> anonymisable sur demande après 5 ans révolus depuis la date de collecte</li>
                <li><strong>Photos :</strong> conservation pérenne, sauf décision de suppression validée par un administrateur communal</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Mesures de sécurité techniques et organisationnelles</th>
        <td>
            <ul class="mb-0">
                <li>Scoping strict par commune : les agents n'accèdent qu'aux fiches de leur commune</li>
                <li>Contrôle d'accès RBAC : seuls les rôles autorisés peuvent créer, modifier ou supprimer des fiches</li>
                <li>Journalisation de toutes les opérations CRUD sur les fiches (voir Traitement n°7)</li>
                <li>Sauvegardes quotidiennes chiffrées de la base de données</li>
                <li>Photos stockées dans un répertoire non accessible publiquement, servies via des routes authentifiées</li>
                <li>Validation côté serveur de l'ensemble des champs de saisie</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Droit d'opposition / opt-out</th>
        <td>Le nom de l'enquêteur peut être anonymisé sur demande écrite adressée via
            <a href="{{ route('contact.form') }}">le formulaire de contact</a>, après vérification
            de l'identité du demandeur et sous réserve que la fiche ait plus de 5 ans.
            L'intégralité des données techniques de l'infrastructure est conservée pour
            des raisons d'intérêt public patrimonial.</td>
    </tr>
</table>

{{-- ============================================================ --}}
{{-- TRAITEMENT N°4 — PLANIFICATION DES TRAVAUX DE MAINTENANCE   --}}
{{-- ============================================================ --}}

<h2>Traitement n°4 — Planification des travaux de maintenance</h2>

<table>
    <tr>
        <th style="width:35%">Responsable du traitement</th>
        <td>ADECOB — Parakou, Bénin</td>
    </tr>
    <tr>
        <th>Finalité(s) précise(s)</th>
        <td>
            <ul class="mb-0">
                <li>Planification, suivi et historisation des travaux de maintenance et de réhabilitation des infrastructures communales</li>
                <li>Gestion des coûts prévisionnels et réels par opération de maintenance</li>
                <li>Identification des prestataires et des agents responsables de la validation des travaux</li>
                <li>Suivi de l'évolution de l'état des infrastructures avant et après intervention</li>
                <li>Alimentation des indicateurs de performance et de planification budgétaire communale</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Base légale</th>
        <td>Mission d'intérêt public — entretien et gestion du patrimoine communal
            <em>(art. 386 loi n°2017-20)</em></td>
    </tr>
    <tr>
        <th>Catégories de personnes concernées</th>
        <td>
            <ul class="mb-0">
                <li>Agents de mairie et responsables techniques ayant enregistré ou validé des travaux dans la Plateforme</li>
                <li>Prestataires ou artisans identifiés nominativement dans les fiches de travaux (les petits prestataires peuvent être des personnes physiques)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Catégories de données à caractère personnel</th>
        <td>
            <ul class="mb-0">
                <li><strong>Responsable :</strong> nom et identifiant de l'agent ayant planifié ou validé les travaux (<code>user_id</code>, <code>agent_nom</code>)</li>
                <li><strong>Prestataire :</strong> nom du prestataire ou de l'artisan (peut être une personne physique pour les petits travaux)</li>
                <li><strong>Travaux :</strong> type de travaux, description textuelle, date planifiée, date de réalisation effective</li>
                <li><strong>Finances :</strong> coût estimé, coût réel (montants en FCFA)</li>
                <li><strong>État :</strong> état de l'infrastructure avant intervention (enum), état après intervention (enum)</li>
                <li><strong>Métadonnées :</strong> identifiant de l'infrastructure concernée, commune, date de saisie (<code>created_at</code>)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Source des données</th>
        <td>Saisie par les agents de mairie ou administrateurs communaux habilités, via l'interface
            de gestion des travaux de la Plateforme</td>
    </tr>
    <tr>
        <th>Destinataires internes</th>
        <td>
            <ul class="mb-0">
                <li>Agents de mairie et administrateurs de la commune concernée</li>
                <li>Super-administrateurs ADECOB</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Destinataires externes / sous-traitants</th>
        <td>DDC Suisse : rapports agrégés sur les travaux réalisés (totaux, taux de réalisation,
            montants consolidés) sans données nominatives sur les prestataires personnes physiques</td>
    </tr>
    <tr>
        <th>Transferts hors Bénin</th>
        <td><strong>Non</strong> pour les données nominatives. Les rapports transmis à DDC Suisse
            (Berne, Suisse) sont agrégés et ne contiennent pas de noms de personnes physiques.</td>
    </tr>
    <tr>
        <th>Durée de conservation</th>
        <td>
            <ul class="mb-0">
                <li><strong>Fiches de travaux :</strong> 10 ans à compter de la date de réalisation (obligations de traçabilité patrimoniale et comptable)</li>
                <li><strong>Données de prestataires personnes physiques :</strong> anonymisation après 5 ans si le prestataire n'est plus actif sur la Plateforme</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Mesures de sécurité techniques et organisationnelles</th>
        <td>
            <ul class="mb-0">
                <li>Accès restreint aux agents habilités de la commune concernée et aux super-administrateurs</li>
                <li>Journalisation de toutes les créations et modifications (voir Traitement n°7)</li>
                <li>Sauvegardes quotidiennes chiffrées</li>
                <li>Validation des montants et des dates par contraintes de base de données et règles métier côté serveur</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Droit d'opposition / opt-out</th>
        <td>Un prestataire personne physique peut demander l'anonymisation de son nom dans
            les fiches de travaux de plus de 5 ans, via
            <a href="{{ route('contact.form') }}">le formulaire de contact</a>, sous réserve
            des obligations de traçabilité patrimoniale.</td>
    </tr>
</table>

{{-- ============================================================ --}}
{{-- TRAITEMENT N°5 — DONNÉES DE PLANIFICATION MAIRIE            --}}
{{-- ============================================================ --}}

<h2>Traitement n°5 — Données de planification mairie (MairieAgentData)</h2>

<table>
    <tr>
        <th style="width:35%">Responsable du traitement</th>
        <td>ADECOB — Parakou, Bénin</td>
    </tr>
    <tr>
        <th>Finalité(s) précise(s)</th>
        <td>
            <ul class="mb-0">
                <li>Centralisation des données de planification budgétaire et infrastructurelle des 8 communes du Borgou pour les périodes 2023–2030</li>
                <li>Suivi des priorités d'investissement et des sources de financement par commune et par secteur</li>
                <li>Identification des agents responsables de la planification et de la validation des projets communaux</li>
                <li>Production de tableaux de bord et de rapports de suivi à destination de l'ADECOB et de DDC Suisse</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Base légale</th>
        <td>Mission d'intérêt public — planification du développement communal dans le cadre
            du programme de coopération DDC Suisse
            <em>(art. 386 loi n°2017-20)</em></td>
    </tr>
    <tr>
        <th>Catégories de personnes concernées</th>
        <td>Agents de mairie responsables de la planification, responsables techniques et
            administratifs des 8 communes désignés nominativement dans les fiches de planification</td>
    </tr>
    <tr>
        <th>Catégories de données à caractère personnel</th>
        <td>
            <ul class="mb-0">
                <li><strong>Identité du responsable :</strong> nom et prénom de l'agent de mairie ou du responsable technique désigné dans la fiche</li>
                <li><strong>Organisation :</strong> commune, service, fonction exercée</li>
                <li><strong>Planification :</strong> période de référence (2023–2030), type de projet, secteur, niveau de priorité, statut (planifié / en cours / réalisé)</li>
                <li><strong>Finance :</strong> source de financement (budget communal, DDC, État béninois, autre), montant prévu (FCFA), taux de réalisation</li>
                <li><strong>Métadonnées :</strong> date de saisie, identifiant de l'agent saisissant (<code>user_id</code>)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Source des données</th>
        <td>Saisie par les agents de mairie habilités sur la Plateforme, sur la base des plans
            de développement communaux (PDC) et des budgets communaux votés</td>
    </tr>
    <tr>
        <th>Destinataires internes</th>
        <td>
            <ul class="mb-0">
                <li>Agents de mairie et administrateurs de la commune concernée</li>
                <li>Super-administrateurs ADECOB</li>
                <li>Équipe technique ADECOB pour les rapports de suivi</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Destinataires externes / sous-traitants</th>
        <td>DDC Suisse : accès aux rapports agrégés de planification ;
            mairies des 8 communes pour leurs propres données de planification</td>
    </tr>
    <tr>
        <th>Transferts hors Bénin</th>
        <td><strong>Oui — rapports agrégés uniquement</strong> vers DDC Suisse (Berne, Suisse).
            Garanties : accord de coopération DDC/ADECOB, données agrégées ne permettant pas
            l'identification individuelle des agents de mairie.</td>
    </tr>
    <tr>
        <th>Durée de conservation</th>
        <td>Durée du programme DDC (jusqu'à 2030) + 5 ans après clôture pour des raisons
            d'audit, d'évaluation et de traçabilité. Noms des responsables anonymisables
            5 ans après la clôture officielle du programme.</td>
    </tr>
    <tr>
        <th>Mesures de sécurité techniques et organisationnelles</th>
        <td>
            <ul class="mb-0">
                <li>Accès scopé par commune (un agent ne voit que les données de sa commune)</li>
                <li>Journalisation des modifications (voir Traitement n°7)</li>
                <li>Sauvegardes quotidiennes chiffrées</li>
                <li>Validation des données par contraintes métier côté serveur</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Droit d'opposition / opt-out</th>
        <td>Un agent peut demander la rectification ou l'anonymisation de son nom dans les fiches
            de planification anciennes, via
            <a href="{{ route('contact.form') }}">le formulaire de contact</a>. L'opposition n'est pas
            recevable pour les fiches en cours de validité, ce traitement relevant d'une mission
            de service public.</td>
    </tr>
</table>

{{-- ============================================================ --}}
{{-- TRAITEMENT N°6 — FORMULAIRE DE CONTACT                      --}}
{{-- ============================================================ --}}

<h2>Traitement n°6 — Formulaire de contact</h2>

<table>
    <tr>
        <th style="width:35%">Responsable du traitement</th>
        <td>ADECOB — Parakou, Bénin</td>
    </tr>
    <tr>
        <th>Finalité(s) précise(s)</th>
        <td>
            <ul class="mb-0">
                <li>Réception et traitement des demandes d'information, de support technique et de signalement adressées à l'ADECOB par les visiteurs et utilisateurs de la Plateforme</li>
                <li>Protection contre le spam et les soumissions automatisées (reCAPTCHA v3)</li>
                <li>Traçabilité des échanges pour assurer le suivi et la clôture des demandes</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Base légale</th>
        <td>Consentement de la personne concernée, matérialisé par la soumission volontaire
            du formulaire
            <em>(art. 386 loi n°2017-20)</em></td>
    </tr>
    <tr>
        <th>Catégories de personnes concernées</th>
        <td>Toute personne physique — visiteur non authentifié, utilisateur authentifié ou citoyen —
            qui soumet volontairement le formulaire de contact</td>
    </tr>
    <tr>
        <th>Catégories de données à caractère personnel</th>
        <td>
            <ul class="mb-0">
                <li><strong>Identité :</strong> nom complet (déclaré librement par l'auteur)</li>
                <li><strong>Contact :</strong> adresse e-mail</li>
                <li><strong>Message :</strong> sujet sélectionné, contenu libre du message (peut contenir des données personnelles à la discrétion de l'auteur)</li>
                <li><strong>Technique :</strong> adresse IP au moment de l'envoi, horodatage (<code>created_at</code>)</li>
                <li><strong>Anti-spam :</strong> score reCAPTCHA v3 (non persisté en base de données après validation)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Source des données</th>
        <td>Déclaration directe et volontaire de la personne via le formulaire en ligne
            accessible sans authentification</td>
    </tr>
    <tr>
        <th>Destinataires internes</th>
        <td>Service communication / responsable de la Plateforme ADECOB désigné pour
            la lecture et le traitement des messages entrants</td>
    </tr>
    <tr>
        <th>Destinataires externes / sous-traitants</th>
        <td>
            <ul class="mb-0">
                <li>Fournisseur SMTP : acheminement de la notification interne par e-mail à l'équipe ADECOB</li>
                <li>Google reCAPTCHA v3 : score anti-bot calculé côté client et serveur Google (voir section Sous-traitants)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Transferts hors Bénin</th>
        <td><strong>Oui</strong> : via Google reCAPTCHA (serveurs Google, USA/UE) et via le
            fournisseur SMTP. Garanties : Data Privacy Framework (DPF) Google, transmission TLS.
            Le contenu du message n'est pas transmis à Google.</td>
    </tr>
    <tr>
        <th>Durée de conservation</th>
        <td>12 mois à compter de la dernière correspondance relative à la demande,
            puis suppression définitive</td>
    </tr>
    <tr>
        <th>Mesures de sécurité techniques et organisationnelles</th>
        <td>
            <ul class="mb-0">
                <li>Protection anti-spam par Google reCAPTCHA v3 (seuil de score configuré côté serveur)</li>
                <li>Rate-limiting sur la route de soumission (3 envois par heure par adresse IP)</li>
                <li>Transmission HTTPS (TLS 1.2+)</li>
                <li>Validation et assainissement (sanitization) de tous les champs côté serveur</li>
                <li>Accès aux messages restreint aux responsables ADECOB expressément habilités</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Droit d'opposition / opt-out</th>
        <td>La personne peut demander la suppression de ses messages via
            <a href="{{ route('contact.form') }}">le formulaire de contact</a>.
            Elle dispose également des droits d'accès, de rectification et d'effacement de ses
            données conformément aux articles 393 et suivants de la loi n°2017-20.</td>
    </tr>
</table>

{{-- ============================================================ --}}
{{-- TRAITEMENT N°7 — JOURNALISATION ET AUDIT DE SÉCURITÉ        --}}
{{-- ============================================================ --}}

<h2>Traitement n°7 — Journalisation et audit de sécurité</h2>

<table>
    <tr>
        <th style="width:35%">Responsable du traitement</th>
        <td>ADECOB — Parakou, Bénin</td>
    </tr>
    <tr>
        <th>Finalité(s) précise(s)</th>
        <td>
            <ul class="mb-0">
                <li>Traçabilité exhaustive des actions sensibles effectuées sur la Plateforme (créations, modifications, suppressions, connexions, déconnexions, exportations de données)</li>
                <li>Détection et investigation des incidents de sécurité et des accès non autorisés</li>
                <li>Preuve d'imputabilité des actions en cas de litige, d'audit ou de réquisition judiciaire</li>
                <li>Surveillance de l'intégrité des données du patrimoine communal</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Base légale</th>
        <td>Obligation légale de sécurité des systèmes d'information et intérêt légitime du
            responsable de traitement — protection du patrimoine de données communales
            <em>(art. 386 loi n°2017-20)</em></td>
    </tr>
    <tr>
        <th>Catégories de personnes concernées</th>
        <td>Tous les utilisateurs authentifiés de la Plateforme, toutes communes et tous rôles
            confondus</td>
    </tr>
    <tr>
        <th>Catégories de données à caractère personnel</th>
        <td>
            <ul class="mb-0">
                <li><strong>Identifiant :</strong> <code>user_id</code> de l'utilisateur ayant réalisé l'action, adresse e-mail associée</li>
                <li><strong>Action :</strong> type d'événement (<code>create</code>, <code>update</code>, <code>delete</code>, <code>login</code>, <code>logout</code>, <code>login_failed</code>, <code>export</code>…)</li>
                <li><strong>Entité :</strong> modèle Eloquent concerné (<code>auditable_type</code>), identifiant de l'enregistrement affecté (<code>auditable_id</code>)</li>
                <li><strong>Données avant/après :</strong> valeurs JSON de l'enregistrement avant modification (<code>old_values</code>) et après modification (<code>new_values</code>) — les mots de passe hachés sont explicitement exclus</li>
                <li><strong>Réseau :</strong> adresse IP de la requête, chaîne <code>User-Agent</code> du navigateur</li>
                <li><strong>Horodatage :</strong> <code>created_at</code> précis à la milliseconde</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Source des données</th>
        <td>Collecte automatique côté serveur via un Observer Eloquent global appliqué à tous
            les modèles sensibles ; collecte des événements d'authentification via les
            listeners d'événements Laravel Auth</td>
    </tr>
    <tr>
        <th>Destinataires internes</th>
        <td>Super-administrateurs ADECOB uniquement (accès en lecture seule à l'interface d'audit) —
            la table <code>audit_logs</code> est en lecture seule pour tous les autres rôles</td>
    </tr>
    <tr>
        <th>Destinataires externes / sous-traitants</th>
        <td>Aucun en conditions normales. En cas d'incident de sécurité avéré, les journaux
            peuvent être transmis aux autorités compétentes (CRIET, APDP) sur réquisition
            judiciaire ou administrative.</td>
    </tr>
    <tr>
        <th>Transferts hors Bénin</th>
        <td><strong>Non</strong> — les journaux d'audit restent exclusivement sur le serveur
            d'hébergement. Aucun transfert prévu hors du territoire béninois en dehors
            d'une réquisition légale.</td>
    </tr>
    <tr>
        <th>Durée de conservation</th>
        <td>
            <ul class="mb-0">
                <li><strong>Journaux courants :</strong> 12 mois glissants</li>
                <li><strong>Journaux liés à un incident de sécurité déclaré :</strong> extension à 36 mois pour les entrées concernées</li>
                <li><strong>Purge :</strong> suppression automatisée des entrées expirées par tâche planifiée (Laravel Scheduler)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Mesures de sécurité techniques et organisationnelles</th>
        <td>
            <ul class="mb-0">
                <li>Table <code>audit_logs</code> accessible uniquement en lecture via les Policies Laravel pour tous les rôles non super-admin</li>
                <li>Aucune interface de suppression manuelle d'entrées pour les administrateurs communaux</li>
                <li>Index composé sur <code>(user_id, created_at)</code> pour les recherches rapides</li>
                <li>Les mots de passe hachés et les jetons de session sont explicitement exclus des colonnes <code>old_values</code> / <code>new_values</code> par configuration de l'Observer</li>
                <li>Sauvegardes chiffrées quotidiennes incluant les journaux</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Droit d'opposition / opt-out</th>
        <td>Traitement non opposable : la journalisation est une obligation de sécurité inhérente
            à l'exploitation de la Plateforme. Un utilisateur peut demander l'accès à ses propres
            entrées de journal (droit d'accès), via
            <a href="{{ route('contact.form') }}">le formulaire de contact</a>,
            après vérification de son identité.</td>
    </tr>
</table>

{{-- ============================================================ --}}
{{-- TRAITEMENT N°8 — RÉINITIALISATION DE MOT DE PASSE           --}}
{{-- ============================================================ --}}

<h2>Traitement n°8 — Réinitialisation de mot de passe</h2>

<table>
    <tr>
        <th style="width:35%">Responsable du traitement</th>
        <td>ADECOB — Parakou, Bénin</td>
    </tr>
    <tr>
        <th>Finalité(s) précise(s)</th>
        <td>
            <ul class="mb-0">
                <li>Permettre à un utilisateur ayant perdu l'accès à son compte de redéfinir son mot de passe de manière sécurisée</li>
                <li>Générer, stocker et valider un jeton de réinitialisation à usage unique et à durée de vie strictement limitée</li>
                <li>Prévenir les attaques par force brute ou par réutilisation de jetons exposés</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Base légale</th>
        <td>Exécution d'un service sollicité par la personne concernée — la demande de
            réinitialisation est initiée à la seule initiative de l'utilisateur
            <em>(art. 386 loi n°2017-20)</em></td>
    </tr>
    <tr>
        <th>Catégories de personnes concernées</th>
        <td>Tout utilisateur authentifié de la Plateforme ayant initié une demande de
            réinitialisation de mot de passe</td>
    </tr>
    <tr>
        <th>Catégories de données à caractère personnel</th>
        <td>
            <ul class="mb-0">
                <li><strong>Identifiant :</strong> adresse e-mail du compte (clé primaire de la table <code>password_reset_tokens</code>)</li>
                <li><strong>Jeton :</strong> jeton de réinitialisation haché (SHA-256) — jamais stocké en clair en base de données</li>
                <li><strong>Horodatage :</strong> <code>created_at</code> de la demande (utilisé pour le calcul de l'expiration)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Source des données</th>
        <td>Demande initiée par l'utilisateur sur la page "Mot de passe oublié" ; jeton généré
            automatiquement par le Password Broker Laravel</td>
    </tr>
    <tr>
        <th>Destinataires internes</th>
        <td>Aucun accès humain direct — traitement entièrement automatisé par le composant
            Password Broker de Laravel</td>
    </tr>
    <tr>
        <th>Destinataires externes / sous-traitants</th>
        <td>Fournisseur SMTP : le lien de réinitialisation contenant le jeton en clair
            (avant hachage) est transmis à l'adresse e-mail enregistrée du compte ;
            seule l'adresse e-mail destinataire et le corps de l'e-mail sont communiqués
            au service d'envoi</td>
    </tr>
    <tr>
        <th>Transferts hors Bénin</th>
        <td><strong>Potentiellement oui</strong> via le fournisseur SMTP. Garanties :
            TLS 1.2+ obligatoire, jeton à usage unique, durée de vie très courte (60 min).</td>
    </tr>
    <tr>
        <th>Durée de conservation</th>
        <td>
            <ul class="mb-0">
                <li><strong>Entrée en table <code>password_reset_tokens</code> :</strong> 60 minutes après génération, puis suppression automatique par expiration</li>
                <li><strong>Invalidation immédiate</strong> après utilisation réussie (usage unique strict)</li>
                <li><strong>Trace dans les journaux d'audit :</strong> 12 mois (voir Traitement n°7)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Mesures de sécurité techniques et organisationnelles</th>
        <td>
            <ul class="mb-0">
                <li>Jeton stocké sous forme hachée (SHA-256) — seul le lien e-mail contient le jeton en clair</li>
                <li>Expiration strictement automatique après 60 minutes</li>
                <li>Usage unique : invalidation immédiate après utilisation réussie</li>
                <li>Rate-limiting : 3 demandes par minute par adresse IP, 5 par heure par adresse e-mail</li>
                <li>Envoi exclusivement sur l'adresse e-mail enregistrée et préalablement vérifiée du compte</li>
                <li>Aucune révélation de l'existence ou non du compte dans la réponse HTTP (protection contre l'énumération des comptes)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Droit d'opposition / opt-out</th>
        <td>Non applicable : traitement initié exclusivement à la demande explicite de l'utilisateur,
            sans collecte de données supplémentaires et sans persistance au-delà de 60 minutes.</td>
    </tr>
</table>

{{-- ============================================================ --}}
{{-- TRAITEMENT N°9 — CONNEXION VIA GOOGLE OAUTH 2.0             --}}
{{-- ============================================================ --}}

<h2>Traitement n°9 — Connexion via Google OAuth 2.0</h2>

<table>
    <tr>
        <th style="width:35%">Responsable du traitement</th>
        <td>ADECOB — Parakou, Bénin</td>
    </tr>
    <tr>
        <th>Finalité(s) précise(s)</th>
        <td>
            <ul class="mb-0">
                <li>Permettre aux utilisateurs disposant d'un compte Google de s'authentifier sur la Plateforme sans saisir leur mot de passe ADECOB</li>
                <li>Vérifier que l'adresse e-mail Google correspond à un compte ADECOB existant, actif et validé</li>
                <li>Simplifier l'expérience de connexion pour les agents disposant d'une adresse Google professionnelle</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Base légale</th>
        <td>Consentement explicite de la personne concernée, matérialisé par le clic volontaire sur
            "Se connecter avec Google" et l'autorisation accordée sur la page de consentement Google
            <em>(art. 386 loi n°2017-20)</em></td>
    </tr>
    <tr>
        <th>Catégories de personnes concernées</th>
        <td>Utilisateurs authentifiés de la Plateforme qui choisissent activement d'utiliser
            Google OAuth comme méthode d'authentification</td>
    </tr>
    <tr>
        <th>Catégories de données à caractère personnel</th>
        <td>
            <ul class="mb-0">
                <li><strong>Reçues de Google (en mémoire uniquement) :</strong> adresse e-mail Google, nom d'affichage Google (prénom / nom), URL de la photo de profil Google</li>
                <li><strong>Données utilisées pour la vérification locale :</strong> adresse e-mail uniquement, mise en correspondance avec la colonne <code>users.email</code></li>
                <li><strong>Non stockées en base de données ADECOB :</strong> identifiant Google (<code>sub</code>), jeton d'accès OAuth, jeton de rafraîchissement, photo de profil</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Source des données</th>
        <td>API Google OAuth 2.0 / OpenID Connect — données transmises par Google après
            consentement explicite de l'utilisateur sur la page d'autorisation Google</td>
    </tr>
    <tr>
        <th>Destinataires internes</th>
        <td>Composant Laravel Socialite (traitement en mémoire vive uniquement, sans persistance
            des données OAuth dans la base de données)</td>
    </tr>
    <tr>
        <th>Destinataires externes / sous-traitants</th>
        <td>Google LLC — co-intervenant dans le flux OAuth ; la requête d'authentification transite
            par les serveurs de Google Identity Platform</td>
    </tr>
    <tr>
        <th>Transferts hors Bénin</th>
        <td><strong>Oui</strong> — vers les serveurs Google (USA / UE selon le routage).
            Garanties : Google adhère au Data Privacy Framework (DPF) UE–USA ;
            les données transitant vers ADECOB se limitent à l'e-mail pour la vérification locale ;
            politique de confidentialité Google applicable au flux OAuth.</td>
    </tr>
    <tr>
        <th>Durée de conservation</th>
        <td>Aucune donnée spécifique à Google OAuth n'est persistée en base de données ADECOB.
            Après authentification réussie, la session Laravel standard est créée
            (durée d'inactivité : 120 minutes par défaut).</td>
    </tr>
    <tr>
        <th>Mesures de sécurité techniques et organisationnelles</th>
        <td>
            <ul class="mb-0">
                <li><strong>Absence de création automatique de compte :</strong> si l'e-mail Google ne correspond pas à un compte ADECOB existant, actif et validé, la connexion est refusée sans révéler d'information</li>
                <li>Aucun stockage du jeton OAuth, du <code>sub</code> Google ou de la photo de profil en base de données</li>
                <li>Vérification du paramètre <code>state</code> OAuth (protection CSRF conforme à RFC 6749)</li>
                <li>Communication exclusivement en HTTPS avec les endpoints Google</li>
                <li>Journalisation de l'événement de connexion OAuth dans les journaux d'audit (voir Traitement n°7)</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>Droit d'opposition / opt-out</th>
        <td>L'utilisateur peut révoquer l'accès Google à tout moment depuis les paramètres de son
            compte Google (Sécurité › Applications tierces). Sur la Plateforme, il peut continuer
            à se connecter avec son identifiant et son mot de passe ADECOB. La révocation OAuth
            n'entraîne aucune perte de données ni de droits sur la Plateforme.</td>
    </tr>
</table>

{{-- ============================================================ --}}
{{-- SECTION : SOUS-TRAITANTS ET PRESTATAIRES TECHNIQUES         --}}
{{-- ============================================================ --}}

<h2>Sous-traitants et prestataires techniques</h2>

<p>Les prestataires ci-dessous interviennent dans le cadre de la Plateforme ADECOB. Conformément
aux articles 412 et suivants de la loi n°2017-20, chaque sous-traitant est lié à l'ADECOB par des
engagements contractuels garantissant un niveau de protection adéquat des données personnelles.
Aucun sous-traitant n'est autorisé à utiliser les données personnelles auxquelles il accède à des
fins autres que celles définies par contrat.</p>

<table>
    <tr>
        <th>Sous-traitant / Prestataire</th>
        <th>Service fourni</th>
        <th>Données personnelles traitées</th>
        <th>Localisation</th>
        <th>Garanties contractuelles</th>
    </tr>
    <tr>
        <td><strong>Hébergeur applicatif</strong><br><em>(Hostinger ou équivalent)</em></td>
        <td>Hébergement de l'application Laravel, de la base de données MariaDB, des fichiers
            uploadés (photos d'infrastructures) et des sauvegardes chiffrées</td>
        <td>Toutes les données personnelles traitées par la Plateforme, en transit et au repos
            sur le serveur</td>
        <td>Union Européenne (data center UE)</td>
        <td>Contrat de service avec clauses de confidentialité et de sécurité ; chiffrement des
            données au repos et en transit ; accès restreint aux ingénieurs d'astreinte sous NDA ;
            certifications ISO 27001 ou équivalent ; politique de sauvegarde documentée</td>
    </tr>
    <tr>
        <td><strong>Fournisseur SMTP</strong><br><em>Mailtrap (environnement de développement)<br>
            SMTP de production (à confirmer)</em></td>
        <td>Acheminement de l'ensemble des e-mails transactionnels de la Plateforme : codes OTP MFA,
            liens de réinitialisation de mot de passe, notifications d'activation / refus de compte,
            alertes de sécurité, réponses au formulaire de contact</td>
        <td>Adresse e-mail du destinataire, contenu de l'e-mail (peut inclure un code OTP ou un lien
            de réinitialisation à durée de vie courte)</td>
        <td>Variable selon le fournisseur (UE / USA)</td>
        <td>Contrat de traitement des données ; transmission chiffrée TLS 1.2+ obligatoire ;
            journaux d'envoi conservés 30 jours maximum ; données minimales transmises ;
            aucune exploitation commerciale des contenus d'e-mails</td>
    </tr>
    <tr>
        <td><strong>Google LLC</strong><br><em>reCAPTCHA v3 + OAuth 2.0</em></td>
        <td>
            <ul class="mb-0">
                <li><strong>reCAPTCHA v3 :</strong> protection anti-bot des formulaires de contact, de connexion et d'inscription</li>
                <li><strong>Google OAuth 2.0 :</strong> authentification des utilisateurs via leur compte Google (voir Traitement n°9)</li>
            </ul>
        </td>
        <td>
            <ul class="mb-0">
                <li><em>reCAPTCHA :</em> adresse IP du visiteur, empreinte du navigateur (user-agent, cookies Google), interactions sur la page — Google retourne un score de risque</li>
                <li><em>OAuth :</em> adresse e-mail Google, nom d'affichage (traitement en mémoire uniquement côté ADECOB)</li>
            </ul>
        </td>
        <td>USA / UE (infrastructure Google Cloud)</td>
        <td>Data Privacy Framework (DPF) UE–USA ; politique de confidentialité Google ;
            conditions d'utilisation reCAPTCHA ; Google déclare ne pas utiliser les données
            reCAPTCHA à des fins publicitaires ; accord de traitement des données Google (DPA)
            disponible sur <em>google.com/about/datacenters/data-security</em></td>
    </tr>
    <tr>
        <td><strong>OpenStreetMap Foundation (OSMF)<br>Nominatim</strong></td>
        <td>Affichage des tuiles cartographiques (fond de carte) et géocodage des adresses /
            coordonnées GPS pour la localisation des infrastructures communales</td>
        <td>Coordonnées GPS ou noms de lieux (villages, arrondissements, communes) — aucune
            donnée nominative individuelle transmise aux serveurs OSMF</td>
        <td>UE (serveurs OSMF)</td>
        <td>Service public communautaire sous licence ODbL ; politique d'utilisation acceptable
            OpenStreetMap ; les requêtes de géocodage ne contiennent aucune information permettant
            l'identification d'une personne physique ; rate-limiting respecté conformément aux
            conditions d'utilisation Nominatim</td>
    </tr>
</table>

{{-- ============================================================ --}}
{{-- SECTION : SYNTHÈSE DES TRAITEMENTS                          --}}
{{-- ============================================================ --}}

<h2>Synthèse des traitements recensés</h2>

<p>Le tableau ci-dessous offre une vue d'ensemble de l'ensemble des traitements documentés dans
ce registre, permettant une lecture rapide à des fins de contrôle interne ou d'audit externe.</p>

<table>
    <tr>
        <th>N°</th>
        <th>Nom du traitement</th>
        <th>Base légale principale</th>
        <th>Durée de conservation</th>
        <th>Données sensibles</th>
        <th>Transfert hors Bénin</th>
    </tr>
    <tr>
        <td><strong>1</strong></td>
        <td>Gestion des comptes et authentification</td>
        <td>Mission d'intérêt public + consentement</td>
        <td>Durée d'activité + 3 ans</td>
        <td>Non</td>
        <td>Potentiel (SMTP)</td>
    </tr>
    <tr>
        <td><strong>2</strong></td>
        <td>Authentification multi-facteurs (MFA)</td>
        <td>Intérêt légitime</td>
        <td>10 min (code OTP) / 90 j (journaux MFA)</td>
        <td>Non</td>
        <td>Potentiel (SMTP)</td>
    </tr>
    <tr>
        <td><strong>3</strong></td>
        <td>Collecte et gestion des infrastructures communales</td>
        <td>Mission d'intérêt public</td>
        <td>Pérenne (anonymisation enquêteur après 5 ans)</td>
        <td>Non</td>
        <td>Limité (Nominatim / rapports DDC agrégés)</td>
    </tr>
    <tr>
        <td><strong>4</strong></td>
        <td>Planification des travaux de maintenance</td>
        <td>Mission d'intérêt public</td>
        <td>10 ans</td>
        <td>Non</td>
        <td>Non</td>
    </tr>
    <tr>
        <td><strong>5</strong></td>
        <td>Données de planification mairie (MairieAgentData)</td>
        <td>Mission d'intérêt public</td>
        <td>Jusqu'en 2030 + 5 ans après clôture</td>
        <td>Non</td>
        <td>Oui (DDC Suisse — agrégé uniquement)</td>
    </tr>
    <tr>
        <td><strong>6</strong></td>
        <td>Formulaire de contact</td>
        <td>Consentement</td>
        <td>12 mois après dernière correspondance</td>
        <td>Non</td>
        <td>Oui (reCAPTCHA Google, SMTP)</td>
    </tr>
    <tr>
        <td><strong>7</strong></td>
        <td>Journalisation et audit de sécurité</td>
        <td>Obligation légale + intérêt légitime</td>
        <td>12 mois (36 mois sur incident déclaré)</td>
        <td>Non</td>
        <td>Non</td>
    </tr>
    <tr>
        <td><strong>8</strong></td>
        <td>Réinitialisation de mot de passe</td>
        <td>Exécution du service demandé</td>
        <td>60 minutes (jeton), puis suppression</td>
        <td>Non</td>
        <td>Potentiel (SMTP)</td>
    </tr>
    <tr>
        <td><strong>9</strong></td>
        <td>Connexion via Google OAuth 2.0</td>
        <td>Consentement</td>
        <td>Aucune persistance locale</td>
        <td>Non</td>
        <td>Oui (Google LLC — e-mail en mémoire)</td>
    </tr>
</table>

{{-- ============================================================ --}}
{{-- SECTION : DÉCLARATION APDP ET DROITS DES PERSONNES          --}}
{{-- ============================================================ --}}

<h2>Déclaration à l'APDP et exercice des droits des personnes concernées</h2>

<h3>Obligation de déclaration</h3>

<p>Conformément aux <strong>articles 412 et suivants de la loi n°2017-20 portant Code du numérique
en République du Bénin</strong>, tout responsable de traitement de données à caractère personnel
est tenu de notifier ses traitements à l'<strong>Autorité de Protection des Données Personnelles
du Bénin (APDP)</strong>. L'ADECOB s'engage à déclarer l'ensemble des traitements recensés dans
le présent registre auprès de l'APDP, et à tenir ce registre à jour à chaque évolution
significative de la Plateforme, de ses finalités ou de ses sous-traitants.</p>

<div class="alert alert-light border">
    <strong>Coordonnées de l'Autorité de Protection des Données Personnelles du Bénin (APDP) :</strong><br>
    APDP — Autorité de Protection des Données Personnelles<br>
    Cotonou, République du Bénin<br>
    Site web officiel :
    <a href="https://www.apdp.bj" target="_blank" rel="noopener noreferrer">www.apdp.bj</a>
</div>

<h3>Droits des personnes concernées</h3>

<p>Toute personne concernée par les traitements décrits dans ce registre dispose des droits suivants,
conformément aux <strong>articles 393 et suivants de la loi n°2017-20</strong> :</p>

<ul>
    <li><strong>Droit d'accès (art. 393)</strong> — obtenir confirmation que des données la concernant sont traitées, et en recevoir une copie</li>
    <li><strong>Droit de rectification (art. 394)</strong> — faire corriger des données inexactes ou incomplètes</li>
    <li><strong>Droit à l'effacement (art. 395)</strong> — demander la suppression des données, dans les limites des obligations légales de conservation</li>
    <li><strong>Droit d'opposition (art. 396)</strong> — s'opposer à un traitement fondé sur l'intérêt légitime, sauf si des motifs légitimes impérieux du responsable de traitement prévalent</li>
    <li><strong>Droit à la limitation du traitement</strong> — demander la suspension temporaire d'un traitement le temps qu'une contestation soit résolue</li>
    <li><strong>Droit à la portabilité</strong> — recevoir ses données dans un format structuré et lisible par machine, pour les traitements fondés sur le consentement</li>
</ul>

<p>Ces droits s'exercent par voie de demande écrite adressée à l'ADECOB via le
<a href="{{ route('contact.form') }}">formulaire de contact</a>. L'ADECOB s'engage à accuser réception
et à apporter une réponse dans un délai de <strong>30 jours ouvrables</strong> à compter de la
réception de la demande. En cas de réponse jugée insatisfaisante, la personne concernée peut
saisir l'<strong>APDP</strong> aux coordonnées indiquées ci-dessus.</p>

<h3>Documents connexes</h3>

<ul>
    <li><a href="{{ route('legal.confidentialite') }}">Politique de confidentialité</a> —
        modalités détaillées de collecte et d'utilisation des données personnelles,
        à destination des utilisateurs de la Plateforme</li>
    <li><a href="{{ route('legal.cgu') }}">Conditions Générales d'Utilisation (CGU)</a> —
        règles d'accès, d'utilisation et de responsabilité sur la Plateforme</li>
    <li><a href="{{ route('legal.pssi') }}">Politique de Sécurité du Système d'Information (PSSI)</a> —
        description des mesures organisationnelles et techniques de sécurité mises en œuvre</li>
</ul>

<p class="text-muted small mt-4">Ce registre est mis à jour à chaque évolution significative
de la Plateforme ou des traitements de données qui y sont associés. La version en vigueur
est celle publiée sur la Plateforme à la date de consultation.</p>

@endsection
