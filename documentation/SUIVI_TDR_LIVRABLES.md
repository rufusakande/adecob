# SUIVI TDR & LIVRABLES — PLATEFORME ARMANI
### (ex-ADECOB) — Mission de sécurisation, d'amélioration et d'opérationnalisation
**Date :** 19/08/2026 — **Projet :** ARMANI (Gestion des infrastructures sociocommunautaires et économiques — Communes du Borgou)
**Référence :** TDR « Mission de sécurisation, d'amélioration et d'opérationnalisation de la plateforme digitale de gestion des infrastructures sociocommunautaires et économiques dans les communes du Borgou » (POA 2026/ADECOB/AGORA1)

---

## 1. Rappel du cadre (TDR)

- **Constat initial (audit mars 2026) :** maturité SI globale **1,4/5**, conformité **54/100**, vulnérabilités majeures en sécurité (absence d'authentification renforcée, faiblesses gestion des accès, risques de compromission), absence de PCA/PRA/sauvegardes formalisées, module de planification faiblement exploité, insuffisances de gouvernance SI et de traçabilité.
- **Objectif général :** renforcer sécurité, fiabilité, conformité et opérationnalisation ; faire de la plateforme un outil stratégique de pilotage des investissements locaux.
- **8 phases** et **7 résultats attendus (R1→R7)** avec indicateurs mesurables.

---

## 2. État d'avancement par résultat attendu (R1 → R7)

### R1 – Sécurité et fiabilité
| Exigence TDR | État | Preuves / observations |
|---|---|---|
| Vulnérabilités critiques corrigées (0 résiduelle) | ✅ Quasi fait | Refonte authentification, reCAPTCHA, SecurityHeaders, RBAC, Sanctum |
| MFA opérationnelle sur 100 % des comptes admin | ✅ Fait | Table `mfa_codes`, middleware `mfa.verified`, code email + logs |
| Score maturité sécurité ≥ 3,0/5 | ⚠️ À mesurer | Nécessite un re-test de sécurité (pentest/audit) et un rapport |
| Système de journaux d'audit opérationnel | ✅ Fait | `AuditLog` + `AuditService` + trait `Auditable` + écran d'audit |

### R2 – Continuité et résilience
| Exigence TDR | État | Preuves / observations |
|---|---|---|
| Sauvegardes automatiques quotidiennes vérifiées | ✅ Fait (hébergeur) | À **documenter/preuver** (job, journal de restauration) |
| PCA et PRA élaborés, validés **et testés (1 test documenté)** | ⚠️ Partiel | Doc `documentation/PRA_PCA_SAUVEGARDE.md` existe ; **test de reprise à documenter** |
| Monitoring temps réel opérationnel | ✅ Fait | `monitoring-dashboard` (mairie agent), logs, SLA hébergeur |
| Taux de disponibilité ≥ 99 % | ⚠️ À prouver | Mesure sur la période + rapport |

### R3 – Conformité réglementaire
| Exigence TDR | État | Preuves / observations |
|---|---|---|
| Score conformité Code du numérique ≥ 80/100 | ⚠️ À produire | Documents présents ; **auto-évaluation/rapport de conformité à rédiger** |
| PSSI formalisée, validée et publiée | ⚠️ Partiel | Vue `legal/pssi` (interne) ; **finaliser + PV de validation** |
| Politique de confidentialité et CGU publiées | ✅ Fait | Pages `/politique-confidentialite`, `/cgu`, `/mentions-legales`, `/politique-cookies` |
| Mécanismes de gouvernance du SI documentés | ✅ Fait | Procédures (RGPD, incidents, gestion comptes, charte informatique, convention sous-traitance) |

### R4 – Qualité des données
| Exigence TDR | État | Preuves / observations |
|---|---|---|
| Taux de complétude ≥ 85 % | ⚠️ À mesurer | Nécessite un **rapport de certification qualité des données** |
| Mécanismes de contrôle et validation des données | ✅ Fait | Workflow validation (pending → validated/rejected), rejets, affectations |
| Base nettoyée et cohérente | ✅ Fait | Nettoyages effectués (communes, doublons, géolocalisation) |
| Rapport de certification qualité des données | ❌ **À produire** | Livrable absent |

### R5 – Fonctionnalités opérationnelles
| Exigence TDR | État | Preuves / observations |
|---|---|---|
| Module de planification utilisé ≥ 6/8 communes | ⚠️ À mesurer | Module opérationnel (annuel + triennal, exports MDGL) ; **preuve d'usage à fournir** |
| 100 % des exigences fonctionnelles intégrées ou documentées | ✅ Fait | CRUD, import/export (Excel/PDF), filtres, géoloc, PWA hors-ligne, affectations, photos caméra |
| Suivi et maintenance opérationnels | ✅ Fait | `mairie_agent_data` (maintenance), réhabilitation, monitoring |
| **Rapport de recette fonctionnelle validé par l'ADECOB** | ❌ **À produire** | PV de recette + émargement |

### R6 – Outils de pilotage
| Exigence TDR | État | Preuves / observations |
|---|---|---|
| Tableaux de bord pour chacune des 8 communes | ✅ Fait | Dashboards super admin, admin commune, mairie agent, public |
| ≥ 10 KPI définis et suivis | ✅ Fait | Statistiques, score de priorité (IPR), états, types, planification… |
| Reporting automatisé opérationnel | ⚠️ Partiel | Exports Excel/PDF automatisés ; **génération périodique à formaliser** |
| ≥ 80 % des autorités communales formées | ❌ **À faire** | Action de formation (organisationnel) |

### R7 – Appropriation communale
| Exigence TDR | État | Preuves / observations |
|---|---|---|
| 100 % des agents désignés formés | ❌ **À faire** | Plan de formation + ateliers à organiser |
| ≥ 2 sessions pratiques par commune | ❌ **À faire** | Rapports d'ateliers + feuilles de présence |
| Taux d'utilisation active ≥ 70 % (3 mois) | ❌ **À suivre** | Indicateur post-formation |
| Référent numérique désigné par commune | ❌ **À faire** | À identifier et acter |

---

## 3. LISTE DES LIVRABLES À FOURNIR

### A. Livrables déjà disponibles (à consolider / présenter)
1. **Plateforme opérationnelle + code source** (Laravel 10) — ✅ disponible
2. **Guide de l'utilisateur** — 3 guides par rôle : `GUIDE_UTILISATEUR_SUPER_ADMIN.md`, `GUIDE_UTILISATEUR_ADMIN_COMMUNE.md`, `GUIDE_UTILISATEUR_AGENT.md` (+ anciens `GUIDE_UTILISATION.md` / Word à rebrander « ARMANI ») — ✅
3. **Guide d'administration** — `documentation/GUIDE_ADMINISTRATION.md` — ✅ à rebrander
4. **Guide de démarrage rapide** — `documentation/DEMARRAGE_RAPIDE.md` — ✅
5. **PSSI** (Politique de Sécurité des Systèmes d'Information) — ✅ à finaliser/valider
6. **Politique de confidentialité, CGU, mentions légales, politique cookies** — ✅ publiées sur la plateforme
7. **PCA / PRA / Sauvegarde** — `documentation/PRA_PCA_SAUVEGARDE.md` — ⚠️ à compléter avec un test documenté
8. **Procédures de gouvernance** (RGPD, gestion des comptes, incidents, charte informatique, convention de sous-traitance, chiffrement TLS) — ✅
9. **Système de journaux d'audit** — `documentation/AUDIT_SYSTEM.md` + `AUDIT_INTEGRATION_GUIDE.md` — ✅
10. **Tableaux de bord & statistiques** — dashboards opérationnels — ✅

### B. Livrables À PRODUIRE (documents de mission manquants)
| # | Livrable | Phase TDR | Type | État |
|---|---|---|---|---|
| L1 | **Rapport de cadrage et diagnostic consolidé** | P1 | Document | ✅ Rédigé (`LIVRABLE_L1_RAPPORT_CADRAGE_DIAGNOSTIC.md`) |
| L2 | **Plan de sécurisation et de gestion des risques** (classification vulnérabilités + feuille de route) | P2 | Document | ✅ Rédigé (`LIVRABLE_L2_PLAN_SECURISATION_RISQUES.md`) |
| L3 | **Rapport de recette fonctionnelle validé par l'ADECOB** (PV de recette) | P5 | Document + PV | ✅ Rédigé (`LIVRABLE_L3_RAPPORT_RECETTE_FONCTIONNELLE.md`) — PV à signer |
| L4 | **Rapport de certification qualité des données** (complétude ≥ 85 %) | P5 | Document | ✅ Rédigé (`LIVRABLE_L4_CERTIFICATION_QUALITE_DONNEES.md`) |
| L5 | **Rapport de tests** (fonctionnels, techniques, sécurité) + re-test de sécurité post-correction | P3/P8 | Document | ✅ Rédigé (`LIVRABLE_L5_RAPPORT_TESTS.md`) — re-test externe à joindre |
| L6 | **Rapport de conformité au Code du numérique** (score ≥ 80/100) | P3 | Document | ✅ Rédigé (`LIVRABLE_L6_RAPPORT_CONFORMITE_CODE_NUMERIQUE.md`) |
| L7 | **Preuve de test PCA/PRA** (1 test de restauration documenté) | P4 | PV | ✅ Rédigé (`LIVRABLE_L7_TEST_PCA_PRA.md`) — PV à compléter/valider |
| L8 | **Modules de formation + rapports d'ateliers + feuilles de présence** | P7 | Documents + PV | ❌ À produire |
| L9 | **Rapport final de mission** (synthèse, capitalisation, recommandations) | P8 | Document | ✅ Rédigé (`LIVRABLE_L9_RAPPORT_FINAL_MISSION.md`) — PV à signer |
| L10 | **Procédures d'exploitation & transfert de compétences** (maintenance, mise à jour sécurité, documentation technique) | P11 | Document | ✅ Rédigé (`LIVRABLE_L10_PROCEDURES_EXPLOITATION.md`) |
| L11 | **PV de validation des livrables** (comité technique ADECOB/UGP/RSI) | P9 | PV | ❌ À produire |

### C. Actions organisationnelles à planifier (non-code)
- Plan de formation des acteurs communaux (DST, DDLP, RAAF, RSI, PRMP, Maires, Adjoints, CA).
- ≥ 2 sessions pratiques par commune + feuilles d'émargement.
- Désignation d'un **référent numérique** par commune.
- Atelier de restitution final + séance de validation des livrables.

---

## 4. Ce qui reste à faire — synthèse

### En priorité (livrables contractuels)
1. Rédiger le **Rapport de cadrage et diagnostic consolidé** (L1).
2. Rédiger le **Plan de sécurisation et de gestion des risques** (L2) + re-test sécurité pour justifier le score maturité ≥ 3,0/5.
3. Réaliser le **Rapport de recette fonctionnelle** (L3) validé par l'ADECOB.
4. Produire le **Rapport de certification qualité des données** (L4).
5. Produire le **Rapport de conformité au Code du numérique** (L6) avec score ≥ 80/100.
6. Documenter **1 test PCA/PRA** (L7).
7. Rédiger le **Rapport final de mission** (L9) et les **procédures d'exploitation** (L10).

### En second lieu (gouvernance / appropriation)
8. Consolider/rebrander les documents « ADECOB » → « ARMANI » (guides, rapports).
9. Planifier et exécuter la **formation** (L8) et désigner les référents numériques.
10. Mesurer les indicateurs (utilisation planification, disponibilité ≥ 99 %, complétude ≥ 85 %) et les rapporter.

---

## 5. Rappel des indicateurs cibles du TDR (à renseigner dans le rapport final)
- Maturité sécurité : **≥ 3,0/5** (vs 1,4/5)
- Conformité Code du numérique : **≥ 80/100** (vs 54/100)
- Complétude données : **≥ 85 %**
- Disponibilité : **≥ 99 %**
- Planification utilisée : **≥ 6/8 communes**
- Formation : **100 % des agents désignés**, **≥ 2 sessions/commune**
- Utilisation active : **≥ 70 % des communes** (3 mois post-formation)
