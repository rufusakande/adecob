# LIVRABLE L1 — RAPPORT DE CADRAGE ET DIAGNOSTIC CONSOLIDÉ
### Plateforme ARMANI (ex-ADECOB) — Gestion des infrastructures sociocommunautaires et économiques des communes du Borgou

**Mission :** Sécurisation, amélioration et opérationnalisation de la plateforme digitale (POA 2026/ADECOB/AGORA1)
**Version :** 1.0 — **Date :** 19/08/2026
**Rédigé par :** [Nom du prestataire / consultant]
**Destinataires :** ADECOB, UGP/AGORA, RSI des communes

---

## 1. Contexte et objet

Dans le cadre du Programme d'Appui à la Gouvernance locale et au Renforcement de l'Attractivité territoriale (AGORA) — Phase 1, financé par la Coopération Suisse, l'ADECOB a engagé une mission de sécurisation, d'amélioration et d'opérationnalisation de la plateforme digitale de gestion des infrastructures des communes du Borgou.

Ce présent rapport constitue le **livrable de la Phase 1 (Cadrage et analyse documentaire)** du TDR. Il consolide le diagnostic technique, fonctionnel et de gouvernance du système, et identifie les écarts entre les exigences initiales et l'état actuel de la plateforme.

## 2. Périmètre et méthode

| Élément | Description |
|---|---|
| Système audité | Plateforme digitale de gestion des infrastructures sociocommunautaires et économiques (nom retenu : **ARMANI**) |
| Technologie | Laravel 10 (PHP 8.1/8.2), MySQL, Bootstrap 5, Leaflet, PWA |
| Méthode | Analyse documentaire (TDR, DAO, guides, rapport d'audit) + revue du code + analyse de la base de données + entretiens |
| Données de référence | Base de référence : **9 496 infrastructures**, **8 communes** |

## 3. Analyse documentaire

Documents exploités :
- TDR de la mission (2026) ;
- Rapport d'audit technique, fonctionnel et de gouvernance (mars 2026) — maturité SI estimée **1,4/5**, conformité **54/100** ;
- Guide d'utilisation et guide d'administration ;
- Documents de gouvernance produits pendant la mission (PSSI, procédures RGPD, PCA/PRA, charte informatique).

## 4. Architecture technique

- **Framework :** Laravel 10 (PHP), architecture MVC, standards PSR-12 ;
- **Base de données :** MySQL (InnoDB, UTF-8), normalisée ;
- **Front-end :** Bootstrap 5, JavaScript, cartographie Leaflet/OpenStreetMap ;
- **Fonctionnalités PWA :** mode hors-ligne (saisie offline, service worker), installation sur appareil ;
- **Hébergement :** [Open.bg / serveur de production — à compléter], HTTPS activé ;
- **Authentification :** sessions sécurisées, **MFA par email** pour les administrateurs, **reCAPTCHA v3**, RBAC (4 rôles).

## 5. Modules fonctionnels

| Module | État | Observations |
|---|---|---|
| Authentification / comptes | ✅ Opérationnel | MFA admin, reCAPTCHA, politique de mots de passe, validation d'inscription |
| Gestion des infrastructures (CRUD) | ✅ Opérationnel | Saisie, modification, validation (workflow), suppression contrôlée |
| Géolocalisation | ✅ Opérationnel | GPS (lat/long, altitude, précision < 5 m), carte interactive |
| Photos | ✅ Opérationnel | Import + **prise de photo caméra**, jusqu'à 4 photos |
| Import / Export | ✅ Opérationnel | Excel (maatwebsite), PDF (dompdf), CSV |
| Planification (annuelle & triennale) | ✅ Opérationnel | Fiches MDGL exportables (annuelle + triennale) |
| Affectation aux agents | ✅ Opérationnel | Workflow affecter → soumettre → valider/rejeter → perte d'accès |
| Suivi / Maintenance | ✅ Opérationnel | Mairie agent data, réhabilitation, monitoring |
| Statistiques / Tableaux de bord | ✅ Opérationnel | Dashboards par rôle, indicateurs, score de priorité |
| PWA / Hors-ligne | ✅ Opérationnel | Saisie hors-ligne, installation, service worker |
| Journaux d'audit | ✅ Opérationnel | Traçabilité complète (10 142 entrées) |

## 6. État de la base de données (référence)

- **8 communes** couvertes : Parakou, Bembereke, Tchaourou, Nikki, Kalale, N'Dali, Perere, Sinende ;
- **9 496 infrastructures** dont 9 491 validées, 5 en attente de validation ;
- **100 %** des infrastructures géoréférencées (GPS renseigné) ;
- Répartition sectorielle : Éducation (4 440), Eau potable (1 576), Marché (1 284), Santé (872), Assainissement (444), Agriculture/Élevage (298), Administration (287), Culture/Sport/Loisirs (180), Marché à bétail (113)…

## 7. Écarts entre les exigences initiales et l'état actuel

| Exigence TDR | État avant mission | État à la fin de la mission |
|---|---|---|
| Authentification renforcée (MFA) | Absente | ✅ Opérationnelle sur 100 % des comptes admin |
| Gestion des accès (RBAC) | Faible | ✅ 4 rôles strictement cloisonnés |
| Traçabilité (journaux d'audit) | Absente | ✅ Système d'audit actif |
| Protection anti-intrusion | Faible | ✅ reCAPTCHA v3, Security Headers, HTTPS |
| Module de planification | Peu exploité | ✅ Plans annuel & triennal conformes MDGL |
| Suivi / maintenance | Partiel | ✅ Opérationnel |
| Données (qualité/complétude) | Insuffisante | ⚠️ Champs clés à 100 %, champs optionnels à compléter |
| Continuité (PCA/PRA/sauvegardes) | Absente | ⚠️ Documents rédigés, test de restauration à documenter |
| Conformité réglementaire | 54/100 | ⚠️ En cours, documents produits (PSSI, CGU, politique confidentialité) |

## 8. Diagnostic consolidé

**Points forts :**
- Plateforme fonctionnellement complète et opérationnelle en production ;
- Sécurité renforcée : MFA, reCAPTCHA, RBAC, chiffrement des données sensibles, journaux d'audit ;
- Couverture communale exhaustive (8 communes, ~9 500 infrastructures géoréférencées) ;
- Modules avancés (planification MDGL, affectations, PWA hors-ligne).

**Points de vigilance / à finaliser :**
- Complétude des champs optionnels (photos, année de réalisation, bailleur) ;
- Formalisation des preuves de tests de sécurité et de continuité (PCA/PRA) ;
- Production des rapports de recette, de conformité et de certification qualité (livrables L3 à L7) ;
- Renforcement de l'appropriation (formation, référents numériques).

## 9. Conclusion

La plateforme ARMANI a atteint un niveau de maturité nettement supérieur à l'état de départ (audit 2026 : 1,4/5). Les vulnérabilités critiques ont été corrigées et les fonctionnalités clés sont opérationnelles. Le présent diagnostic sert de référentiel pour les phases suivantes (sécurisation, continuité, qualité des données, recette, conformité).

---
*Document à transmettre officiellement et à valider par le Comité technique de suivi (ADECOB / UGP-AGORA / RSI).*
