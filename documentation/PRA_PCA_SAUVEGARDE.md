# 💾 PLAN DE SAUVEGARDE & RESTAURATION (PRA / PCA)
## Plateforme ARMANI — Hébergement Open.bj (offre DIAMOND)

---

## 1. Objectifs

Ce plan définit la politique de **sauvegarde**, de **restauration** et de **continuité d'activité** de la plateforme ARMANI, afin de :

- Prévenir toute **perte de données** (base MySQL, fichiers de photos).
- Garantir la **reprise rapide** du service en cas d'incident (panne serveur, erreur humaine, sinistre).
- Satisfaire aux exigences de l'audit AGORA TERRAIN et aux obligations de l'association.

**Cibles :**
- **RPO (perte de données maximale admise) :** 24 heures
- **RTO (délai de reprise maximal) :** 24 à 48 heures

## 2. Périmètre à sauvegarder

| Élément | Contenu | Support |
|---|---|---|
| **Base de données** | MySQL `adecob_db` (infrastructures, communes, utilisateurs, journaux) | Sauvegarde MySQL |
| **Fichiers** | Photos des infrastructures (`storage/app/public`), documents, exports | Sauvegarde fichiers |
| **Code source** | Application Laravel (hors `vendor/` et `node_modules/`) | Gestion de versions Git + archive |
| **Configuration** | Fichier `.env`, configs serveur | Sauvegarde sécurisée |

## 3. Politique de sauvegarde (Open.bj — Backup support inclus)

### 3.1 Sauvegardes automatiques (hébergeur)
L'offre **DIAMOND** d'Open.bj inclut le **support des sauvegardes**. À formaliser avec l'hébergeur :

- **Fréquence recommandée :** sauvegarde **quotidienne** de la base de données et des fichiers.
- **Rétention :** conserver au minimum **7 à 30 jours** de sauvegardes (historique).
- **Vérification :** contrôle mensuel de l'intégrité des sauvegardes (fichier non corrompu, ouvrable).

### 3.2 Sauvegardes manuelles / complémentaires
En complément, l'équipe technique peut réaliser une sauvegarde manuelle avant toute opération sensible (mise à jour, import massif, modification majeure) :

- **Base MySQL** : `mysqldump` (via SSH ou l'outil d'export du panel) → fichier `.sql` compressé.
- **Fichiers** : copie du dossier `storage/app/public` (photos) en archive `.zip`.
- **Stockage** : téléchargement local sécurisé + conservation d'une copie hors site (disque externe / cloud) si possible.

## 4. Procédure de restauration (pas à pas)

### 4.1 Restauration de la base de données
1. Accéder au panel d'hébergement Open.bj (ou via SSH/phpMyAdmin).
2. Télécharger le fichier de sauvegarde `.sql` le plus récent et **valide**.
3. Vérifier que personne n'écrit dans la base pendant l'opération (mettre le site en maintenance si nécessaire : `php artisan down`).
4. Importer le fichier dans MySQL (phpMyAdmin → Import, ou `mysql -u user -p adecob_db < sauvegarde.sql`).
5. Vérifier les données : nombre d'infrastructures, communes, utilisateurs.
6. Lever la maintenance (`php artisan up`) et contrôler le site.

### 4.2 Restauration des fichiers (photos)
1. Télécharger l'archive des fichiers la plus récente.
2. Remplacer le contenu de `storage/app/public` par l'archive.
3. Vérifier le lien symbolique `storage` (public/storage → storage/app/public) et les permissions.
4. Contrôler l'affichage des photos sur les fiches.

### 4.3 Restauration du code source
1. Cloner / récupérer la dernière version stable depuis **Git**.
2. Réinstaller les dépendances : `composer install --no-dev --optimize-autoloader`.
3. Reconfigurer `.env` (base, mail, clés).
4. Appliquer les migrations si nécessaire : `php artisan migrate --force`.
5. Nettoyer les caches : `php artisan config:cache && php artisan route:cache`.

## 5. Test de restauration

- **Fréquence :** **test mensuel** de restauration (sur un environnement de test de préférence).
- **Objectif :** vérifier que la sauvegarde est exploitable et que la procédure est maîtrisée.
- **Traçabilité :** consigner chaque test dans le registre des sauvegardes (date, résultat, anomalies).

## 6. Registre des sauvegardes

| Date | Type | Contenu | Taille | Support | Restauration testée | Responsable | Statut |
|---|---|---|---|---|---|---|---|
| JJ/MM/AAAA | Auto | Base + fichiers | ... | Open.bj | Non | [Nom] | OK |
| JJ/MM/AAAA | Manuelle | Base | ... | Local | Oui | [Nom] | OK |
| ... | | | | | | | |

## 7. Gestion des incidents (PCA)

### Scénarios et actions
| Incident | Action immédiate | Rétablissement |
|---|---|---|
| **Suppression accidentelle de données** | Arrêter toute écriture, prévenir le référent | Restauration de la dernière sauvegarde valide |
| **Panne serveur / hébergeur** | Ouvrir un ticket Open.bj (priorité) | Restauration sur l'hébergement ou serveur de secours |
| **Compromission de compte** | Changer les mots de passe, révoquer les sessions, analyser le journal d'audit | Remise en état + notification si violation de données |
| **Sinistre (vol, incendie)** | Utiliser la copie hors site | Restauration complète sur nouvelle infrastructure |
| **Erreur de mise à jour** | Retour à la version précédente (Git) | Restauration code + données |

### Contacts en cas d'incident
- **Équipe technique :** [À COMPLÉTER — nom, téléphone]
- **Support Open.bj :** [À COMPLÉTER — téléphone / ticket]
- **Référent RGPD :** [À COMPLÉTER — e-mail]
- **Autorité de contrôle (si violation de données) :** notification sous **72 h**

## 8. Responsabilités

| Acteur | Responsabilité |
|---|---|
| **Hébergeur (Open.bj)** | Sauvegardes techniques de l'offre, disponibilité des serveurs, support |
| **Équipe technique** | Sauvegardes manuelles, tests de restauration, exécution des procédures |
| **Administrateurs** | Vérification périodique, signalement des anomalies, validation des restaurations |
| **Direction / référent RGPD** | Arbitrage, validation, notification en cas de violation |

---

*Plan à valider avec l'hébergeur Open.bj (fréquences réelles, rétention, procédure de restauration proposée).*
