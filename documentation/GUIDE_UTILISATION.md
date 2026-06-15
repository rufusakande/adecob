# 📘 GUIDE D'UTILISATION - PLATEFORME ADECOB

## Système de Gestion des Infrastructures - Infrastructure Development & Management Platform

---

## 📑 TABLE DES MATIÈRES

1. [Introduction](#introduction)
2. [Vue d'ensemble de la plateforme](#vue-densemble)
3. [Authentification et connexion](#authentification)
4. [Les différents rôles utilisateurs](#roles-utilisateurs)
5. [Accueil et sélection de commune](#accueil-commune)
6. [Gestion des infrastructures](#gestion-infrastructures)
7. [Dashboard Mairie Agent](#dashboard-mairie-agent)
8. [Fonctionnalités avancées](#fonctionnalites-avancees)
9. [Guide de dépannage](#guide-depannage)
10. [FAQ](#faq)

---

## 1. INTRODUCTION {#introduction}

Bienvenue sur la plateforme **ADECOB** - Système de Gestion des Infrastructures.

Cette plateforme a été conçue pour permettre une gestion complète et structurée des infrastructures publiques. Elle facilite la collecte, le suivi et l'analyse des données relatives aux infrastructures, en tenant compte des différents niveaux de responsabilité et d'accès utilisateurs.

### Objectifs de la plateforme:
- 📊 Centraliser les données des infrastructures publiques
- 🔍 Permettre le suivi et l'analyse des infrastructures par commune
- 👥 Gérer les niveaux d'accès selon les rôles utilisateurs
- 📈 Générer des statistiques et rapports d'infrastructure
- 🔐 Sécuriser l'accès aux données sensibles

---

## 2. VUE D'ENSEMBLE DE LA PLATEFORME {#vue-densemble}

### Architecture générale

La plateforme est organisée autour de concepts clés:

```
ADECOB PLATEFORME
│
├─ AUTHENTIFICATION
│  └─ Connexion / Inscription
│
├─ PAGE D'ACCUEIL
│  └─ Sélection de la commune
│
├─ GESTION DES INFRASTRUCTURES
│  ├─ Consultation des données
│  ├─ Création de nouvelles entrées
│  ├─ Modification des entrées
│  └─ Suppression des entrées
│
├─ DASHBOARDS ET RAPPORTS
│  ├─ Dashboard Mairie Agent
│  ├─ Tableau de synthèse
│  └─ Statistiques par commune
│
└─ ADMINISTRATION
   ├─ Gestion des utilisateurs
   ├─ Configuration des communes
   └─ Codes d'accès
```

### Accès à la plateforme

**URL d'accès:** `http://127.0.0.1:8000/`

**Navigateurs recommandés:**
- ✅ Google Chrome (dernière version)
- ✅ Mozilla Firefox (dernière version)
- ✅ Microsoft Edge (dernière version)
- ✅ Safari (dernière version)

---

## 3. AUTHENTIFICATION ET CONNEXION {#authentification}

### 3.1 Page de connexion

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Insérez ici une capture d'écran de la page de connexion (login.blade.php)

### 3.2 Accès à la plateforme

**Deux options sont disponibles:**

#### Option 1: Connexion avec compte existant

1. Dirigez-vous vers la page d'accueil de la plateforme
2. Cliquez sur **"Connexion"** (si vous avez déjà un compte)
3. Entrez votre **adresse email**
4. Entrez votre **mot de passe**
5. Cliquez sur **"Se connecter"**

**Informations d'accès:**
- **Email:** adresse email enregistrée dans le système
- **Mot de passe:** votre mot de passe sécurisé

> ⚠️ **Important:** Vérifiez que les majuscules et minuscules sont correctes pour votre mot de passe

#### Option 2: Créer un nouveau compte (inscription)

1. Cliquez sur **"Créer un compte"** ou **"S'inscrire"**
2. Remplissez le formulaire d'inscription avec:
   - Nom complet
   - Adresse email
   - Mot de passe sécurisé
   - Confirmation du mot de passe

3. Acceptez les conditions d'utilisation
4. Cliquez sur **"S'inscrire"**

**Note:** Les nouveaux utilisateurs doivent être approuvés par un administrateur avant de pouvoir accéder à la plateforme complètement.

### 3.3 Récupération de mot de passe oublié

1. Sur la page de connexion, cliquez sur **"Mot de passe oublié?"**
2. Entrez votre **adresse email**
3. Cliquez sur **"Réinitialiser le mot de passe"**
4. Consultez votre email pour le lien de réinitialisation
5. Cliquez sur le lien fourni et créez un nouveau mot de passe

---

## 4. LES DIFFÉRENTS RÔLES UTILISATEURS {#roles-utilisateurs}

La plateforme dispose de **4 rôles utilisateurs distincts**, chacun ayant des permissions et des responsabilités différentes.

### 4.1 Super Administrateur (Super Admin)

**Description:** Gestionnaire système avec accès complet à la plateforme.

#### Permissions et accès:

| Fonctionnalité | Accès | Notes |
|---|---|---|
| Voir toutes les communes | ✅ Oui | Accès illimité |
| Consulter tous les données | ✅ Oui | Toutes les infrastructures |
| Créer des infrastructures | ✅ Oui | Pour toute commune |
| Modifier les données | ✅ Oui | Sans restriction |
| Supprimer les données | ✅ Oui | Sans restriction |
| Dashboard Synthèse | ✅ Oui | Vue globale |
| Exporter les données | ✅ Oui | Tous formats |
| Importer les données | ✅ Oui | Fichiers Excel/CSV |
| Gérer les utilisateurs | ✅ Oui | Créer, modifier, supprimer |
| Gérer les communes | ✅ Oui | Ajouter code d'accès |
| Gérer les accès | ✅ Oui | Codes d'accès des communes |
| Accéder sans code | ✅ Oui | Pas besoin de code d'accès |

#### Flux utilisateur Super Admin:

```
1. Connexion avec identifiants
2. Page d'accueil (liste des communes)
3. Sélectionne une commune
4. ✅ ACCÈS DIRECT (pas de code requis)
5. Accès à: infrastructures.index
   └─ Voir TOUTES les infrastructures de TOUTES les communes
   └─ Créer, modifier, supprimer
   └─ Consulter les statistiques complètes
```

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de l'interface Super Admin - Vue du tableau des infrastructures

---

### 4.2 Administrateur de Commune (Commune Admin)

**Description:** Responsable de la gestion des données pour une commune spécifique.

#### Permissions et accès:

| Fonctionnalité | Accès | Notes |
|---|---|---|
| Voir sa commune | ✅ Oui | Sa commune assignée uniquement |
| Consulter les données | ✅ Oui | Commune assignée uniquement |
| Créer des infrastructures | ✅ Oui | Pour sa commune |
| Modifier les données | ✅ Oui | Commune assignée |
| Supprimer les données | ✅ Oui | Commune assignée |
| Dashboard Synthèse | ✅ Oui | Données de sa commune |
| Exporter les données | ✅ Oui | Commune assignée |
| Importer les données | ✅ Oui | Commune assignée |
| Voir autres communes | ❌ Non | Bloqué |
| Gérer les utilisateurs | ❌ Non | Bloqué |
| Gérer les accès | ✅ Oui | Voir/Modifier le code de sa commune |
| Accès sans code | ✅ Oui | Pas besoin de code d'accès |

#### Flux utilisateur Commune Admin:

```
1. Connexion avec identifiants
2. Page d'accueil (liste des communes)
3. Clique sur sa commune assignée
4. ✅ ACCÈS DIRECT (pas de code requis)
5. Accès à: infrastructures.index
   └─ Voir infrastructures de SA COMMUNE uniquement
   └─ Créer, modifier, supprimer dans sa commune
   └─ Consulter les statistiques de sa commune
```

#### Gestion du code d'accès:

Le Commune Admin peut:
1. Accéder à la gestion du code d'accès
2. Consulter le code d'accès actuel de sa commune
3. Modifier le code d'accès si nécessaire

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de l'interface Commune Admin - Gestion du code d'accès

---

### 4.3 Agent Collecteur (Agent)

**Description:** Personne responsable de la collecte et de l'ajout des données d'infrastructure.

#### Permissions et accès:

| Fonctionnalité | Accès | Notes |
|---|---|---|
| Voir toutes les communes | ✅ Oui | Consultation |
| Consulter les données | ✅ Oui | Ses propres données |
| Créer des infrastructures | ✅ Oui | Avec code d'accès |
| Modifier ses données | ✅ Oui | Données qu'il a créées |
| Supprimer ses données | ✅ Oui | Données qu'il a créées |
| Modifier données autres | ❌ Non | Bloqué |
| Dashboard Synthèse | ✅ Oui | Ses propres statistiques |
| Exporter les données | ✅ Oui | Ses propres données |
| Importer les données | ❌ Non | Bloqué |
| Gérer les utilisateurs | ❌ Non | Bloqué |
| Accès sans code | ❌ Non | Code d'accès requis |

#### Flux utilisateur Agent:

```
1. Connexion avec identifiants
2. Page d'accueil (liste des communes)
3. Clique sur une commune
4. 🔐 FORMULAIRE CODE D'ACCÈS (champ obligatoire)
5. Entrez le code de la commune
6. ✅ ACCÈS ACCORDÉ si code correct
7. Accès à: infrastructures.index
   └─ Voir ses propres infrastructures
   └─ Créer nouvelles infrastructures
   └─ Modifier ses propres infrastructures
   └─ Consulter ses statistiques personnelles
```

#### Paramètres d'affichage:

En tant qu'agent, vous verrez:
- ✅ Les statistiques de vos propres données uniquement
- ✅ Le tableau avec vos propres infrastructures
- ❌ Les données d'autres agents ou communes

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de l'interface Agent - Formulaire code d'accès

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de l'interface Agent - Tableau des infrastructures personnelles

---

### 4.4 Utilisateur Public (Public User)

**Description:** Personne ayant accès aux statistiques publiques uniquement.

#### Permissions et accès:

| Fonctionnalité | Accès | Notes |
|---|---|---|
| Voir les communes | ✅ Oui | Liste des communes |
| Consulter les statistiques | ✅ Oui | Publiques uniquement |
| Voir les données détaillées | ❌ Non | Bloqué |
| Créer des infrastructures | ❌ Non | Bloqué |
| Modifier les données | ❌ Non | Bloqué |
| Supprimer les données | ❌ Non | Bloqué |
| Exporter les données | ❌ Non | Bloqué |
| Importer les données | ❌ Non | Bloqué |
| Gérer les utilisateurs | ❌ Non | Bloqué |
| Accès sans code | ✅ Oui | Pas besoin de code d'accès |

#### Flux utilisateur Public:

```
1. Connexion avec identifiants
2. Page d'accueil (liste des communes)
3. Clique sur une commune
4. ✅ ACCÈS DIRECT (pas de code requis)
5. Accès à: commune.dashboard (page spéciale public)
   └─ Voir les statistiques de la commune
   └─ Consulter les graphiques
   └─ AUCUN accès aux données détaillées
```

#### Statistiques visibles:

L'utilisateur public peut voir:
- 📊 Nombre total d'infrastructures
- 📈 Nombre d'infrastructures planifiées
- ✅ Infrastructures entretenues
- ⏳ Infrastructures à entretenir
- 🗺️ Répartition par commune, secteur, type
- ⚠️ Niveaux de priorité
- 💯 Progression des travaux

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de la page Public - Dashboard des statistiques

---

## 5. ACCUEIL ET SÉLECTION DE COMMUNE {#accueil-commune}

### 5.1 Page d'accueil

Après connexion, vous êtes dirigé vers la page d'accueil présentant toutes les communes disponibles.

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de la page d'accueil avec la liste des communes

### 5.2 Structure de la page d'accueil

**Titre et description:**
```
Bienvenue sur ADECOB
Infrastructure Development & Management Platform
Sélectionnez une commune pour accéder aux données des infrastructures
```

**Éléments affichés pour chaque commune:**

1. **Logo ou icône** - Image de la commune
2. **Nom de la commune** - Titre principal
3. **Statistiques rapides:**
   - Nombre d'infrastructures
   - Nombre d'agents assignés

4. **Bouton d'accès** - "Accéder →"

**Section utilisateur:**
- Affiche votre nom et email
- Affiche votre rôle personnel
- Bouton "Déconnexion"

### 5.3 Sélection d'une commune

#### Processus:

1. **Cliquez sur une carte de commune**
   - La carte devient plus grande et met en relief
2. **Vous êtes redirigé vers la page de sélection**
3. **La plateforme affiche ensuite:**
   - Le logo/image de la commune
   - Un message d'information adaptée à votre rôle
   - Un formulaire ou un bouton

#### Par rôle utilisateur:

**Super Admin ou Commune Admin:**
```
Vue du formulaire:
├─ Message: "Admin Commune - Vous avez accès complet"
├─ Champ: Code d'accès (pré-rempli, optionnel)
└─ Bouton: "Accéder aux données"
   └─ Redirection directe → Infrastructure.index
```

**Agent Collecteur:**
```
Vue du formulaire:
├─ Message: "Agent Collecteur - Entrez le code"
├─ Champ: Code d'accès (OBLIGATOIRE)
├─ Icône: Toggle pour voir/masquer le mot de passe
└─ Bouton: "Accéder aux données"
   └─ Si code correct → Infrastructure.index
   └─ Si code faux → Message d'erreur
```

**Utilisateur Public:**
```
Vue simplifiée:
├─ Message: "Mode Consultation - Statistiques publiques"
├─ Champ: Code d'accès (OPTIONNEL, grisé)
└─ Bouton: "Accéder aux données"
   └─ Redirection → Page commune.dashboard (statistiques)
```

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du formulaire de sélection pour Super Admin

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du formulaire de sélection pour Agent

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du formulaire de sélection pour Public User

---

## 6. GESTION DES INFRASTRUCTURES {#gestion-infrastructures}

### 6.1 Page principale des infrastructures

L'une des pages majeures de la plateforme où vous pouvez consulter, créer et gérer les infrastructures.

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de la page Infrastructure (vue complète)

### 6.2 Sections de la page

#### 6.2.1 En-tête et actions

```
├─ Titre: "Exploitation des Données des Infrastructures"
├─ Sous-titre: "Gestion complète des équipements publics et suivi des interventions"
│
└─ Boutons d'action (visibilité selon le rôle):
   ├─ [📥 Importer] - Importer un fichier Excel/CSV
   └─ [➕ Nouveau] - Créer une nouvelle infrastructure
```

> ℹ️ **Les utilisateurs publics ne voient ni le bouton Importer ni Nouveau**

#### 6.2.2 Statistiques générales

Une section avec des cartes affichant:

**Statistiques principales:**
- 📊 **Total Infrastructures** - Nombre total d'entrées
- 📈 **Planifiées** - Infrastructures en planification
- ⏳ **À Entretenir** - Nécessitant maintenance
- ✅ **Entretenues** - Maintenance terminée

**Répartitions détaillées (6 graphiques):**

1. **Par Commune** - Nombre d'infrastructures par commune
   - Affichable dans un dropdown scrollable
2. **Par Secteur** - Distribution par domaine (eau, santé, éducation...)
3. **Par Type** - Types d'infrastructures (borne fontaine, école, centre de santé...)
4. **Par État** - État de fonctionnement (Fonctionnel/Non fonctionnel)
5. **Par Dégradation** - Niveau de dégradation (Élevé/Moyen/Faible)
6. **Progression** - Barre de progression des travaux

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de la section statistiques

**Niveaux de priorité (4 cartes):**
- 🔴 **Très Urgent** (Score ≥ 4.2) - Couleur rouge
- 🟠 **Urgent** (Score 3.0-4.19) - Couleur orange/jaune
- 🔵 **Moyen** (Score 2.0-2.99) - Couleur bleu
- ⚪ **Faible** (Score < 2.0) - Couleur gris

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture des niveaux de priorité

#### 6.2.3 Filtres de recherche

La plateforme offre des filtres avancés pour trouver les infrastructures:

**Filtres disponibles** (Super Admin et Commune Admin):

```
┌─ Date début     (date picker)
├─ Date fin       (date picker)
├─ Commune        (dropdown)
├─ Arrondissement (dropdown)
├─ Village        (dropdown)
├─ Secteur        (dropdown)
├─ Type d'infra.  (dropdown)
├─ Année          (dropdown)
├─ État           (dropdown)
├─ Dégradation    (dropdown)
│
└─ Boutons:
   ├─ [🔍 Rechercher] - Appliquer les filtres
   └─ [🔄 Réinitialiser] - Effacer tous les filtres
```

> **⚠️ Note:** Les utilisateurs publics ne voient pas la section filtres

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de la section filtres

#### 6.2.4 Boutons d'export

Les utilisateurs autorisés peuvent exporter les données:

**Formats d'export:**
- 📊 **Excel** (.xlsx) - Tableau Excel avec formatage
- 📄 **PDF** - Document PDF formaté

**Filtrage à l'export:**
- Par année
- Par commune
- Par secteur

> **⚠️ Note:** Les utilisateurs publics ne voient pas les options d'export

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de la section export

#### 6.2.5 Tableau des données

Le tableau affiche toutes les infrastructures avec:

**Colonnes du tableau:**

| Colonne | Contenu |
|---------|---------|
| ☑️ | Checkbox pour sélection |
| ID | Identifiant unique |
| Enquêteur | Nom de la personne ayant collectionné |
| Téléphone | Contact de l'enquêteur |
| Date | Date de la collecte |
| Localisation | Commune, arrondissement, village |
| Secteur | Domaine (eau, santé, etc.) |
| Infrastructure | Type et nom |
| Caractéristiques | Année, matériaux, etc. |
| État | État de fonctionnement |
| Photos | Galerie de photos |
| Coordonnées | Latitude, Longitude, Altitude |
| Descriptions | Observations |
| Actions | Voir, Modifier, Supprimer |

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du tableau des infrastructures complet

**Code couleur des lignes:**

Les lignes du tableau sont colorées selon la priorité:

- 🔴 **Rouge** - Très Urgent (Score ≥ 4.2)
- 🟠 **Orange/Jaune** - Urgent (Score 3.0-4.19)
- 🔵 **Bleu** - Priorité Moyen (Score 2.0-2.99)
- ⚪ **Gris** - Priorité Faible (Score < 2.0)
- 🟢 **Vert** - Infrastructure planifiée

**Actions disponibles:**

Pour chaque infrastructure, vous pouvez:
- 👁️ **Voir** - Consulter les détails complets
- ✏️ **Modifier** - Éditer les informations
- 🗑️ **Supprimer** - Supprimer l'entrée

> ℹ️ **Permissions:**
> - Super Admin: peut modifier/supprimer ANY infrastructure
> - Commune Admin: peut modifier/supprimer dans sa commune
> - Agent: peut modifier/supprimer SES PROPRES infrastructures uniquement
> - Public User: aucun bouton d'action visible

---

### 6.3 Créer une nouvelle infrastructure

#### Accès:

Cliquez sur le bouton **[➕ Nouveau]** en haut de la page.

#### Formulaire de création:

Le formulaire est divisé en plusieurs sections:

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du formulaire de création - Vue générale

**Section 1: Informations de collecte**
```
├─ Date de collecte        (date picker)
├─ Nom de l'enquêteur       (texte - auto-rempli)
├─ Numéro de téléphone      (téléphone)
└─ Lieux de collecte        (localisation)
```

**Section 2: Localisation**
```
├─ Commune                  (dropdown)
├─ Arrondissement           (dropdown - multiple selection)
├─ Village                  (texte)
├─ Hameau                   (texte)
├─ Latitude                 (nombre)
├─ Longitude                (nombre)
├─ Altitude                 (nombre)
└─ Précision GPS            (nombre)
```

**Section 3: Caractéristiques de l'infrastructure**
```
├─ Secteur/Domaine          (dropdown)
├─ Type d'infrastructure    (dropdown)
├─ Nom de l'infrastructure  (texte)
├─ Année de réalisation     (année)
├─ Bailleur/Promoteur       (texte)
├─ Type de matériaux        (dropdown)
└─ Mode de gestion          (dropdown)
```

**Section 4: État et condition**
```
├─ État de fonctionnement   (dropdown: Fonctionnel/Non fonctionnel)
├─ Niveau de dégradation    (dropdown: Faible/Moyen/Élevé)
└─ Coût de réhabilitation   (dropdown: Faible/Moyen/Élevé)
```

**Section 5: Observations**
```
├─ Défectuosités relevées   (texte long)
├─ Mesures proposées        (texte long)
└─ Observations générales   (texte long)
```

**Section 6: Photos**
```
├─ Photo 1 (upload ou caméra)
├─ Photo 2 (upload ou caméra)
├─ Photo 3 (upload ou caméra)
└─ Photo 4 (upload ou caméra)
```

#### Validations:

Certains champs sont **obligatoires** (marqués d'un *):
- Nom de l'enquêteur *
- Commune *
- Type d'infrastructure *

#### Sauvegarde:

Cliquez sur le bouton **[💾 Enregistrer]** en bas du formulaire.

**Résultats:**
- ✅ Infrastructure créée avec succès
- ❌ Message d'erreur si des champs requis manquent

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du formulaire de création - Section localisation

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du formulaire de création - Section photos

---

### 6.4 Modifier une infrastructure existante

#### Processus:

1. Allez à la page Infrastructure
2. Trouvez l'infrastructure à modifier (utilisez les filtres si nécessaire)
3. Cliquez sur le bouton **[✏️ Modifier]** dans le tableau

#### Formulaire de modification:

Le formulaire est identique à celui de création, mais pré-rempli avec les données existantes.

#### Permissions:

- **Super Admin:** peut modifier toutes les infrastructures
- **Commune Admin:** peut modifier les infrastructures de sa commune
- **Agent:** peut modifier UNIQUEMENT les infrastructures qu'il a créées (identifiées par son nom)
- **Public User:** aucun accès à la modification

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du formulaire de modification

---

### 6.5 Consulter les détails d'une infrastructure

#### Processus:

1. Allez à la page Infrastructure
2. Cliquez sur le bouton **[👁️ Voir]** ou cliquez sur l'ID de l'infrastructure
3. Une page de détails s'ouvre

#### Informations affichées:

- Tous les champs de l'infrastructure
- Photos en galerie avec zoom
- Coordonnées GPS
- Historique (si disponible)
- Informations de l'enquêteur

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de la page de détails d'une infrastructure

---

### 6.6 Supprimer une infrastructure

#### Processus:

1. Allez à la page Infrastructure
2. Trouvez l'infrastructure
3. Cliquez sur le bouton **[🗑️ Supprimer]**
4. Une confirmation s'affiche

#### Confirmation:

```
⚠️ ATTENTION - Suppression irréversible

Êtes-vous sûr de vouloir supprimer cette infrastructure?
Cette action ne peut pas être annulée.

[Annuler]  [Supprimer définitivement]
```

5. Cliquez sur **[Supprimer définitivement]**

#### Permissions:

- **Super Admin:** peut supprimer toutes les infrastructures
- **Commune Admin:** peut supprimer dans sa commune
- **Agent:** peut supprimer SES PROPRES infrastructures
- **Public User:** aucun accès

#### Après suppression:

```
✅ Infrastructure supprimée avec succès
```

---

### 6.7 Importer des données (Excel/CSV)

#### Accès:

Cliquez sur le bouton **[📥 Importer]**

#### Modal d'importation:

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du modal d'importation

**Étapes:**

1. Cliquez sur **"Sélectionner un fichier"**
2. Choisissez un fichier .xlsx, .xls ou .csv
3. Cliquez sur **"Importer"**

**Format du fichier:**
- Les colonnes doivent correspondre aux champs de l'infrastructure
- La première ligne doit contenir les en-têtes
- Format accepté: Excel (.xlsx, .xls) ou CSV

**Validations:**
- Vérification des colonnes obligatoires
- Vérification des types de données
- Détection des doublons

**Résultats:**
- ✅ X infrastructures importées avec succès
- ⚠️ Y avertissements (doublons détectés)
- ❌ Z erreurs (données invalides)

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture des résultats d'importation

---

### 6.8 Exporter les données

#### Processus:

1. Utilisez la section **"Exporter les Données"**
2. Sélectionnez les filtres (optionnel):
   - Année
   - Commune
   - Secteur
3. Cliquez sur le format souhaité

#### Formats d'export:

**Format Excel:**
```
[📊 Excel]
└─ Créé un fichier .xlsx avec:
   ├─ Toutes les colonnes d'infrastructure
   ├─ Formatage avec couleurs
   ├─ En-têtes gelés
   └─ Largeur des colonnes ajustées
```

**Format PDF:**
```
[📄 PDF]
└─ Créé un document PDF avec:
   ├─ Layout tabulaire
   ├─ En-têtes et pied de page
   ├─ Numérotation des pages
   └─ Logo de la commune
```

**Nom du fichier:**
```
infrastructures_[commune]_[year].xlsx
infrastructures_[commune]_[year].pdf
```

#### Export personnalisé:

Vous pouvez aussi sélectionner des lignes spécifiques dans le tableau:
1. Cochez les cases (☑️) des infrastructures à exporter
2. Cliquez sur **[📥 Exporter la sélection]**

---

## 7. DASHBOARD MAIRIE AGENT {#dashboard-mairie-agent}

### 7.1 Accès au dashboard

Depuis le menu principal, vous pouvez accéder au dashboard Mairie Agent qui offre une vue synthétisée des données.

> **Accessible à:** Super Admin, Commune Admin, Agents (avec données filtrées)

### 7.2 Contenu du dashboard

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du dashboard Mairie Agent - Vue complète

**Sections du dashboard:**

#### Section 1: Carte des priorités

```
Affichage visuel des infrastructures par priorité:
├─ Triangle rouge: Très urgent
├─ Carré orange: Urgent
├─ Cercle bleu: Moyen
└─ Losange gris: Faible
```

#### Section 2: Statistiques de priorité

```
Compteurs des niveaux:
├─ Très Urgent: X infrastructures
├─ Urgent: X infrastructures
├─ Moyen: X infrastructures
└─ Faible: X infrastructures
```

#### Section 3: Données de travail

```
Tableau résumé des infrastructures avec:
├─ Identifiant
├─ Commune
├─ Type
├─ État
├─ Priorité
└─ Actions
```

#### Section 4: Statistiques

```
Graphiques et chiffres:
├─ Total par commune
├─ Total par secteur
├─ État des infrastructures
├─ Dégradation
└─ Évolution temporelle
```

### 7.3 Filtres appliqués au dashboard

Le dashboard applique automatiquement les filtres selon votre rôle:

**Super Admin:**
- Voit TOUTES les données
- Filtre par commune optionnel

**Commune Admin:**
- Voit d'abord les données de SA commune
- Peut filtrer mais reste limité à SA commune

**Agent:**
- Voit UNIQUEMENT ses propres données
- Les statistiques reflètent ses créations uniquement

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du dashboard - Section statistiques

---

## 8. FONCTIONNALITÉS AVANCÉES {#fonctionnalites-avancees}

### 8.1 Sélection multiple d'infrastructures

Dans le tableau, vous pouvez sélectionner plusieurs lignes:

```
1. Cochez les cases (☑️) des infrastructures
2. Une barre d'actions s'affiche:
   ├─ [📥 Exporter sélection]
   ├─ [✏️ Modifier groupe] (si applicable)
   └─ [🗑️ Supprimer groupe] (avec confirmation)
```

### 8.2 Galerie de photos

Les photos des infrastructures peuvent être:

- **Consultées** en cliquant sur l'image
- **Agrandies** avec un zoom au survol
- **Téléchargées** directement depuis la galerie
- **Supprimées** si vous avez les permissions

**Formats supportés:**
- JPG/JPEG
- PNG
- GIF
- BMP

**Taille maximale:** 10 MB par image

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture de la galerie de photos

### 8.3 Formulaire de recherche avancée

Utilisez l'onglet **"Recherche Avancée"** pour:

```
├─ Recherche par texte (nom, commune, etc.)
├─ Recherche par date (plage de dates)
├─ Recherche par localisation (GPS)
├─ Filtres composés (ET / OU)
└─ Sauvegarde des recherches
```

### 8.4 Historique des modifications

Certains utilisateurs peuvent consulter:

- Qui a créé une infrastructure
- Qui a modifié les données
- Quand les modifications ont eu lieu
- Quelles valeurs ont changé

> **Accessible à:** Super Admin, Commune Admin (dans leur commune)

### 8.5 Flux de travail de gestion d'infrastructure

```
1. CRÉATION
   └─ Agent/Admin crée une nouvelle entrée
2. VÉRIFICATION
   └─ Commune Admin vérifie les données
3. ANALYSE
   └─ Super Admin analyticien évalue les données
4. ACTION
   └─ Décision sur le travail à faire
5. EXÉCUTION
   └─ Mise à jour de l'état
6. VALIDATION
   └─ Vérification de la complétude
```

---

## 9. GUIDE DE DÉPANNAGE {#guide-depannage}

### Problème: Code d'accès incorrect

**Symptôme:** Message "Code d'accès incorrect"

**Solutions:**
1. Vérifiez les majuscules/minuscules (sensible à la casse)
2. Assurez-vous d'avoir supprimé les espaces inutiles
3. Vérifiez avec l'administrateur de la commune que le code est correct
4. Réinitialisez le code si oublié

> **📸 ESPACE RÉSERVÉ POUR CAPTURE D'ÉCRAN:**
> 
> Capture du message d'erreur de code incorrect

---

### Problème: Impossible de modifier une infrastructure

**Symptôme:** Bouton Modifier grisé ou inaccessible

**Solutions:**
1. Vérifiez votre rôle utilisateur
2. Vérifiez si vous avez créé cette infrastructure (Agents)
3. Vérifiez si l'infrastructure appartient à votre commune (Commune Admin)
4. Contactez le Super Admin si vous devriez avoir accès

**Permissions requises:**
- ✅ Super Admin: accès à tout
- ✅ Commune Admin: sa commune uniquement
- ✅ Agent: ses propres infrastructures
- ❌ Public User: aucun accès

---

### Problème: L'upload de photos échoue

**Symptôme:** Erreur lors du téléchargement de photo

**Solutions:**
1. Vérifiez le format (JPG, PNG, GIF, BMP acceptés)
2. Vérifiez la taille (max 10 MB)
3. Vérifiez votre connexion internet
4. Essayez un autre navigateur
5. Videz le cache du navigateur

---

### Problème: L'import Excel échoue

**Symptôme:** Erreur lors de l'importation du fichier Excel

**Solutions:**
1. Vérifiez le format du fichier (.xlsx, .xls, .csv)
2. Vérifiez que les colonnes correspondent à la structure attendue
3. Vérifiez que les types de données sont corrects
4. Consultez le fichier d'exemple fourni par l'administrateur

**Format requis pour l'import:**
```
Colonne A: ID                    (nombre)
Colonne B: Commune               (texte)
Colonne C: Enquêteur             (texte)
Colonne D: Type Infrastructure   (texte)
[... autres colonnes selon la structure]
```

---

### Problème: Les statistiques ne s'affichent pas

**Symptôme:** Cartes statistiques vides ou "Loading..." permanent

**Solutions:**
1. Rafraîchissez la page (F5 ou Ctrl+R)
2. Videz le cache du navigateur
3. Désactivez les extensions du navigateur (bloqueurs de pubs, etc.)
4. Changez de navigateur pour tester
5. Attendez quelques minutes (grande base de données = traitement lent)

---

### Problème: Vous n'avez pas les permissions attendues

**Symptôme:** Accès refusé à une section

**Solutions:**
1. Vérifiez votre rôle utilisateur (en bas à droite)
2. Contactez l'administrateur pour un changement de rôle
3. Vérifiez que votre compte est approuvé
4. Déconnectez-vous et reconnectez-vous

---

### Problème: Mot de passe oublié ou perdu

**Symptôme:** Impossible de se connecter

**Solutions:**
1. Cliquez sur "Mot de passe oublié?" sur la page de connexion
2. Entrez votre adresse email
3. Consultez votre email pour le lien de réinitialisation
4. Créez un nouveau mot de passe
5. Essayez de vous connecter à nouveau

Si vous ne recevez pas l'email:
- Vérifiez les spams/indésirables
- Attendez quelques minutes
- Contactez l'administrateur système

---

## 10. FAQ (QUESTIONS FRÉQUEMMENT POSÉES) {#faq}

### Q: Dois-je un code d'accès?

**R:** Ça dépend de votre rôle:
- ❌ Super Admin: Non
- ❌ Commune Admin: Non
- ✅ Agent: Oui, requis
- ❌ Public User: Non

---

### Q: Puis-je modifier les données d'autres agents?

**R:** Ça dépend de votre rôle:
- ✅ Super Admin: Oui, tout
- ✅ Commune Admin: Oui, dans sa commune
- ❌ Agent: Non, que vos propres données
- ❌ Public User: Non

---

### Q: Comment puis-je changer mon rôle?

**R:** Vous ne pouvez pas changer votre rôle vous-même. Contactez l'administrateur système qui gère les rôles utilisateurs.

---

### Q: Avec quel fichier puis-je importer?

**R:** Vous pouvez importer:
- 📊 Fichiers Excel: .xlsx, .xls
- 📄 Fichiers CSV: .csv

Assurez-vous que la structure correspond.

---

### Q: Puis-je exporter sans sélectionner?

**R:** Oui, l'export sans sélection exporte TOUTES les données filtrées actuellement. Vous pouvez appliquer des filtres avant l'export pour réduire les données.

---

### Q: Mes données sont-elles sécurisées?

**R:** Oui:
- 🔐 Tous les transmissions sont chiffrées (HTTPS)
- 🔑 Accès contrôlé par rôles et permissions
- 📋 Système sauvegarde régulièrement
- 👁️ Les utilisateurs publics ne voient que les stats

---

### Q: Combien de temps les données sont-elles conservées?

**R:** Les données sont conservées indéfiniment dans la base de données jusqu'à suppression manuelle par un administrateur.

---

### Q: Puis-je télécharger les photos?

**R:** Oui, depuis la galerie de photos dans la page détails d'une infrastructure.

---

### Q: Quel est le format requis pour les coordonnées GPS?

**R:** Les coordonnées doivent être en:
- Latitude: -90 à 90 (décimal)
- Longitude: -180 à 180 (décimal)

Exemple: Latitude 48.8566, Longitude 2.3522

---

### Q: Comment commencer une nouvelle saisie?

**R:** 
1. Allez à la page Infrastructure
2. Cliquez sur le bouton **[➕ Nouveau]**
3. Remplissez le formulaire
4. Cliquez sur **[💾 Enregistrer]**

---

## CONCLUSION

Cette plateforme ADECOB a été conçue pour faciliter la gestion et le suivi des infrastructures publiques. En comprenant les différents rôles, permissions et fonctionnalités, vous pourrez utiliser la plateforme efficacement.

**Pour toute question supplémentaire:**
- 📧 Contactez: [email du support]
- 📞 Téléphone: [numéro de support]
- 🕐 Heures: [heures de disponibilité]

---

## ANNEXES

### Annexe A: Tableau des permissions complet

| Fonctionnalité | Super Admin | Commune Admin | Agent | Public User |
|---|:---:|:---:|:---:|:---:|
| Voir sa commune | ✅ | ✅ | ✅ | ✅ |
| Voir autres communes | ✅ | ❌ | ✅ | ✅ |
| Consulter données | ✅ | ✅ | ✅ (siennes) | ❌ |
| Voir statistiques | ✅ | ✅ | ✅ | ✅ |
| Créer infrastructure | ✅ | ✅ | ✅ | ❌ |
| Modifier infrastructure | ✅ | ✅ (siennes) | ✅ (siennes) | ❌ |
| Supprimer infrastructure | ✅ | ✅ (siennes) | ✅ (siennes) | ❌ |
| Exporter données | ✅ | ✅ | ✅ (siennes) | ❌ |
| Importer données | ✅ | ✅ | ❌ | ❌ |
| Accès sans code | ✅ | ✅ | ❌ | ✅ |
| Gérer utilisateurs | ✅ | ❌ | ❌ | ❌ |
| Gérer communes | ✅ | ❌ | ❌ | ❌ |

---

### Annexe B: Glossaire des termes

**Infrastructure:** Équipement ou établissement public (école, centre de santé, borne fontaine, route, etc.)

**Commune:** Division administrative du pays

**Enquêteur/Agent:** Personne responsable de la collecte des données d'infrastructure

**Commune Admin:** Administrateur responsable d'une commune spécifique

**Super Admin:** Administrateur système avec accès complet

**Priorité:** Niveau d'urgence basé sur l'état et la dégradation

**Code d'accès:** Mot de passe temporaire pour accéder à une commune

**Secteur/Domaine:** Catégorie d'infrastructure (eau, santé, éducation, etc.)

---

### Annexe C: Raccourcis clavier

| Raccourci | Action |
|-----------|--------|
| F5 | Rafraîchissement de la page |
| Ctrl+P | Imprimer |
| Ctrl+S | Sauvegarder (si applicable) |
| Escape | Fermer modal/dialogue |
| Tab | Navigation entre champs |

---

**Document Version:** 1.0  
**Date de création:** Février 2026  
**Auteur:** Équipe ADECOB  
**Dernier mise à jour:** Février 2026

---

*Ce guide est sujet à des modifications. Consultez l'authentification pour la dernière version.*
