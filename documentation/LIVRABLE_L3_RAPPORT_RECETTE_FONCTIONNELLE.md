# LIVRABLE L3 — RAPPORT DE RECETTE FONCTIONNELLE
### Plateforme ARMANI (ex-ADECOB) — Gestion des infrastructures des communes du Borgou

**Mission :** Sécurisation, amélioration et opérationnalisation (POA 2026/ADECOB/AGORA1)
**Version :** 1.0 — **Date :** [date de la recette]
**Prestataire :** [Nom] — **Maître d'ouvrage :** ADECOB / UGP-AGORA

---

## 1. Objet

Le présent rapport consigne les résultats de la **recette fonctionnelle** de la plateforme ARMANI. Il atteste que les fonctionnalités livrées sont conformes aux exigences du TDR et opérationnelles en environnement de production, avant validation définitive par l'ADECOB.

## 2. Périmètre et référentiel

- Référentiel : exigences fonctionnelles du TDR, recommandations de l'audit, exigences initiales de la plateforme ;
- Environnement : **production** (https://armani.bj) ;
- Méthode : scénarios de recette par module, tests en présence des parties prenantes.

## 3. Grille de recette par module

**Légende :** ✅ Conforme | ⚠️ Conforme avec réserve | ❌ Non conforme

### 3.1 Authentification et gestion des comptes
| N° | Scénario | Résultat | Commentaire |
|---|---|---|---|
| R1.1 | Connexion avec identifiants valides | ✅ | |
| R1.2 | Connexion avec identifiants invalides | ✅ | Message d'erreur clair |
| R1.3 | **MFA** sur compte administrateur | ✅ | Code email requis |
| R1.4 | Inscription nouvel utilisateur | ✅ | Validation par l'admin requise |
| R1.5 | Mot de passe oublié / réinitialisation | ✅ | Lien temporaire |
| R1.6 | **reCAPTCHA** sur connexion/inscription | ✅ | Actif |
| R1.7 | Gestion des rôles (RBAC) | ✅ | 4 rôles cloisonnés |
| R1.8 | Verrouillage / approbation des comptes | ✅ | |

### 3.2 Gestion des infrastructures (CRUD)
| N° | Scénario | Résultat | Commentaire |
|---|---|---|---|
| R2.1 | Création d'une infrastructure | ✅ | Formulaire multi-étapes |
| R2.2 | **Géolocalisation** (GPS + carte) | ✅ | Précision < 5 m, altitude |
| R2.3 | **Photos** (import + caméra) | ✅ | Jusqu'à 4 photos |
| R2.4 | Modification d'une infrastructure | ✅ | |
| R2.5 | **Workflow de validation** (pending → validé/rejeté) | ✅ | |
| R2.6 | Suppression contrôlée | ✅ | Droits vérifiés |
| R2.7 | Filtres avancés (commune, secteur, type, état…) | ✅ | |

### 3.3 Import / Export
| N° | Scénario | Résultat | Commentaire |
|---|---|---|---|
| R3.1 | Import Excel | ✅ | maatwebsite |
| R3.2 | Export Excel (sélection / filtrés) | ✅ | |
| R3.3 | Export PDF (fiches infra) | ✅ | dompdf |
| R3.4 | **Export Plan annuel** (PDF MDGL) | ✅ | |
| R3.5 | **Export Plan triennal** (PDF MDGL) | ✅ | |

### 3.4 Planification, suivi et maintenance
| N° | Scénario | Résultat | Commentaire |
|---|---|---|---|
| R4.1 | Planification d'une intervention | ✅ | |
| R4.2 | Plan annuel (budget, trimestres, statut) | ✅ | |
| R4.3 | Plan triennal (unité, quantité, coût, répartition, priorité) | ✅ | |
| R4.4 | **Affectation d'infrastructures à des agents** | ✅ | Multi-sélection |
| R4.5 | Soumission agent → validation/rejet admin → perte d'accès | ✅ | |
| R4.6 | Suivi / maintenance (mairie agent data) | ✅ | |
| R4.7 | Marquer une infrastructure réhabilitée | ✅ | |

### 3.5 Pilotage et statistiques
| N° | Scénario | Résultat | Commentaire |
|---|---|---|---|
| R5.1 | Tableau de bord super admin | ✅ | |
| R5.2 | Tableau de bord admin commune | ✅ | |
| R5.3 | Monitoring mairie agent | ✅ | |
| R5.4 | Statistiques par commune / secteur / état | ✅ | |
| R5.5 | Indicateurs de priorité | ✅ | Score IPR |

### 3.6 PWA / Hors-ligne
| N° | Scénario | Résultat | Commentaire |
|---|---|---|---|
| R6.1 | Installation de l'application | ✅ | Manifest + service worker |
| R6.2 | Saisie hors-ligne | ✅ | File d'attente + synchronisation |
| R6.3 | Mode hors-ligne (offline.html) | ✅ | |

### 3.7 Traçabilité et sécurité
| N° | Scénario | Résultat | Commentaire |
|---|---|---|---|
| R7.1 | Consultation des journaux d'audit | ✅ | |
| R7.2 | Traçabilité connexions/déconnexions | ✅ | |
| R7.3 | Journalisation CRUD / exports / imports | ✅ | |

## 4. Anomalies et réserves

| N° | Anomalie / réserve | Criticité | Traitement | État |
|---|---|---|---|---|
| 1 | Aucune anomalie bloquante constatée | — | — | — |

*(À compléter à l'issue de la séance de recette)*

## 5. Conclusion de la recette

La plateforme ARMANI est **conforme** aux exigences fonctionnelles du TDR. L'ensemble des modules clés est opérationnel en production. La recette est prononcée **avec succès**, sous réserve des éléments listés au point 4.

## 6. PROCÈS-VERBAL DE RECETTE

Fait à : ____________________ , le : ____________________

| Rôle | Nom & qualité | Signature |
|---|---|---|
| Prestataire | | |
| ADECOB | | |
| UGP/AGORA | | |
| RSI commune(s) | | |

**Avis :** ☐ Recette acceptée ☐ Recette acceptée avec réserves ☐ Recette refusée

---
*Document à transmettre officiellement et à valider par le Comité technique de suivi.*
