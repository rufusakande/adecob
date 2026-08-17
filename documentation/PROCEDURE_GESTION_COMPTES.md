# 👥 PROCÉDURE DE GESTION DES COMPTES ET DES HABILITATIONS
## Plateforme ARMANI — Gestion des Infrastructures des Communes du Borgou

---

## 1. Objet

Cette procédure définit le cycle de vie des **comptes utilisateurs** et la gestion des **habilitations** (droits) sur la plateforme ARMANI : création, approbation, modification, suspension, suppression et revue périodique.

**Principe directeur :** **moindre privilège** — chaque compte ne dispose que des droits strictement nécessaires à sa mission.

## 2. Rôles et habilitations

| Rôle | Créé par | Droits |
|---|---|---|
| **super_admin** | Super administrateur | Accès global : communes, utilisateurs, validation, paramètres, exports |
| **commune_admin** | Super administrateur | Gestion de sa commune : utilisateurs, infrastructures, validation |
| **agent** | Super admin / admin de commune | Saisie, collecte, modification des infrastructures |
| **public_user** | Auto-inscription (validation requise) | Consultation publique uniquement |

## 3. Création des comptes

### 3.1 Comptes internes (agents, administrateurs)
1. **Demande :** la commune / la direction transmet les informations (nom, prénom, e-mail, fonction, commune).
2. **Vérification :** l'administrateur vérifie l'identité et le besoin.
3. **Création :** le compte est créé avec le rôle minimal adapté.
4. **Information :** l'utilisateur reçoit ses identifiants et signe la `CHARTE_INFORMATIQUE.md`.

### 3.2 Comptes publics
1. L'utilisateur s'inscrit via le formulaire public (protégé par **reCAPTCHA**).
2. **Validation obligatoire :** l'administrateur **approuve ou rejette** l'inscription avant tout accès.

## 4. Approbation et activation

- Toute inscription doit être **approuvée** avant activation (`is_approved`).
- Le rejet est motivé (compte non conforme, doublon, demande abusive).
- Les comptes inactifs peuvent être désactivés après [À COMPLÉTER — ex. 6 mois] d'inactivité.

## 5. Modification des comptes

| Type de modification | Règle |
|---|---|
| Changement de mot de passe | Par l'utilisateur (mot de passe robuste) ou réinitialisation par l'admin |
| Réinitialisation de mot de passe oublié | Procédure de réinitialisation sécurisée (lien à usage unique) |
| Activation MFA | **Obligatoire** pour les administrateurs (super_admin, commune_admin) |
| Changement de rôle | Uniquement sur demande validée (voir point 6) |
| Correction de données | Par l'utilisateur ou l'administrateur (nom, e-mail, commune) |

## 6. Élévation de droits (changement de rôle)

1. Toute **élévation de droits** (ex. agent → commune_admin) fait l'objet d'une demande écrite auprès du super administrateur.
2. Le besoin est justifié et le changement est **tracé** dans le journal d'audit.
3. L'ancien rôle est révoqué après changement (pas d'accumulation de droits).

## 7. Suspension et désactivation

Un compte est **suspendu** dans les cas suivants :
- Utilisation anormale ou suspecte (tentatives répétées, comportement inapproprié) ;
- Non-respect de la charte informatique ;
- Signalement d'un incident de sécurité.

La suspension peut être **temporaire** (enquête) ou **définitive** (suppression).

## 8. Suppression des comptes

- **Compte interne :** suppression à la fin de la mission, au départ de l'utilisateur, ou sur demande motivée de la commune.
- **Compte public :** suppression sur demande de la personne (droit à l'effacement — voir `PROCEDURE_DROITS_RGPD.md`) ou en cas de compte non conforme.
- La suppression est **tracée** dans le journal d'audit (qui, quoi, quand).
- **Recommandation :** préférer la **désactivation** à la suppression définitive pour conserver la traçabilité historique des saisies ; l'archivage des données reste possible.

## 9. Revue périodique des comptes

Une **revue des comptes** est réalisée **au minimum une fois par an** (recommandation : semestrielle) par le super administrateur :

1. Lister tous les comptes actifs (rôle, commune, dernière connexion).
2. Identifier les comptes inactifs, les rôles inappropriés, les doublons.
3. Désactiver / supprimer les comptes obsolètes.
4. Vérifier que les **MFA** des administrateurs sont actives.
5. Consigner la revue (date, actions, anomalies) dans le registre des comptes.

## 10. Journal d'audit et registre des comptes

- Toutes les actions sensibles (création, modification, approbation, suspension, suppression, connexions) sont **journalisées** par la plateforme (journal d'audit intégré).
- Le super administrateur tient un **registre des comptes** :

| N° | Date création | Utilisateur | Rôle | Commune | Statut | Dernière revue |
|---|---|---|---|---|---|---|
| 1 | JJ/MM/AAAA | [Nom] | agent | Parakou | Actif | JJ/MM/AAAA |
| ... | | | | | | |

## 11. Responsabilités

| Acteur | Responsabilité |
|---|---|
| **Super administrateur** | Création/approbation des comptes admin, revue périodique, arbitrage des élévations |
| **Administrateur de commune** | Gestion des agents de sa commune, validation des inscriptions |
| **Utilisateurs** | Confidentialité de leurs identifiants, respect de la charte |

---

*Procédure à valider par l'administration. À compléter : durée d'inactivité avant désactivation, fréquence de revue retenue.*
