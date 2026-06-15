# 👨‍💼 GUIDE D'ADMINISTRATION - PLATEFORME ADECOB

## Guide pour les Administrateurs Système

---

## 📑 TABLE DES MATIÈRES

1. [Introduction](#introduction)
2. [Panneau d'administration](#panneau-admin)
3. [Gestion des utilisateurs](#gestion-utilisateurs)
4. [Gestion des communes](#gestion-communes)
5. [Codes d'accès](#codes-acces)
6. [Gestion des rôles et permissions](#roles-permissions)
7. [Sauvegardes et maintenance](#maintenance)
8. [Sécurité](#securite)
9. [Journal d'audit](#audit)
10. [Dépannage administration](#depannage-admin)

---

## 1. INTRODUCTION {#introduction}

Ce guide est destiné aux **administrateurs système** responsables de la gestion globale de la plateforme ADECOB.

### Accès administrateur

**URL du panneau admin:** `http://127.0.0.1:8000/admin`

**Accès requis:** Super Admin uniquement

> **⚠️ IMPORTANT:** L'accès administrateur n'est réservé que aux Super Admin. Les autres rôles ne peuvent pas accéder au panneau d'administration.

### Responsabilités principales

- ✅ Gérer les comptes utilisateurs (création, modification, suppression)
- ✅ Attribuer et modifier les rôles et permissions
- ✅ Gérer les communes et leurs paramètres
- ✅ Configurer les codes d'accès
- ✅ Superviser les activités des utilisateurs
- ✅ Effectuer les sauvegardes et la maintenance
- ✅ Assurer la sécurité du système
- ✅ Traiter les incidents et demandes d'assistance

---

## 2. PANNEAU D'ADMINISTRATION {#panneau-admin}

### 2.1 Accueil du panneau admin

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du tableau de bord administrateur

Le panneau affiche:

**Section 1: Statistiques globales**
```
├─ Nombre total d'utilisateurs
├─ Nombre d'utilisateurs par rôle
├─ Nombre de communes
├─ Nombre total d'infrastructures
├─ Infrastructures ajoutées ce mois
└─ Derniers utilisateurs créés
```

**Section 2: Activité récente**
```
├─ Connexions récentes
├─ Créations d'infrastructures
├─ Modifications d'utilisateurs
├─ Changements de permissions
└─ Alertes système
```

**Section 3: Menu de navigation**
```
├─ Dashboard
├─ Utilisateurs
├─ Communes
├─ Infrastructures
├─ Rapports
├─ Paramètres
├─ Journaux
└─ Déconnexion
```

### 2.2 Menu de navigation admin

**Menu principal:**
```
ADMINISTRATION
├─ 📊 Dashboard
├─ 👥 Utilisateurs
│  ├─ Liste des utilisateurs
│  ├─ Créer utilisateur
│  ├─ Supprimer utilisateurs
│  └─ Gérer rôles
├─ 🏛️ Communes
│  ├─ Liste des communes
│  ├─ Créer commune
│  ├─ Codes d'accès
│  └─ Paramètres communes
├─ 🏗️ Infrastructures
│  ├─ Consulter toutes
│  ├─ Supprimer en masse
│  ├─ Vérifications
│  └─ Statistiques
├─ 📈 Rapports
│  ├─ Rapport utilisateurs
│  ├─ Rapport infrastructures
│  ├─ Rapport activité
│  └─ Export général
├─ ⚙️ Paramètres
│  ├─ Profil admin
│  ├─ Configuration système
│  ├─ Paramètres de sécurité
│  └─ Email/Notifications
└─ 📋 Journaux
   ├─ Audit
   ├─ Erreurs
   ├─ Accès
   └─ Modifications
```

---

## 3. GESTION DES UTILISATEURS {#gestion-utilisateurs}

### 3.1 Liste des utilisateurs

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de la liste des utilisateurs administrateur

**Tableau des utilisateurs:**

| Colonne | Contenu |
|---------|---------|
| ID | Identifiant unique |
| Nom | Nom complet |
| Email | Adresse email |
| Rôle | Super Admin, Commune Admin, Agent, Public |
| Commune | Commune assignée (si applicable) |
| Statut | Actif, Inactif, En attente |
| Créé | Date de création du compte |
| Actions | Voir, Modifier, Supprimer |

**Filtres et recherche:**
```
├─ Recherche par nom
├─ Recherche par email
├─ Filtrer par rôle
├─ Filtrer par commune
├─ Filtrer par statut
└─ Filtrer par date
```

### 3.2 Créer un nouvel utilisateur

#### Processus:

1. Cliquez sur **[➕ Créer utilisateur]**
2. Remplissez le formulaire

#### Formulaire de création:

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du formulaire de création d'utilisateur

**Champs obligatoires:**

```
INFORMATIONS PERSONNELLES:
├─ Nom complet *           (texte)
├─ Email *                 (email)
├─ Téléphone              (téléphone)
└─ Date de naissance      (date)

IDENTIFIANTS DE CONNEXION:
├─ Email (pour la connexion) * (email)
├─ Mot de passe *             (texte sécurisé)
├─ Confirmer mot de passe *   (texte sécurisé)
└─ ☐ Générer mot de passe random (option)

RÔLE ET PERMISSIONS:
├─ Rôle *                 (dropdown: Super Admin / Commune Admin / Agent / Public)
├─ Commune               (dropdown - selon rôle)
├─ Approbation statut    (dropdown: Approuvé / En attente)
└─ Actif                 (toggle: Oui/Non)

INFORMATIONS SUPPLÉMENTAIRES:
├─ Adresse               (texte)
├─ Ville                 (texte)
├─ Code postal           (texte)
└─ Notes                 (texte long)
```

**Validations:**
- ✅ Email unique (pas de doublon)
- ✅ Mot de passe minimum 8 caractères
- ✅ Au moins 1 majuscule et 1 chiffre dans le mot de passe
- ✅ Rôle obligatoire

#### Après création:

```
✅ Utilisateur créé avec succès

Email de confirmation envoyé à: [email]
Statut: En attente d'approbation
```

Un email est envoyé à l'utilisateur avec les instructions de connexion.

### 3.3 Modifier un utilisateur

#### Processus:

1. Trouvez l'utilisateur dans la liste
2. Cliquez sur **[✏️ Modifier]**
3. Modifiez les champs voulus
4. Cliquez sur **[💾 Enregistrer]**

#### Champs modifiables:

```
✅ Modifiables:
├─ Nom complet
├─ Email
├─ Téléphone
├─ Rôle
├─ Commune (selon rôle)
├─ Statut d'approbation
├─ Actif/Inactif
├─ Adresse
└─ Notes

❌ Non modifiables:
├─ ID utilisateur
├─ Date de création
└─ Historique d'accès
```

#### Modification du rôle:

> **⚠️ IMPORTANT:** Changer le rôle d'un utilisateur a des impacts importants

**Avant de changer le rôle:**
1. Archivez les données de l'utilisateur (si nécessaire)
2. Informez l'utilisateur du changement
3. Vérifiez les permissions requises

**Flux de changement de rôle:**
```
ANCIEN RÔLE → NOUVEAU RÔLE
├─ Agent → Commune Admin
│  └─ Données créées restent visibles
├─ Commune Admin → Agent
│  └─ Perte d'accès à d'autres données de la commune
├─ X → Super Admin
│  └─ Accès complet immédiat
└─ Commune Admin → Public
   └─ Limitation drastique d'accès
```

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du formulaire de modification d'utilisateur

### 3.4 Approuver des utilisateurs en attente

**Processus:**

1. Allez à **[Utilisateurs] → [En attente d'approbation]**
2. Consultez les demandes d'inscription
3. Pour chaque utilisateur:
   - ✅ Cliquez **[Approuver]** - Compte activé
   - ❌ Cliquez **[Rejeter]** - Email de refus envoyé

**Tableau des en attente:**

```
| Nom | Email | Date inscription | Actions |
|-----|-------|-------------------|---------|
| Jean... | jean@... | 25/02/2026 | [✅ Approuver] [❌ Rejeter] |
```

### 3.5 Désactiver/Réactiver un utilisateur

**Désactiver:**
```
1. Ouvrir le profil utilisateur
2. Basculer "Actif" à OFF
3. Enregistrer
└─ L'utilisateur ne peut plus se connecter
```

**Réactiver:**
```
1. Ouvrir le profil utilisateur
2. Basculer "Actif" à ON
3. Enregistrer
└─ L'utilisateur peut se reconnecter
```

### 3.6 Supprimer un utilisateur

#### ⚠️ ATTENTION: Action irréversible

**Processus:**

1. Cliquez **[🗑️ Supprimer]** sur le profil utilisateur
2. **Confirmation requise:**
   ```
   ⚠️ ATTENTION - Suppression d'utilisateur irréversible
   
   Cette action va:
   ✓ Supprimer le compte utilisateur
   ✓ Supprimer TOUTES ses données personnelles
   ✓ CONSERVER les infrastructures créées (orphelines)
   ✓ Vous ne pouvez pas annuler
   
   [Annuler]  [Supprimer]
   ```

3. **Après suppression:**
   ```
   ✅ Utilisateur supprimé avec succès
   
   Les infrastructures créées par cet utilisateur
   sont conservées mais sans propriétaire assigné.
   ```

> **Bonne pratique:** Plutôt que de supprimer, désactivez le compte pour conserver l'historique

### 3.7 Réinitialiser le mot de passe d'un utilisateur

**Processus:**

1. Ouvrez le profil utilisateur
2. Cliquez sur **[🔑 Réinitialiser le mot de passe]**
3. **Options:**

```
Option 1: Générer un mot de passe temporaire
└─ Admin génère un mot de passe aléatoire
└─ Email envoyé à l'utilisateur
└─ Utilisateur doit changer au prochain login

Option 2: Envoyer lien de réinitialisation
└─ Email avec lien de réinitialisation envoyé
└─ Utilisateur crée son propre mot de passe
└─ Lien valide pendant 24 heures
```

---

## 4. GESTION DES COMMUNES {#gestion-communes}

### 4.1 Liste des communes

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de la liste des communes administrateur

**Tableau des communes:**

| Colonne | Contenu |
|---------|---------|
| ID | Identifiant unique |
| Nom | Nom de la commune |
| Logo | Image/logo de la commune |
| Commune Admin | Responsable assigné |
| Infra. | Nombre d'infrastructures |
| Agents | Nombre d'agents |
| Créée | Date de création |
| Actions | Voir, Modifier, Supprimer |

### 4.2 Créer une nouvelle commune

#### Processus:

1. Cliquez sur **[➕ Créer commune]**
2. Remplissez le formulaire

#### Formulaire de création:

```
INFORMATIONS DE BASE:
├─ Nom de la commune *        (texte)
├─ Logo/Image                 (upload image)
├─ Description                (texte long)
└─ Slug URL                   (auto-généré)

LOCALISATION:
├─ Région                     (texte)
├─ Province                   (texte)
├─ Arrondissements            (texte - liste)
└─ Villages                   (texte - liste)

CONTACTS:
├─ Email de contact           (email)
├─ Téléphone                  (téléphone)
├─ Adresse                    (texte)
└─ Site web                   (URL)

PARAMÈTRES:
├─ Commune Admin assigné *    (dropdown utilisateurs)
├─ Statut                     (Actif/Inactif)
└─ Publique                   (Oui/Non)
```

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du formulaire de création de commune

### 4.3 Modifier une commune

**Processus similaire à la création**

Champs modifiables:
```
✅ Tous les champs peuvent être modifiés
   sauf:
├─ ID commune
└─ Date de création
```

### 4.4 Supprimer une commune

> ⚠️ **ATTENTION:** Vérifiez avant de supprimer!

**Impact de la suppression:**
- ❌ Les infrastructures liées seront:
  - Soit supprimées aussi
  - Soit orphelines (sans commune)
  
**Processus:**

1. Cliquez **[🗑️ Supprimer]**
2. Confirmation avec impact
3. Si suppression d'infrastructures, confirmation supplémentaire

---

## 5. CODES D'ACCÈS {#codes-acces}

### 5.1 Gestion des codes d'accès

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de l'interface de gestion des codes d'accès

**Liste des codes par commune:**

```
Commune: [Nom]
├─ Code actuel: ••••••••••
├─ Date création: 25/02/2025
├─ Dernière modif: 20/02/2026
├─ Créé par: Jean Admin
└─ Modifié par: Marie Admin
```

### 5.2 Créer/Réinitialiser un code d'accès

**Processus:**

1. Allez à **[Communes] → [Codes d'accès]**
2. Sélectionnez la commune
3. Cliquez **[🔐 Nouveau code] ou [🔄 Réinitialiser]**

**Options:**

```
Option 1: Générer automatiquement
└─ Système génère un code aléatoire (8-12 caractères)
└─ Sélectionnez "Générer"
└─ Code généré automatiquement

Option 2: Définir personnalisé
├─ Vous entrez le code voulu
├─ Minimum 6 caractères
├─ Peut contenir lettres + chiffres
└─ Optionnel: caractères spéciaux
```

### 5.3 Distribuer les codes d'accès

**Après création/modification du code:**

```
Notification:
├─ ✉️ Email aux Agents de la commune
├─ ✉️ Email au Commune Admin
├─ 📋 Affichage sur le dashboard admin
└─ 📝 Enregistrement dans l'audit
```

**Modèle d'email:**
```
Sujet: Nouveau code d'accès - [Nom Commune]

Bonjour,

Un nouveau code d'accès a été généré pour la commune:
Commune: [Nom]
Code: [CODE]
Validité: Indéfinie (jusqu'à nouvelle modification)

Pour accéder aux données:
1. Allez à http://127.0.0.1:8000
2. Connectez-vous
3. Sélectionnez [Commune]
4. Entrez le code: [CODE]

Merci,
Équipe ADECOB
```

### 5.4 Historique des codes

**Journal de changement:**

```
Commune: École Primaire X

Date | Modifié | Code ancien → Code nouveau | Raison
-----|---------|---------------------------|--------
20/02 | Jean A. | CODE1234 → CODE5678 | Changement semestriel
15/01 | Marie A.| CODE9876 → CODE1234 | Renouvellement
```

---

## 6. GESTION DES RÔLES ET PERMISSIONS {#roles-permissions}

### 6.1 Vue d'ensemble des rôles

```
SUPER ADMIN
├─ Accès: TOUT
├─ Permissions: TOUTES
├─ Limites: AUCUNE
└─ Notes: Gestion complète

COMMUNE ADMIN
├─ Accès: Sa commune uniquement
├─ Permissions: Gestion commune + données
├─ Limites: Autres communes invisibles
└─ Notes: Responsable commune

AGENT
├─ Accès: Communes assignées
├─ Permissions: Créer/Modifier propres données
├─ Limites: Accès par code obligatoire
└─ Notes: Collecteur de données

PUBLIC USER
├─ Accès: Vue statistiques publiques
├─ Permissions: Lecture statistiques
├─ Limites: Aucun accès données détaillées
└─ Notes: Consultation publique
```

### 6.2 Modification des permissions

**Processus:**

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de l'interface de gestion des permissions

1. Allez à **[Paramètres] → [Rôles & Permissions]**
2. Sélectionnez un rôle
3. Cochez/Décochez les permissions

**Permissions disponibles:**

```
INFRASTRUCTURES:
☑️ Créer
☑️ Lire
☑️ Modifier
☑️ Supprimer
☑️ Exporter
☑️ Importer

UTILISATEURS:
☑️ Voir
☑️ Créer
☑️ Modifier
☑️ Supprimer

COMMUNES:
☑️ Voir
☑️ Créer
☑️ Modifier
☑️ Supprimer

RAPPORTS:
☑️ Voir
☑️ Exporter

ADMINISTRATION:
☑️ Accéder admin
☑️ Voir journaux
☑️ Gérer codes
```

> **⚠️ WARNING:** Modifier les permissions affecte tous les utilisateurs avec ce rôle!

---

## 7. SAUVEGARDES ET MAINTENANCE {#maintenance}

### 7.1 Sauvegardes automatiques

**Fréquence:** Automatique tous les jours à 00:00

**Localisation:** `/storage/backups/`

**Rétention:** 30 derniers jours

### 7.2 Effectuer une sauvegarde manuelle

**Processus:**

1. Allez à **[Paramètres] → [Maintenance]**
2. Cliquez **[💾 Créer sauvegarde]**

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de l'écran de maintenance

**Progression:**
```
Création de sauvegarde...
├─ Base de données: ████████░░ 80%
├─ Fichiers: ██████░░░░ 60%
└─ Compression: ██░░░░░░░░ 20%

Temps estimé: 2 minutes
```

**Après:**
```
✅ Sauvegarde créée avec succès
Fichier: backup_2026-02-26_143022.zip
Taille: 125 MB
Disponible pour: 30 jours
```

### 7.3 Restaurer une sauvegarde

> ⚠️ **ATTENTION:** Cette action peut être dangereuse!

**Processus:**

1. Allez à **[Paramètres] → [Maintenance]**
2. Cliquez **[📥 Restaurer]** sur une sauvegarde
3. **Confirmation double:**
   ```
   ⚠️ RESTAURATION DE SAUVEGARDE - CONFIRMATION
   
   Cette action va:
   ✓ Remplacer TOUTES les données actuelles
   ✓ Restaurer les données du [DATE]
   ✓ Perdre TOUS les changements depuis [DATE]
   
   Êtes-vous ABSOLUMENT sûr?
   [Annuler]  [Oui, restaurer]
   ```

4. Processus de restauration:
   ```
   Restauration en cours...
   └─ Base de données: ████████████ 100%
   
   ✅ Restauration terminée
   ```

### 7.4 Maintenance du système

**Tâches régulières:**

```
QUOTIDIENNE:
├─ Vérifier les erreurs système
├─ Nettoyer les caches
└─ Vérifier la disponibilité

HEBDOMADAIRE:
├─ Vérifier les sauvegardes
├─ Analyser les journaux
└─ Nettoyer les fichiers temporaires

MENSUELLE:
├─ Maintenance de la base de données
├─ Vérifier l'espace disque
├─ Mettre à jour les dépendances
└─ Audit de sécurité
```

**Commandes de maintenance:**

```bash
# Nettoyer les caches
php artisan cache:clear

# Optimiser l'autoloader
php artisan optimize

# Vérifier la base de données
php artisan db:check

# Nettoyer les fichiers temporaires
php artisan files:clear
```

---

## 8. SÉCURITÉ {#securite}

### 8.1 Paramètres de sécurité

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture des paramètres de sécurité

**Options disponibles:**

```
AUTHENTIFICATION:
├─ ☑️ Activer l'authentification à 2 facteurs (2FA)
├─ ☑️ Exiger les mots de passe forts
├─ ☑️ Historique de mots de passe (nombre): 5
├─ ☑️ Expiration du mot de passe: 90 jours
└─ ☑️ Verrouillage après tentatives échouées: 5

SESSIONS:
├─ ☑️ Durée de session inactif: 30 minutes
├─ ☑️ Durée maximum session: 8 heures
└─ ☑️ 1 seule session par utilisateur

DONNÉES:
├─ ☑️ Chiffrement des mots de passe
├─ ☑️ Chiffrement des données sensibles
└─ ☑️ Suppression sécurisée (écrasement)

IP WHITELIST:
├─ Adresses IP autorisées
├─ (Laisser vide pour tous)
└─ Une IP par ligne

HTTPS:
├─ ☑️ Forcer HTTPS
├─ ☑️ HSTS (Strict-Transport-Security)
└─ Durée HSTS: 31536000 secondes
```

### 8.2 gérer les accès administrateur

**Super Admin actuels:**

```
Tableau des Super Admin:
├─ ID | Nom | Email | Créé | Dernière activité
└─ Actions: Révoquer accès
```

**Ajouter un Super Admin:**

```
1. Créer un utilisateur regular
2. Aller à [Paramètres] → [Admin]
3. Cliquez [➕ Ajouter Super Admin]
4. Sélectionnez l'utilisateur
5. Confirmer

⚠️ ATTENTION: Ne donnez cet accès qu'à des personnes de confiance!
```

**Révoquer un Super Admin:**

```
1. Ouvrez le profil Super Admin
2. Cliquez [❌ Révoquer accès admin]
3. Confirmation requise
4. Compte retourné à sa position précédente
```

### 8.3 Journal de sécurité

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du journal de sécurité

**Événements enregistrés:**

```
DATE | UTILISATEUR | ACTION | DÉTAIL | STATUS
-----|-------------|--------|--------|-------
26/02 | jean@... | LOGIN | Connexion réussie | ✅
26/02 | marie@... | CHMOD | Admin revoked | ✅
26/02 | système | BACKUP | Sauvegarde créée | ✅
26/02 | admin@... | DELETE | User removed | ✅
```

**Filtres:**
- Par date
- Par utilisateur
- Par type d'action
- Par statut

---

## 9. JOURNAL D'AUDIT {#audit}

### 9.1 Consulter les journaux

**Types de journaux:**

```
AUDIT:
└─ Toutes les actions des utilisateurs
   ├─ Créations
   ├─ Modifications
   ├─ Suppressions
   └─ Accès

ERREURS:
└─ Erreurs système et exceptions
   ├─ Erreurs PHP
   ├─ Erreurs base de données
   ├─ Erreurs réseau
   └─ Timeouts

ACCÈS:
└─ Connexions et authentifications
   ├─ Connexions réussies
   ├─ Tentatives échouées
   ├─ Accès refusés
   └─ 2FA

ACTIVITÉ:
└─ Activités globales
   ├─ Créations d'infrastructures
   ├─ Imports/exports
   ├─ Changements de permissions
   └─ Modifications système
```

### 9.2 Exporter les journaux

**Processus:**

1. Allez à **[Journaux]**
2. Sélectionnez le type
3. Appliquez les filtres
4. Cliquez **[📥 Exporter]**

**Formats disponibles:**
- CSV (Excel)
- JSON
- PDF

### 9.3 Archiver les journaux

**Automatique:** Tous les 30 jours

**Manuel:**
1. Allez à **[Journaux] → [Archive]**
2. Cliquez **[📦 Archiver]**
3. Sélectionnez la période
4. Confirmez

---

## 10. DÉPANNAGE ADMINISTRATION {#depannage-admin}

### Problème: Un utilisateur ne peut pas se connecter

**Solutions:**
1. Vérifiez que le compte est approuvé
2. Vérifiez que le compte est actif (non-bloqué)
3. Vérifiez que le mot de passe est correct
4. Réinitialisez le mot de passe si oublié
5. Consultez le journal de sécurité pour les tentatives échouées

---

### Problème: Une commune manque de données

**Solutions:**
1. Vérifiez les filtres appliqués
2. Vérifiez les permissions des utilisateurs
3. Consultez le journal d'audit pour les suppressions
4. Restaurez une sauvegarde si nécessaire

---

### Problème: Migration de données échouée

**Solutions:**
1. Vérifiez les logs d'erreur: `/storage/logs/`
2. Vérifiez le format du fichier import
3. Réessayez l'import
4. Contactez le support technique

---

### Problème: Performance dégradée

**Solutions:**
1. Nettoyez les caches: `php artisan cache:clear`
2. Optimisez la base de données: `php artisan db:optimize`
3. Consultez les journaux d'erreur
4. Vérifiez l'espace disque disponible
5. Vérifiez la mémoire RAM disponible

---

## CHECKLIST MAINTENANCE MENSUELLE

```
☐ Vérifier l'espace disque (minimum 20% libre)
☐ Vérifier les sauvegardes (au moins 5 existantes)
☐ Nettoyer les fichiers temporaires > 30 jours
☐ Analyser les journaux d'erreur
☐ Mettre à jour les dépendances
☐ Tester la restauration d'une sauvegarde
☐ Vérifier les utilisateurs inactifs (> 90 jours)
☐ Auditer les accès administrateur
☐ Optim la base de données
☐ Vérifier la sécurité SSL/TLS
```

---

**Document Version:** 1.0  
**Destiné à:** Administrateurs Système  
**Date:** Février 2026

---

*Pour toute question technique, consultez la documentation système ou contactez le support.*
