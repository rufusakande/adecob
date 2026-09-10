# LIVRABLE L2 — PLAN DE SÉCURISATION ET DE GESTION DES RISQUES
### Plateforme ARMANI (ex-ADECOB) — Communes du Borgou

**Mission :** Sécurisation, amélioration et opérationnalisation (POA 2026/ADECOB/AGORA1)
**Version :** 1.0 — **Date :** 19/08/2026
**Rédigé par :** [Nom du prestataire / consultant]

---

## 1. Objectif

Identifier, classer et traiter les risques techniques, sécuritaires, organisationnels et juridiques pesant sur la plateforme, conformément aux constats de l'audit de mars 2026, afin d'atteindre un **score de maturité sécurité ≥ 3,0/5** et **0 vulnérabilité critique résiduelle**.

## 2. Méthode d'analyse des risques

Analyse qualitative basée sur la probabilité (P) et l'impact (I), niveau de risque = P × I (1 à 4) :
- **Critique** (12-16) : traitement immédiat ;
- **Élevé** (8-12) : traitement prioritaire ;
- **Modéré** (4-8) : traitement planifié ;
- **Faible** (< 4) : surveillé.

## 3. Inventaire des vulnérabilités identifiées (audit 2026) et traitement

| # | Vulnérabilité constatée | Criticité (audit) | Mesure appliquée | État |
|---|---|---|---|---|
| V1 | Absence d'authentification renforcée | Critique | **MFA par email** sur tous les comptes administrateurs | ✅ Corrigé |
| V2 | Faiblesse de la gestion des accès | Élevée | **RBAC** (4 rôles : super_admin, commune_admin, agent, public_user) + middlewares | ✅ Corrigé |
| V3 | Risque d'attaques par force brute | Élevée | **reCAPTCHA v3**, throttling (limite de tentatives) | ✅ Corrigé |
| V4 | Absence de traçabilité | Critique | **Journaux d'audit** (AuditLog + trait Auditable) | ✅ Corrigé |
| V5 | Risques XSS | Élevée | Échappement automatique Blade, Security Headers (CSP) | ✅ Corrigé |
| V6 | Risques SQL Injection | Élevée | **ORM Eloquent** (requêtes paramétrées) | ✅ Corrigé |
| V7 | Données sensibles en clair | Élevée | **Chiffrement** des champs sensibles (téléphone, clés) | ✅ Corrigé |
| V8 | Absence de politique de mots de passe | Modérée | **Politique de mots de passe** (force, expiration, réinitialisation) | ✅ Corrigé |
| V9 | Absence de PCA / PRA | Critique | Documents PCA/PRA + sauvegardes quotidiennes | ⚠️ Rédigés, test à documenter |
| V10 | Formulaires non protégés (CSRF) | Modérée | **Protection CSRF** active (Laravel) | ✅ Corrigé |
| V11 | Connexion non chiffrée | Élevée | **HTTPS obligatoire** en production, redirection HTTP→HTTPS | ✅ Corrigé |

## 4. Registre des risques résiduels

| Risque | Type | Prob. | Impact | Niveau | Traitement / maîtrise |
|---|---|---|---|---|---|
| Défaillance serveur / indisponibilité | Technique | 2 | 4 | 8 | SLA hébergeur, monitoring, PCA |
| Perte de données | Technique | 1 | 4 | 4 | Sauvegardes quotidiennes + restauration testée (L7) |
| Compromission de comptes utilisateurs | Sécuritaire | 2 | 3 | 6 | MFA, reCAPTCHA, politique de mots de passe |
| Non-respect de la conformité (Code du numérique) | Juridique | 2 | 3 | 6 | PSSI, registre des traitements, CGU, politique confidentialité |
| Faible appropriation par les communes | Organisationnel | 3 | 3 | 9 | Plan de formation, référents numériques, accompagnement |
| Qualité des données insuffisante | Organisationnel | 3 | 3 | 9 | Workflow de validation, rapport de certification (L4) |
| Attaque DDoS | Sécuritaire | 1 | 3 | 3 | Protection hébergeur, monitoring |

## 5. Mesures de sécurisation mises en œuvre (détail)

### 5.1 Authentification et accès
- **MFA par email** (code à 6 chiffres, validité 10 min, 5 tentatives max) — obligatoire pour tous les administrateurs ;
- **reCAPTCHA v3** (score ≥ 0,5) sur connexion et inscription ;
- **RBAC** : super_admin / commune_admin / agent / public_user, avec middlewares dédiés (`super.admin`, `admin.access`, `commune.admin`, `mfa.verified`, `check.approval`) ;
- **Politique de mots de passe** : longueur minimale, complexité, verrouillage des comptes.

### 5.2 Protection applicative
- **Security Headers** (CSP, X-Content-Type-Options, etc.) ;
- **Chiffrement** des données sensibles (cast `encrypted` Eloquent) ;
- **Protection CSRF**, échappement XSS, ORM (SQLi) ;
- **Throttling** sur les routes sensibles (connexion, MFA, réinitialisation).

### 5.3 Traçabilité
- Journalisation systématique : connexions, déconnexions, CRUD, imports/exports, validations ;
- Table `audit_logs` (10 142 entrées) avec anciennes/nouvelles valeurs, IP, user-agent.

### 5.4 Continuité
- Sauvegardes automatiques quotidiennes (hébergeur) ;
- PCA/PRA rédigés (voir L7 pour le test) ;
- Monitoring du fonctionnement (dashboard mairie agent, logs).

## 6. Feuille de route de mise à niveau

| Étape | Action | Échéance | Responsable |
|---|---|---|---|
| 1 | Re-test de sécurité post-corrections (tests d'intrusion / scan OWASP) | [date] | [prestataire] |
| 2 | Documenter le test de restauration PCA/PRA (L7) | [date] | [prestataire] |
| 3 | Compléter la conformité Code du numérique (L6) | [date] | [prestataire] |
| 4 | Mesurer le score de maturité sécurité final (objectif ≥ 3,0/5) | [date] | [prestataire] |
| 5 | Plan de formation + sensibilisation des communes | [date] | [prestataire / ADECOB] |

## 7. Indicateurs de suivi

- **Vulnérabilités critiques résiduelles :** 0 (objectif) ;
- **Score de maturité sécurité :** ≥ 3,0/5 (vs 1,4/5 à l'audit) ;
- **Comptes administrateurs avec MFA active :** 100 % ;
- **Disponibilité :** ≥ 99 % ;
- **Sauvegardes vérifiées :** quotidiennes + 1 test de restauration documenté.

## 8. Conclusion

Les vulnérabilités critiques et élevées issues de l'audit ont été traitées et corrigées. Le dispositif de sécurité est opérationnel (MFA, RBAC, reCAPTCHA, chiffrement, journalisation, headers de sécurité). Il reste à documenter les preuves (re-test sécurité, test PCA/PRA) et à maintenir ce dispositif dans le temps (mises à jour de sécurité, surveillance).

---
*Document à transmettre officiellement et à valider par le Comité technique de suivi.*
