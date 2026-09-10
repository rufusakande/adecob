# GUIDE DE L'UTILISATEUR — ADMINISTRATEUR DE COMMUNE
### Plateforme ARMANI — Gestion des infrastructures des communes du Borgou
**Version :** 1.0 — **Date :** 19/08/2026

---

## 1. Introduction

Ce guide explique, pas à pas, l'utilisation de la plateforme **ARMANI** pour le profil **Administrateur de Commune**. L'admin de commune gère **sa commune uniquement** : les infrastructures, les agents, les affectations, la planification et la validation des données.

> 🖼️ **Notation** : les mentions `[Capture d'écran : ...]` indiquent où insérer une capture d'écran.

---

## 2. Connexion à la plateforme

1. Allez sur **https://armani.bj** et cliquez sur **« Se connecter »** ;
2. Saisissez votre **email** et votre **mot de passe** ;
3. Cliquez sur **« Se connecter »** ;
   - [Capture d'écran : formulaire de connexion]
4. **Double authentification (MFA)** : un **code à 6 chiffres** est envoyé à votre email → saisissez-le et cliquez **« Vérifier »**.
   - [Capture d'écran : page MFA]
5. En haut à droite, votre **avatar** affiche votre nom et le badge **« Admin Commune »**.

---

## 3. Vue d'ensemble de l'interface

### 3.1 Le menu principal (Admin Commune)
| Menu | Rôle |
|---|---|
| **Tableau de bord** | Statistiques de **votre commune** |
| **Infrastructures** ▾ | **Mes infrastructures** (liste), **Infrastructures planifiées** (planification) |
| **Agents de la commune** | Voir/valider les agents de votre commune (badge orange = demandes en attente) |
| **Affectations** | Affecter des infrastructures aux agents, examiner leurs soumissions |
| **Fiches hors-ligne** | Consulter/synchroniser les fiches saisies hors-ligne |

### 3.2 La barre d'onglets mobile
Onglets : **Tableau**, **Infras**, **Affect.**, **Planifiées**, **Agents**.
- [Capture d'écran : barre d'onglets mobile]

---

## 4. Le tableau de bord de la commune

- [Capture d'écran : tableau de bord commune]
Affiche les statistiques **de votre commune uniquement** :
- Nombre d'infrastructures (total, par statut) ;
- Répartition par **arrondissement/village** ;
- Répartition par **secteur** et **type** ;
- **État de fonctionnement** et **niveau de dégradation** ;
- **Priorités** (urgent, élevée, moyenne, faible) ;
- Accès rapide aux fonctionnalités.

---

## 5. Gestion des infrastructures de la commune

### 5.1 Mes infrastructures (liste)
Menu **Infrastructures → Mes infrastructures**.
- [Capture d'écran : liste des infrastructures]
La liste affiche : **ID, nom, commune/village, secteur, type, état, actions**.

Les **filtres** permettent de rechercher par **commune, arrondissement, village, secteur, type, état, niveau de dégradation** et par **texte**. Boutons **« Filtrer »** / **« Réinitialiser »**.

| Bouton | Rôle |
|---|---|
| **Voir** | Fiche complète (localisation, GPS, photos, état) |
| **Modifier** | Mettre à jour la fiche |
| **Planifier** | Ajouter/modifier une intervention planifiée |
| **Marquer réhabilitée** | Indiquer que l'infrastructure a été réhabilitée |
| **Exporter** | Excel/PDF |

### 5.2 Créer une infrastructure
1. Cliquez sur **« + Ajouter »** ;
2. Remplissez les **3 étapes** :
   - **Identification** : nom, secteur, type, arrondissement, village… ;
   - **Géolocalisation** : bouton **« Me localiser »** (GPS, précision < 5 m, altitude) + carte ;
   - **État & photos** : état constaté + photos (import ou **caméra**).
3. Cliquez **« Soumettre la fiche »**.
- [Capture d'écran : formulaire de création]
- La fiche est soumise pour validation.

### 5.3 Validation des fiches
- Les fiches en attente apparaissent avec une action **Valider** / **Rejeter** (avec motif) ;
- Seules les fiches **validées** sont utilisables pour les analyses et la planification.

---

## 6. Planification (annuelle et triennale)

Menu **Infrastructures → Infrastructures planifiées**.

- [Capture d'écran : page des infrastructures planifiées]

### 6.1 Le tableau
Colonnes : **ID, infrastructure, commune/village, type, interventions planifiées, plan annuel (budget + T1→T4), plan triennal (coût + répartition par année réelle), priorité, statut d'exécution, coût total, prochaine échéance, actions**.

### 6.2 Planifier une infrastructure
1. Dans la liste, cliquez **« Planifier »** sur une infrastructure (ou **« Modifier »** dans les planifiées) ;
2. Renseignez :
   - **Nature de l'intervention** (type de travail, date, description) ;
   - **Budget & acteurs** (coût, période, acteurs, financement) ;
   - **Fiche TRIENNALE** (unité, quantité, coût unitaire, répartition Année 1/2/3, priorité) ;
   - **Fiche ANNUELLE** (budget annuel, trimestres T1→T4, statut d'exécution) ;
   - **Observations** ;
3. Cliquez **« Enregistrer la planification »**.
- [Capture d'écran : formulaire de planification]

### 6.3 Exporter les plans
| Bouton | Rôle |
|---|---|
| **Exporter Plan Triennal (sélection / tous filtrés)** | PDF plan triennal (modèle MDGL) |
| **Exporter Plan Annuel (sélection / tous filtrés)** | PDF plan annuel (modèle MDGL) |
| **Excel (sélection / tous filtrés)** | Export Excel |

---

## 7. Gestion des agents de la commune

Menu **« Agents de la commune »**.
- [Capture d'écran : liste des agents]
Affiche les **agents** de votre commune avec leur statut. Un badge orange indique les demandes **en attente d'approbation**. Actions :
- **Approuver** une inscription d'agent (✓) ;
- **Rejeter** avec motif ;
- **Modifier** le profil d'un agent ;
- **Promouvoir / rétrograder** (gérer les droits d'agent-admin).

---

## 8. Affectations des infrastructures aux agents

Menu **« Affectations »**.
- [Capture d'écran : page des affectations]

### 8.1 Objectif
Affecter des infrastructures à vos agents pour qu'ils **mettent à jour** leurs données sur le terrain.

### 8.2 Affecter des infrastructures
1. **Section agents** : liste des agents avec le **nombre d'infrastructures affectées** ;
2. **Section infrastructures** :
   - Filtrez (commune, arrondissement, village, type) ;
   - **Cochez** les infrastructures (badge **« Déjà affectée »** si déjà attribuée) ;
   - Le **compteur** indique votre sélection ;
3. Choisissez l'**agent** destinataire ;
4. Cliquez **« Affecter »** → toutes les infrastructures sélectionnées sont affectées à cet agent.

### 8.3 Examiner les soumissions des agents
- L'agent **soumet** ses mises à jour → statut **« soumise »** ;
- Vous **examinez** chaque soumission :
  - **Valider** → la mise à jour est acceptée ; l'agent **perd l'accès** à l'infrastructure ;
  - **Rejeter** → indiquez le **motif** ; l'agent corrige et resoumet ;
- Vous pouvez aussi **révoquer** une affectation en cours.

---

## 9. Fiches hors-ligne

Menu **« Fiches hors-ligne »**.
- [Capture d'écran : page des fiches hors-ligne]
Permet de **consulter** et **synchroniser** les fiches saisies hors-ligne par les agents de la commune.

---

## 10. Glossaire des boutons et icônes

| Élément | Signification |
|---|---|
| 🔍 **Voir** (œil) | Afficher le détail |
| ✏️ **Modifier** | Éditer |
| 🗑️ **Supprimer** | Supprimer (confirmation) |
| 📅 **Planifier** | Gérer la planification |
| ✓ **Valider / Approuver** | Accepter |
| ✗ **Rejeter** | Refuser (avec motif) |
| 📄 **Exporter PDF** | Générer un PDF |
| 📊 **Exporter Excel** | Télécharger en Excel |
| 🤝 **Affecter** | Attribuer des infrastructures à un agent |

---

## 11. Déconnexion et bonnes pratiques

- **Déconnexion** : avatar → **« Déconnexion »** ;
- Approuvez rapidement les **inscriptions d'agents** de votre commune ;
- **Validez régulièrement** les fiches et les **soumissions** des agents ;
- Mettez à jour la **planification** (annuelle/triennale) pour suivre les budgets ;
- Protégez votre mot de passe et votre code MFA.

---

## 12. Dépannage rapide

| Problème | Solution |
|---|---|
| Mot de passe oublié | « Mot de passe oublié ? » → lien reçu par email |
| Code MFA non reçu | Vérifiez les spams, cliquez « Renvoyer un code » |
| Un agent ne peut pas se connecter | Vérifiez qu'il est **approuvé** dans « Agents de la commune » |
| Affectation impossible | L'infrastructure est peut-être **déjà affectée** (badge) ou en cours de validation |
| Besoin d'aide | Contactez le super admin / support technique |

---
*Document de référence — Administrateur de Commune — Plateforme ARMANI.*
