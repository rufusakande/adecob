# GUIDE DE L'UTILISATEUR — AGENT COLLECTEUR
### Plateforme ARMANI — Gestion des infrastructures des communes du Borgou
**Version :** 1.0 — **Date :** 19/08/2026

---

## 1. Introduction

Ce guide explique, pas à pas, l'utilisation de la plateforme **ARMANI** pour le profil **Agent collecteur**. L'agent est l'utilisateur de **terrain** : il **saisit les infrastructures** (avec photos et géolocalisation) et **met à jour les infrastructures qui lui sont affectées**.

> 🖼️ **Notation** : les mentions `[Capture d'écran : ...]` indiquent où insérer une capture d'écran.

---

## 2. Connexion à la plateforme

1. Allez sur **https://armani.bj** et cliquez sur **« Se connecter »** ;
2. Saisissez votre **email** et votre **mot de passe** ;
3. Cliquez sur **« Se connecter »** ;
   - [Capture d'écran : formulaire de connexion]
4. En haut à droite, votre **avatar** affiche votre nom et le badge **« Agent »**.

> ℹ️ **Astuce mobile** : vous pouvez **installer l'application** sur votre téléphone (icône « Installer ») pour un accès rapide et pour travailler **hors-ligne** dans les zones sans connexion.

---

## 3. Vue d'ensemble de l'interface

### 3.1 Le menu principal (Agent)
| Menu | Rôle |
|---|---|
| **Mes infrastructures** | Liste des infrastructures que vous avez saisies **ou** qui vous sont affectées |
| **Ajouter** | Créer une nouvelle infrastructure sur le terrain |
| **Mes affectations** | Les infrastructures affectées par l'admin et à mettre à jour |
| **Fiches hors-ligne** | Fiches saisies sans connexion, à synchroniser |

### 3.2 La barre d'onglets mobile
Onglets : **Mes fiches**, **Ajouter**, **Affectées**, **Hors-ligne**.
- [Capture d'écran : barre d'onglets mobile]

---

## 4. Mes infrastructures

Menu **« Mes infrastructures »**.
- [Capture d'écran : liste de mes infrastructures]
Cette page liste :
- Les infrastructures que **vous avez saisies** ;
- Les infrastructures **affectées** à vous (à mettre à jour).

Pour chaque infrastructure : **ID, nom, commune/village, secteur, type, état, photos**, et boutons **« Voir »**, **« Modifier »**.

Les **filtres** en haut permettent de rechercher (commune, secteur, type, état…) puis cliquez **« Filtrer »**.

---

## 5. Ajouter une infrastructure (nouvelle saisie)

Menu **« Ajouter »** (ou **« + Ajouter »**).

- [Capture d'écran : formulaire en 3 étapes]

### Étape 1 — Identification
Renseignez :
- **Nom de l'infrastructure** ;
- **Secteur** (Éducation, Eau potable, Santé, Marché…) ;
- **Type d'infrastructure** ;
- **Commune, Arrondissement, Village** ;
- **Année de réalisation**, **bailleur**, **matériaux** (si connus).
Cliquez **« Suivant »**.

### Étape 2 — Géolocalisation (GPS)
- **« Me localiser »** : la position GPS est captée automatiquement ;
  - 📍 Vérifiez la **précision** (< 5 m idéalement) et l'**altitude** ;
- La position s'affiche sur la **carte** (puce verte) — vous pouvez ajuster en cliquant sur la carte ;
- Si la localisation automatique échoue, **cliquez directement sur la carte** à l'emplacement de l'infrastructure.
Cliquez **« Suivant »**.

### Étape 3 — État & photos
- **État de fonctionnement** (bon, moyen, dégradé…) ;
- **Niveau de dégradation** ;
- **Photos** : deux options :
  - Onglet **« Importer des Photos »** : choisissez des photos dans votre appareil ;
  - Onglet **« Prendre des Photos »** : **« Activer Caméra »** puis **« Prendre Photo »** (jusqu'à 4 photos au total) ;
  - Les photos prises/importées s'affichent dans **« Toutes les Photos »** ; vous pouvez **supprimer** une photo avant envoi.
Cliquez **« Soumettre la fiche »**.

- [Capture d'écran : section photos avec les deux onglets]

✅ Votre fiche est soumise. Elle sera **validée par l'administrateur** avant d'être exploitée.

---

## 6. Mes affectations (mise à jour d'infrastructures)

Menu **« Mes affectations »**.
- [Capture d'écran : page « Mes infrastructures affectées »]

Un administrateur vous a **affecté** certaines infrastructures pour que vous **mettiez à jour leurs données** sur le terrain.

### 6.1 Voir les infrastructures affectées
La liste affiche les infrastructures affectées et leur **statut** :
- **À traiter** (affectée) : vous devez la mettre à jour ;
- **Soumise** : votre mise à jour a été envoyée, en attente de validation ;
- **Validée** : acceptée (vous n'y avez plus accès) ;
- **Rejetée** : à corriger (voir le motif).

### 6.2 Mettre à jour une infrastructure affectée
1. Ouvrez l'infrastructure (bouton **« Modifier »** ou **« Voir »**) ;
2. **Corrigez / complétez** les informations (état, photos, localisation…) ;
3. **Enregistrez** la mise à jour → elle est **soumise automatiquement** à l'administrateur.

### 6.3 Après validation
- ✅ **Validée** : votre travail est terminé pour cette infrastructure ;
- ❌ **Rejetée** : lisez le **motif** de rejet, corrigez, et **resoumettez**.

> ⚠️ Vous ne pouvez pas **supprimer** une infrastructure qui vous est affectée : seul l'administrateur en a le droit.

---

## 7. Fiches hors-ligne (sans connexion)

Menu **« Fiches hors-ligne »**.
- [Capture d'écran : page des fiches hors-ligne]

Quand vous êtes **sans connexion** (zone rurale) :
1. Remplissez la fiche d'infrastructure sur votre téléphone ;
2. La fiche est **enregistrée localement** (dans votre appareil) ;
3. Dès que vous avez **à nouveau une connexion**, la fiche est **synchronisée** automatiquement avec le serveur ;
4. Vous pouvez la consulter dans cette page.

---

## 8. Glossaire des boutons et icônes

| Élément | Signification |
|---|---|
| **+ Ajouter** | Créer une nouvelle infrastructure |
| 🔍 **Voir** (œil) | Consulter la fiche |
| ✏️ **Modifier** | Mettre à jour |
| 📍 **Me localiser** | Capturer la position GPS |
| 📸 **Prendre Photo** | Photographier avec la caméra |
| 📁 **Importer des Photos** | Choisir des photos existantes |
| ☁️ **Fiches hors-ligne** | Saisies stockées en local |
| 🚮 **Supprimer** (photo) | Retirer une photo avant envoi |

---

## 9. Déconnexion et bonnes pratiques

- **Déconnexion** : avatar → **« Déconnexion »** ;
- Sur le terrain : vérifiez la **précision GPS** avant de valider une localisation ;
- Prenez **au moins une photo** par infrastructure ;
- Remplissez le maximum de champs (bailleur, année de réalisation si connus) ;
- Si une infrastructure affectée est **rejetée**, lisez le **motif** et corrigez rapidement ;
- Utilisez le **mode hors-ligne** quand la connexion est mauvaise.

---

## 10. Dépannage rapide

| Problème | Solution |
|---|---|
| Mot de passe oublié | « Mot de passe oublié ? » → lien reçu par email |
| GPS imprécis | Placez-vous devant l'infrastructure, réessayez ; ou cliquez sur la carte |
| Caméra ne s'ouvre pas | Autorisez l'accès à la caméra dans le navigateur (⚠️ nécessite HTTPS) |
| Pas de connexion | Utilisez les **fiches hors-ligne**, synchronisez plus tard |
| Fiche rejetée | Consultez le **motif** dans « Mes affectations » et corrigez |
| Besoin d'aide | Contactez l'administrateur de votre commune |

---
*Document de référence — Agent collecteur — Plateforme ARMANI.*
