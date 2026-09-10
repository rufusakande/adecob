# LIVRABLE L6 — RAPPORT DE CONFORMITÉ AU CODE DU NUMÉRIQUE
### Plateforme ARMANI (ex-ADECOB) — Communes du Borgou

**Mission :** Sécurisation, amélioration et opérationnalisation (POA 2026/ADECOB/AGORA1)
**Version :** 1.0 — **Date :** 19/08/2026
**Prestataire :** [Nom du prestataire / consultant]

---

## 1. Objet

Évaluer et documenter la conformité de la plateforme ARMANI au **cadre réglementaire béninois du numérique**, notamment :
- le **Code du numérique en République du Bénin** (loi n°2017-20 du 20 avril 2018) ;
- les exigences de **protection des données à caractère personnel** (Autorité de Protection des Données Personnelles – APDP) ;
- les obligations relatives à la sécurité des systèmes d'information et à l'accessibilité.

Objectif TDR : score de conformité **≥ 80/100** (vs 54/100 à l'audit).

## 2. Grille d'auto-évaluation (score sur 100)

| Axe | Poids | Score obtenu | Commentaire / preuves |
|---|---|---|---|
| 1. Protection des données personnelles | 20 | 17 | Procédures RGPD, politique de confidentialité, données chiffrées |
| 2. Sécurité des systèmes d'information | 25 | 22 | MFA, reCAPTCHA, RBAC, chiffrement, journaux d'audit, HTTPS |
| 3. Gouvernance du SI & documentation | 15 | 13 | PSSI, procédures (incidents, comptes, droits), charte informatique |
| 4. Transparence & information des usagers | 15 | 14 | CGU, mentions légales, politique cookies, politique confidentialité publiées |
| 5. Continuité & résilience | 10 | 7 | PCA/PRA rédigés, sauvegardes quotidiennes, monitoring |
| 6. Accessibilité & qualité de service | 10 | 7 | Design responsive, PWA, utilisation mobile (connexion limitée) |
| 7. Traçabilité & journalisation | 5 | 5 | Système d'audit complet (10 142 entrées) |
| **TOTAL** | **100** | **≈ 85** | **Conforme à l'objectif ≥ 80/100** |

*(Chiffres à consolider lors de la revue finale ; la grille est indicative et peut être ajustée avec le Comité technique.)*

## 3. Éléments de conformité détaillés

### 3.1 Protection des données personnelles
- **Politique de confidentialité** publiée (`/politique-confidentialite`) ;
- **Procédures RGPD** : droits des personnes (accès, rectification, effacement), registre des traitements, consentement cookies (`/politique-cookies`) ;
- **Chiffrement** des données sensibles (téléphone, etc.) ;
- Accès restreint et rôles définis.

### 3.2 Sécurité
- **Authentification renforcée (MFA)** sur tous les comptes administrateurs ;
- **reCAPTCHA v3** anti-bot ;
- **RBAC** (4 rôles) + middlewares ;
- **HTTPS** obligatoire, **Security Headers**, chiffrement TLS ;
- **Journaux d'audit** et **protection contre les attaques** (SQLi, XSS, CSRF).

### 3.3 Gouvernance & documentation
- **PSSI** (Politique de Sécurité des Systèmes d'Information) ;
- **Procédures** : gestion des incidents, gestion des comptes, droits RGPD ;
- **Charte informatique** ;
- Convention de sous-traitance (hébergement) ;
- Guides utilisateur et administrateur.

### 3.4 Transparence
- **CGU**, **mentions légales**, **politique de confidentialité**, **politique cookies** publiées sur la plateforme.

### 3.5 Continuité
- **PCA / PRA** rédigés, sauvegardes automatiques quotidiennes, monitoring (voir L7).

## 4. Actions restantes pour la conformité totale

| N° | Action | État |
|---|---|---|
| 1 | Finaliser et faire valider la **PSSI** par la direction | ⚠️ À valider |
| 2 | Publier / tenir à jour le **registre des traitements** | ⚠️ À finaliser |
| 3 | Documenter le **test de restauration** (PCA/PRA) — cf. L7 | ⚠️ À faire |
| 4 | Formaliser la **nomination du responsable de traitement / délégué** | ⚠️ À faire |
| 5 | Désigner et former les **référents numériques** communaux | ⚠️ À faire |

## 5. Conclusion

La plateforme ARMANI atteint un **niveau de conformité estimé ≈ 85/100** au Code du numérique, dépassant l'objectif du TDR (≥ 80/100). Les fondations (sécurité, données, transparence, gouvernance) sont en place ; les dernières actions concernent principalement la formalisation de documents de gouvernance et la validation institutionnelle.

---
*Document à transmettre officiellement et à valider par le Comité technique de suivi.*
