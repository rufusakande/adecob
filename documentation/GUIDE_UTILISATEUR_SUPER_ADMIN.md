# GUIDE DE L'UTILISATEUR — SUPER ADMINISTRATEUR
### Plateforme ARMANI — Gestion des infrastructures des communes du Borgou
**Version :** 1.0 — **Date :** 19/08/2026

---

## 1. Introduction

Ce guide explique, pas à pas, l'utilisation complète de la plateforme **ARMANI** pour le profil **Super Administrateur**. Le super admin gère l'ensemble de la plateforme : toutes les communes, tous les utilisateurs, toutes les infrastructures, les affectations et la planification.

> 🖼️ **Notation** : les mentions `[Capture d'écran : ...]` indiquent où insérer une capture d'écran.

---

## 2. Connexion à la plateforme

1. Ouvrez votre navigateur et allez sur **https://armani.bj** ;
2. Cliquez sur **« Se connecter »** (en haut à droite) ;
   - [Capture d'écran : page d'accueil avec bouton Se connecter]
3. Saisissez votre **adresse email** et votre **mot de passe** ;
4. Cliquez sur le bouton **« Se connecter »** ;
   - [Capture d'écran : formulaire de connexion]
5. **Double authentification (MFA)** : un **code à 6 chiffres** est envoyé à votre adresse email. Saisissez ce code puis cliquez **« Vérifier »**.
   - [Capture d'écran : page de saisie du code MFA]
   - ⚠️ Le code est valable **10 minutes** ; après 5 tentatives erronées, demandez un nouveau code.

Vous êtes maintenant connecté. En haut à droite, vous voyez votre **avatar** avec votre prénom/nom et le badge **« Super Admin »**.

---

## 3. Vue d'ensemble de l'interface

### 3.1 L'en-tête (header)
- **Barre du haut (topbar)** : informations de contact (téléphone, email) ;
- **Barre de navigation (navbar)** : logo ARMANI à gauche, menus à droite ;
- **Menu utilisateur** (avatar) : accès rapide à votre tableau de bord, aux liens d'administration et à la **déconnexion**.
- [Capture d'écran : en-tête complet de la plateforme]

### 3.2 Le menu principal (Super Admin)
| Menu | Rôle |
|---|---|
| **Tableau de bord** | Vue d'ensemble chiffrée de toute la plateforme |
| **Administration** ▾ | **Communes** (gérer les communes), **Utilisateurs** (gérer les comptes), **Inscriptions en attente** (valider/refuser les nouvelles demandes) |
| **Infrastructures** | Liste et gestion de toutes les infrastructures |
| **Affectations** | Affecter des infrastructures aux agents, suivre les soumissions |
| **Fiches hors-ligne** | Consulter/synchroniser les fiches saisies sans connexion |

### 3.3 La barre d'onglets mobile
Sur téléphone, une barre d'onglets en bas donne accès à : **Tableau**, **Infras**, **Affect.**, **Communes**, **Users**.
- [Capture d'écran : barre d'onglets mobile]

---

## 4. Le tableau de bord

- [Capture d'écran : tableau de bord super admin]
Le tableau de bord affiche des **statistiques globales** :
- Nombre d'**infrastructures** (total, par statut : validées, en attente, rejetées) ;
- Répartition par **commune** ;
- Répartition par **secteur** (Éducation, Eau potable, Santé, Marché…) ;
- Répartition par **état de fonctionnement** et **niveau de dégradation** ;
- **Priorités** (urgent, élevée, moyenne, faible) ;
- Liens rapides vers les principales fonctionnalités.

---

## 5. Administration des communes

Menu **Administration → Communes** (ou `Communes` sur mobile).

### 5.1 Liste des communes
- [Capture d'écran : liste des communes]
La liste affiche chaque **commune** (nom, nombre d'infrastructures, logo). Boutons disponibles par ligne :
- **Voir** : détail de la commune ;
- **Modifier** : modifier le nom ou le logo ;
- **Supprimer** : supprimer la commune (avec confirmation).

### 5.2 Créer une commune
1. Cliquez sur **« + Nouvelle commune »** ;
2. Renseignez le **nom** de la commune et téléversez un **logo** (optionnel) ;
3. Cliquez **« Enregistrer »**.
- [Capture d'écran : formulaire de création d'une commune]

---

## 6. Administration des utilisateurs

Menu **Administration → Utilisateurs**.

### 6.1 Liste des utilisateurs
- [Capture d'écran : liste des utilisateurs]
La liste affiche : **nom, prénom, email, téléphone, rôle, commune, statut d'approbation**. Actions :
- **Modifier** : modifier le profil (nom, email, rôle, commune, approbation) ;
- **Promouvoir / rétrograder** un agent (selon droits) ;
- **Réinitialiser le mot de passe** (si disponible) ;
- **Supprimer** un compte (avec confirmation).

### 6.2 Modifier un utilisateur
1. Cliquez sur **« Modifier »** ;
2. Modifiez les champs souhaités (**rôle** : super_admin / commune_admin / agent / public_user ; **commune** ; **approbation**) ;
3. Cliquez **« Enregistrer »**.
- [Capture d'écran : formulaire de modification d'un utilisateur]
- ⚠️ Le **rôle** détermine les droits : ne l'attribuez qu'à des personnes de confiance.

---

## 7. Inscriptions en attente

Menu **Administration → Inscriptions en attente** (un badge orange indique le nombre de demandes).

- [Capture d'écran : page des inscriptions en attente]
1. La liste montre les **nouvelles inscriptions** (nom, email, commune, rôle demandé) ;
2. Pour chaque demande :
   - **Approuver** (✓) : le compte est activé, l'utilisateur peut se connecter ;
   - **Rejeter** : indiquez un **motif** (le demandeur sera informé) ;
3. Vérifiez l'identité avant d'approuver.

---

## 8. Journal d'audit (traçabilité)

Menu accessible via l'administration (`/admin/audit`).
- [Capture d'écran : page du journal d'audit]
Le journal enregistre toutes les actions importantes : **connexions, déconnexions, créations, modifications, suppressions, exports, imports**. Pour chaque entrée :
- Utilisateur concerné, **action**, entité, **adresse IP**, **navigateur**, **date/heure**, statut.
- Vous pouvez **filtrer** par utilisateur, action, type et **consulter le détail** d'une entrée.
- ⚠️ Ce journal permet de **retracer** toute opération sur la plateforme.

---

## 9. Gestion des infrastructures

Menu **« Infrastructures »** → liste complète de toutes les infrastructures des 8 communes.

### 9.1 Liste et filtres
- [Capture d'écran : liste des infrastructures avec filtres]
La liste affiche : **ID, nom, commune/village, secteur, type, état, photos, actions**.
Les **filtres** (en haut) permettent de rechercher :
- **Commune**, **Arrondissement**, **Village** ;
- **Secteur** (Éducation, Eau potable…), **Type**, **État de fonctionnement**, **Niveau de dégradation** ;
- **Recherche texte** (nom, ID) ;
- Cliquez **« Filtrer »** pour appliquer, **« Réinitialiser »** pour tout effacer.

### 9.2 Actions sur une infrastructure
| Bouton | Rôle |
|---|---|
| **Voir** | Consulter la fiche complète (localisation, GPS, photos, état, planification) |
| **Modifier** | Mettre à jour la fiche |
| **Planifier** | Ajouter/modifier une intervention planifiée (annuelle/triennale) |
| **Marquer réhabilitée** | Indiquer que l'infrastructure a été réhabilitée |
| **Exporter** | Exporter la liste en Excel/PDF |

### 9.3 Créer une infrastructure
1. Cliquez sur **« + Ajouter »** (ou `infrastructures/create`) ;
2. Suivez les **3 étapes** du formulaire :
   - **Étape 1** : identification (nom, secteur, type, commune, arrondissement, village, année, bailleur) ;
   - **Étape 2** : géolocalisation (**GPS automatique** via le bouton, précision < 5 m, altitude) et **carte** ;
   - **Étape 3** : **état** constaté (fonctionnement, dégradation) et **photos** (import + caméra, jusqu'à 4) ;
3. Cliquez **« Soumettre la fiche »**.
- [Capture d'écran : formulaire multi-étapes de création]
- La fiche passe alors en **« en attente de validation »**.

### 9.4 Validation des infrastructures
- La liste permet de **valider** ou **rejeter** les fiches en attente (avec motif) ;
- Une infrastructure **validée** devient exploitable (analyses, planification, exports).

---

## 10. Planification (annuelle et triennale)

Menu **« Infrastructures planifiées »** (ou via le menu utilisateur).

- [Capture d'écran : page des infrastructures planifiées]

### 10.1 La liste des planifiées
Le tableau affiche pour chaque infrastructure : **ID, nom, commune, type, interventions planifiées, plan annuel (budget + trimestres), plan triennal (coût, répartition), priorité, statut d'exécution, coût total, prochaine échéance**.

### 10.2 Planifier une infrastructure
1. Ouvrez une infrastructure puis cliquez **« Planifier »** (ou bouton **Modifier** dans la liste des planifiées) ;
2. Renseignez le formulaire de planification :
   - **Nature de l'intervention** : type de travail, date prévue, description ;
   - **Budget & acteurs** : coût estimé, plage d'années (année de début → année de fin), acteurs, financement ;
   - **Fiche TRIENNALE** : unité, quantité, coût unitaire, répartition par année (générée automatiquement selon la plage), priorité ;
   - **Fiche ANNUELLE** : un bloc par exercice (budget annuel repris de la répartition + trimestres T1→T4), statut d'exécution ;
   - **Observations** ;
3. Cliquez **« Enregistrer la planification »**.
- [Capture d'écran : formulaire de planification complet]

### 10.3 Exporter les plans
| Bouton | Rôle |
|---|---|
| **Exporter Plan Triennal (sélection)** | PDF du plan triennal pour les infrastructures cochées |
| **Exporter Plan Triennal (tous filtrés)** | PDF du plan triennal pour toutes les planifiées |
| **Exporter Plan Annuel (sélection)** | PDF du plan annuel pour la sélection |
| **Exporter Plan Annuel (tous filtrés)** | PDF du plan annuel pour toutes |
| **Excel (sélection / tous filtrés)** | Export Excel des planifiées |

Les PDF respectent le **modèle officiel MDGL** (en-tête République du Bénin, tableaux conformes).

---

## 11. Affectations des infrastructures aux agents

Menu **« Affectations »**.

- [Capture d'écran : page des affectations]
Cette page permet d'**affecter des infrastructures à des agents collecteurs** pour qu'ils mettent à jour leurs données.

### 11.1 Section 1 : les agents
Liste des **agents** avec le **nombre d'infrastructures affectées** à chacun.

### 11.2 Section 2 : choisir les infrastructures
1. **Filtres** : commune, arrondissement, village, type pour trouver les infrastructures ;
2. **Cochez** les infrastructures à affecter (ou **« Tout cocher »**) ;
   - Les infrastructures **déjà affectées** portent un badge **« Déjà affectée »** ;
3. Le **compteur de sélection** indique le nombre choisi ;
4. Sélectionnez l'**agent** destinataire ;
5. Cliquez **« Affecter »** → toutes les infrastructures sélectionnées sont affectées.

### 11.3 Suivi des affectations
- L'agent **soumet** sa mise à jour → statut **« soumise »** ;
- Vous **examinez** puis **validez** ou **rejetez** (avec motif) ;
- **Dès validation**, l'agent **perd l'accès** à l'infrastructure (l'affectation est close) ;
- Vous pouvez aussi **révoquer** une affectation en cours.

---

## 12. Fiches hors-ligne

Menu **« Fiches hors-ligne »**.
- [Capture d'écran : page des fiches hors-ligne]
Permet de **consulter** et **synchroniser** les fiches saisies sans connexion (PWA) : les saisies effectuées hors-ligne sur un appareil sont **envoyées** vers le serveur dès la reconnexion.

---

## 13. Glossaire des boutons et icônes

| Élément | Signification |
|---|---|
| 🔍 **Voir** (œil) | Afficher le détail |
| ✏️ **Modifier** | Éditer l'élément |
| 🗑️ **Supprimer** | Supprimer (avec confirmation) |
| 📅 **Planifier** | Gérer la planification |
| ✓ **Valider / Approuver** | Accepter une fiche ou une inscription |
| ✗ **Rejeter** | Refuser (avec motif) |
| 📄 **Exporter PDF** | Générer un document PDF |
| 📊 **Exporter Excel** | Télécharger en Excel |
| 🤝 **Affecter** | Attribuer des infrastructures à un agent |
| ☁️ **Hors-ligne** | Fiches stockées en local |

---

## 14. Déconnexion et bonnes pratiques

- **Déconnexion** : cliquez sur votre **avatar** en haut à droite → **« Déconnexion »** ;
- Ne partagez jamais votre mot de passe ni votre code MFA ;
- Vérifiez les **inscriptions en attente** régulièrement ;
- Contrôlez les **journaux d'audit** pour détecter toute activité anormale ;
- Effectuez des **sauvegardes/contrôles** réguliers (voir procédures d'exploitation).

---

## 15. Dépannage rapide

| Problème | Solution |
|---|---|
| Mot de passe oublié | Cliquez sur **« Mot de passe oublié ? »** sur la page de connexion et suivez le lien reçu par email |
| Code MFA non reçu | Vérifiez les spams ; cliquez **« Renvoyer un code »** |
| Impossible de se connecter | Vérifiez que votre compte est **approuvé** ; reCAPTCHA doit être validé |
| Page inaccessible | Vérifiez la connexion internet ou le mode hors-ligne |
| Besoin d'aide | Contactez le support technique / RSI |

---
*Document de référence — Super Administrateur — Plateforme ARMANI.*
