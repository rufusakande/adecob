# 🚨 PROCÉDURE DE GESTION DES INCIDENTS DE SÉCURITÉ
## Plateforme ARMANI — Gestion des Infrastructures des Communes du Borgou

---

## 1. Objet et définition

Cette procédure définit la conduite à tenir en cas d'**incident de sécurité** affectant la plateforme ARMANI ou ses données.

**Incident de sécurité :** tout événement compromettant la **confidentialité, l'intégrité ou la disponibilité** des données ou des systèmes : accès non autorisé, vol de compte, compromission, attaque informatique, perte ou altération de données, intrusion.

**Cas particulier — violation de données personnelles :** incident entraînant accidentellement ou illicitement la destruction, la perte, la modification, la divulgation ou l'accès non autorisé à des données à caractère personnel (RGPD — art. 4).

## 2. Catégories d'incidents

| Catégorie | Exemples |
|---|---|
| **Accès non autorisé** | Connexion avec un compte volé, tentative d'intrusion, escalade de privilèges |
| **Vol / compromission de compte** | Phishing, mot de passe divulgué, MFA contournée |
| **Malware / attaque** | Injection SQL, XSS, déni de service (DDoS), rançongiciel |
| **Perte / altération de données** | Suppression accidentelle, corruption de base, erreur de manipulation |
| **Défaillance technique** | Panne serveur, indisponibilité prolongée |
| **Violation de données personnelles** | Fuite, divulgation ou perte de données d'utilisateurs |

## 3. Niveaux de gravité

| Niveau | Gravité | Exemple | Réaction |
|---|---|---|---|
| **P1 — Critique** | Impact majeur, données sensibles exposées, service indisponible | Intrusion, fuite de données | Mobilisation immédiate, notification |
| **P2 — Élevé** | Impact significatif, compte compromis | Vol de compte admin | Action sous 24 h |
| **P3 — Moyen** | Impact limité | Tentative avortée, erreur mineure | Action sous 72 h |
| **P4 — Faible** | Impact négligeable | Spam, comportement anormal | Suivi normal |

## 4. Processus de gestion d'un incident

### Étape 1 — Détection et signalement
Tout utilisateur constatant un comportement anormal **signale immédiatement** à l'équipe technique / à l'administrateur :
- Référent sécurité : [À COMPLÉTER — nom, e-mail, téléphone]
- Support Open.bj (si incident hébergeur) : [À COMPLÉTER — téléphone / ticket]

### Étape 2 — Évaluation et classification
1. Confirmer la réalité de l'incident.
2. Déterminer le **niveau de gravité** (P1 → P4).
3. Identifier les **données affectées** et les **personnes concernées**.

### Étape 3 — Contenement (endiguement)
1. **Isoler** les systèmes touchés (révoquer les sessions, bloquer les comptes, mettre hors ligne si nécessaire : `php artisan down`).
2. **Changer les mots de passe** et révoquer les jetons d'authentification concernés.
3. **Conserver les preuves** (journaux, captures) sans altérer les données.

### Étape 4 — Éradication
1. Éliminer la cause (correction de faille, suppression du malware, blocage de l'attaque).
2. Mettre à jour les composants (Laravel, dépendances, PHP, MySQL) et renforcer la configuration.

### Étape 5 — Rétablissement
1. Restaurer les données à partir des sauvegardes si nécessaire (voir `PRA_PCA_SAUVEGARDE.md`).
2. Vérifier l'intégrité des données et le bon fonctionnement.
3. Remettre le service en ligne.

### Étape 6 — Retour d'expérience (REX)
1. Rédiger un **rapport d'incident** (chronologie, causes, impact, actions, leçons).
2. Mettre à jour les procédures et corriger les failles détectées.
3. Consigner l'incident dans le **registre des incidents**.

## 5. Notification (violation de données personnelles)

En cas de **violation de données à caractère personnel** (RGPD — art. 33 et 34) :

1. **Autorité de contrôle :** notification **sous 72 heures** avec description de l'incident, des données concernées et des mesures prises. *(Bénin : Autorité de Protection des Données Personnelles — APDP.)*
2. **Personnes concernées :** information **sans délai** si le risque pour leurs droits est élevé.
3. **Hébergeur :** informer Open.bj et solliciter son assistance (sauvegardes, journaux, preuves).

> ⚠️ La notification n'est pas exigée si l'incident est **sans risque pour les droits et libertés** des personnes (ex. données déjà chiffrées). L'évaluation du risque doit être documentée.

## 6. Registre des incidents

| N° | Date détection | Catégorie | Gravité | Description | Données affectées | Actions | Notification | Statut |
|---|---|---|---|---|---|---|---|---|
| 1 | JJ/MM/AAAA | Compromission | P2 | ... | ... | ... | Non requise | Clôturé |
| ... | | | | | | | | |

## 7. Rôles et responsabilités

| Acteur | Rôle |
|---|---|
| **Équipe technique** | Détection, contenement, éradication, rétablissement, rapport |
| **Administrateurs** | Signalement, blocage de comptes, information des utilisateurs |
| **Référent RGPD** | Évaluation du risque, notification autorité/personnes concernées |
| **Direction** | Communication externe, décisions de sanction, arbitrage |
| **Hébergeur Open.bj** | Assistance technique, journaux serveur, sauvegardes |

## 8. Contacts d'urgence

- **Équipe technique :** [À COMPLÉTER]
- **Référent RGPD :** [À COMPLÉTER — e-mail]
- **Support Open.bj :** [À COMPLÉTER — téléphone / ticket]
- **APDP (Bénin) :** [À COMPLÉTER — coordonnées officielles]

---

*Procédure à valider par l'administration. À compléter : contacts, coordonnées APDP.*
