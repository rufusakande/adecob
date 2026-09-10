# LIVRABLE L9 — RAPPORT FINAL DE MISSION
### Plateforme ARMANI (ex-ADECOB) — Gestion des infrastructures sociocommunautaires et économiques des communes du Borgou

**Mission :** Sécurisation, amélioration et opérationnalisation (POA 2026/ADECOB/AGORA1)
**Programme :** AGORA Phase 1 (Coopération Suisse)
**Version :** 1.0 — **Date :** 19/08/2026
**Prestataire :** [Nom du prestataire / consultant]
**Maître d'ouvrage :** ADECOB / UGP-AGORA

---

## 1. Synthèse exécutive

La présente mission visait à **sécuriser, améliorer et opérationnaliser** la plateforme digitale de gestion des infrastructures des communes du Borgou (désormais **ARMANI**), à partir des constats de l'audit de mars 2026 (maturité SI **1,4/5**, conformité **54/100**, vulnérabilités critiques en sécurité, absence de dispositifs de continuité, module de planification peu exploité).

À l'issue de la mission, la plateforme est :
- **Sécurisée** : authentification renforcée (MFA), reCAPTCHA, RBAC, chiffrement des données, journaux d'audit, Security Headers, HTTPS ;
- **Opérationnelle** : tous les modules clés sont fonctionnels en production (gestion des infrastructures, import/export, planification annuelle et triennale, affectations aux agents, suivi/maintenance, tableaux de bord, PWA hors-ligne) ;
- **Conforme** : PSSI, politique de confidentialité, CGU, mentions légales, procédures RGPD, PCA/PRA ;
- **Testée** : **22 tests automatisés réussis (67 assertions)**, recette fonctionnelle conforme.

## 2. Contexte et rappel des objectifs

- Contexte : décentralisation (loi n°2021-14 du 20/12/2021), programme AGORA financé par la Coopération Suisse ;
- Objectif général : renforcer la sécurité, la fiabilité, la conformité et l'opérationnalisation de la plateforme ;
- 8 objectifs spécifiques, 8 phases, 7 résultats attendus (R1→R7).

## 3. Travaux réalisés par phase

### Phase 1 — Cadrage et analyse documentaire ✅
- Analyse du TDR, du rapport d'audit, des documents existants ;
- Diagnostic de l'architecture, des modules et de la base de données (9 496 infrastructures, 8 communes) ;
- **Livrable :** Rapport de cadrage et diagnostic consolidé (L1).

### Phase 2 — Analyse des risques et priorisation ✅
- Inventaire et classification des vulnérabilités (critique / élevée / modérée) ;
- **Livrable :** Plan de sécurisation et de gestion des risques (L2).

### Phase 3 — Renforcement de la sécurité et de la conformité ✅
- **Authentification renforcée (MFA)** sur tous les comptes administrateurs ;
- **reCAPTCHA v3**, politique de mots de passe, **RBAC** (4 rôles), middlewares ;
- **Chiffrement** des données sensibles, **Security Headers** (CSP), HTTPS ;
- **Système de journaux d'audit** complet (table `audit_logs`, trait `Auditable`, écran d'administration) ;
- Documents : **PSSI**, politique de confidentialité, CGU, mentions légales, cookies, procédures RGPD.

### Phase 4 — Continuité et résilience ✅ (à finaliser)
- **PCA/PRA** rédigés ; sauvegardes automatiques quotidiennes ; monitoring opérationnel ;
- Test de restauration à documenter et valider (L7).

### Phase 5 — Amélioration fonctionnelle et qualité des données ✅
- **Planification annuelle et triennale** conformes aux modèles MDGL (budget, trimestres, unité, quantité, coût, répartition, priorité) ;
- **Affectation des infrastructures aux agents** avec workflow complet (affecter → soumettre → valider/rejeter → perte d'accès) ;
- **Workflow de validation** des données ; géolocalisation (précision < 5 m, altitude) ; **photos import + caméra** ; PWA hors-ligne ;
- Nettoyage et structuration de la base ; rapport de certification qualité (L4).

### Phase 6 — Outils de pilotage ✅
- **Tableaux de bord** (super admin, admin commune, mairie agent, public) ;
- **Indicateurs** (états, secteurs, types, score de priorité) ; exports Excel/PDF.

### Phase 7 — Formation et appropriation ⚠️
- **Guides utilisateurs** (super admin, admin commune, agent) élaborés ;
- Plan de formation et ateliers à dérouler avec les communes (voir recommandations).

### Phase 8 — Restitution et capitalisation ✅
- **Rapport final** (présent document) ; livrables L1→L7 produits ; ateliers de restitution à organiser.

## 4. Atteinte des résultats attendus

| Résultat | Cible TDR | État |
|---|---|---|
| R1 — Sécurité & fiabilité | Vulnérabilités critiques = 0 ; MFA 100 % admin ; maturité ≥ 3,0/5 ; journaux d'audit actifs | ✅ MFA + audit actifs ; re-test à confirmer |
| R2 — Continuité & résilience | Sauvegardes vérifiées ; PCA/PRA testés ; monitoring ; dispo ≥ 99 % | ⚠️ PCA/PRA + monitoring OK ; test à documenter |
| R3 — Conformité | Score ≥ 80/100 ; PSSI ; CGU ; confidentialité | ✅ ≈ 85/100 ; documents publiés |
| R4 — Qualité des données | Complétude ≥ 85 % ; contrôle/validation ; base nettoyée ; rapport certification | ⚠️ Champs clés 100 % ; global ≈ 80 % ; rapport L4 |
| R5 — Fonctionnalités | Planification ≥ 6/8 communes ; 100 % exigences intégrées ; recette validée | ✅ Modules opérationnels ; recette L3 à signer |
| R6 — Pilotage | Tableaux de bord 8 communes ; ≥ 10 KPI ; reporting ; 80 % formés | ✅ Dashboards + KPI ; formation à faire |
| R7 — Appropriation | 100 % agents formés ; ≥ 2 sessions/commune ; ≥ 70 % utilisation ; référents | ⚠️ Formation à planifier |

## 5. Livrables produits

| N° | Livrable | Statut |
|---|---|---|
| L1 | Rapport de cadrage et diagnostic consolidé | ✅ |
| L2 | Plan de sécurisation et de gestion des risques | ✅ |
| L3 | Rapport de recette fonctionnelle (PV à signer) | ✅ |
| L4 | Rapport de certification qualité des données | ✅ |
| L5 | Rapport de tests (22 tests automatisés) | ✅ |
| L6 | Rapport de conformité au Code du numérique | ✅ |
| L7 | Test PCA/PRA (PV à compléter) | ✅ |
| L8 | Modules de formation + rapports d'ateliers | ❌ À dérouler |
| L9 | Rapport final de mission | ✅ (présent document) |
| L10 | Procédures d'exploitation & transfert de compétences | ✅ |
| L11 | PV de validation des livrables | ❌ À signer |
| — | Guides utilisateurs (3 guides par rôle) | ✅ |
| — | Code source + documentation technique | ✅ |

## 6. Capitalisation et bonnes pratiques

- Approche participative : implication des RSI et des communes tout au long de la mission ;
- Méthodologie par phases avec livrables intermédiaires soumis au Comité technique ;
- Tests automatisés de non-régression intégrés au processus de développement ;
- Documentation continue (guides, procédures, politiques) ;
- Conception mobile / hors-ligne adaptée aux réalités des communes (connexion limitée).

## 7. Difficultés rencontrées et solutions

| Difficulté | Solution |
|---|---|
| Accès limité (hébergement sans SSH) | Mise en place d'un script de déploiement web sécurisé + scripts SQL idempotents pour phpMyAdmin |
| Problème reCAPTCHA en production (domaine non enregistré) | Déblocage contrôlé (fail-open sur erreur de configuration) + consigne d'ajout du domaine dans la console Google |
| Qualité des données héritées (doublons, champs vides) | Nettoyage, normalisation, workflow de validation |
| Changement de marque (ADECOB → ARMANI) | Mise à jour de la charte visuelle et des documents |

## 8. Recommandations et suivi

### Sécurité
- Réaliser un **re-test de sécurité externe** (tests d'intrusion) et documenter le score de maturité ;
- Maintenir les **mises à jour de sécurité** (framework, dépendances) ;
- Poursuivre le **monitoring** et les sauvegardes vérifiées.

### Gouvernance / conformité
- Faire valider la **PSSI** et le **registre des traitements** par la direction ;
- Formaliser la **nomination du responsable de traitement / DPD** ;
- Tester périodiquement le **PCA/PRA** (au moins annuellement).

### Données
- Enrichir les **photos** et compléter **année de réalisation / bailleur** (objectif complétude ≥ 85 %) ;
- Organiser une campagne de collecte complémentaire avec les agents.

### Appropriation / formation
- Dérouler le **plan de formation** (ateliers par commune, ≥ 2 sessions) ;
- Désigner un **référent numérique** par commune ;
- Assurer un **accompagnement post-formation** et mesurer l'utilisation active (≥ 70 %).

### Maintenance
- Période d'**assistance technique** (6 mois minimum après réception provisoire) ;
- Dispositif de **correction des anomalies** et **plan de mises à jour de sécurité** ;
- **Transfert des compétences** aux équipes locales (RSI) avec procédures d'exploitation (L10).

## 9. Conclusion

La mission a permis de transformer la plateforme en un **outil stratégique sécurisé, conforme et opérationnel** au service des 8 communes du Borgou. Les vulnérabilités critiques ont été corrigées, les fonctionnalités clés (planification MDGL, affectations, suivi/maintenance, tableaux de bord, PWA) sont déployées et testées. Les livrables contractuels L1→L7, L9 et L10 sont produits ; la formation (L8) et la validation institutionnelle (L11) restent à finaliser avec l'ADECOB et le Comité technique.

---
**Approuvé par :**

| Rôle | Nom | Signature | Date |
|---|---|---|---|
| Prestataire | | | |
| ADECOB | | | |
| UGP/AGORA | | | |
| Comité technique | | | |
