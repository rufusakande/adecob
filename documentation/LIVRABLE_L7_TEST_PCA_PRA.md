# LIVRABLE L7 — TEST DU PLAN DE CONTINUITÉ D'ACTIVITÉ (PCA) ET DE REPRISE APRÈS SINISTRE (PRA)
### Plateforme ARMANI (ex-ADECOB) — Communes du Borgou

**Mission :** Sécurisation, amélioration et opérationnalisation (POA 2026/ADECOB/AGORA1)
**Version :** 1.0 — **Date :** 19/08/2026
**Prestataire :** [Nom du prestataire / consultant]

---

## 1. Contexte

Conformément à l'objectif R2 du TDR (« Sauvegardes automatiques quotidiennes vérifiées et fonctionnelles », « PCA et PRA élaborés, validés et testés — au moins 1 test documenté », « Système de monitoring opérationnel », « Disponibilité ≥ 99 % »), le présent document formalise le **dispositif de continuité** de la plateforme et le **procès-verbal du test de restauration** réalisé.

## 2. Dispositif de continuité et de résilience

### 2.1 Sauvegarde des données
| Élément | Détail |
|---|---|
| Fréquence | **Quotidienne (automatique)** |
| Base de données | MySQL (base `armanib2_b25deftr`) |
| Fichiers applicatifs | Code source, configuration, fichiers téléversés (photos) |
| Emplacement | Sauvegardes externalisées (site distant / hébergeur) |
| Rétention | [Politique de rétention — à préciser] |

### 2.2 Monitoring
- Supervision de la disponibilité du site (uptime) ;
- Journaux applicatifs (Laravel) et journaux d'audit ;
- Tableau de bord de monitoring (mairie agent) ;
- Alertes sur indisponibilité.

### 2.3 Rôles et responsabilités
| Rôle | Responsabilités |
|---|---|
| Administrateur technique | Réalisation des sauvegardes, vérification, restauration |
| Responsable SI (commune) | Validation des procédures, point de contact |
| Support hébergeur | Infra, SLA, assistance |

## 3. Procédure de sauvegarde (résumé)
1. Sauvegarde automatique quotidienne de la base et des fichiers ;
2. Vérification de la complétude du fichier de sauvegarde (taille, intègre) ;
3. Rétention des sauvegardes selon la politique définie ;
4. Journalisation de la sauvegarde.

## 4. PROCÈS-VERBAL DU TEST DE RESTAURATION

### 4.1 Informations du test
| Élément | Détail |
|---|---|
| Date du test | ____________________ |
| Heure | ____________________ |
| Réalisé par | ____________________ |
| En présence de | ____________________ |
| Environnement de test | [Environnement de restauration (test / staging)] |
| Sauvegarde utilisée | ____________________ (date) |

### 4.2 Scénario de test
1. Restaurer la dernière sauvegarde de la base dans l'environnement de test ;
2. Restaurer les fichiers applicatifs associés ;
3. Vérifier l'intégrité des données (comptage des enregistrements) ;
4. Démarrer l'application et vérifier la connexion + accès aux fonctionnalités ;
5. Vérifier la présence des fichiers téléversés (photos) ;
6. Documenter le temps de restauration (RTO).

### 4.3 Résultats du test
| Étape | Attendu | Résultat | Statut |
|---|---|---|---|
| 1. Restauration base | Restauration sans erreur | | ☐ OK ☐ KO |
| 2. Restauration fichiers | Fichiers présents | | ☐ OK ☐ KO |
| 3. Intégrité données | Nombre d'enregistrements conforme | | ☐ OK ☐ KO |
| 4. Application fonctionnelle | Connexion + navigation OK | | ☐ OK ☐ KO |
| 5. Fichiers téléversés | Photos accessibles | | ☐ OK ☐ KO |
| 6. Temps de restauration (RTO) | ≤ objectif | | ☐ OK ☐ KO |

### 4.4 Conclusion du test
☐ Test **réussi** — la restauration est fonctionnelle et conforme aux objectifs de reprise (RTO/RPO).
☐ Test **réussi avec réserves** — ____________________
☐ Test **échoué** — actions correctives requises : ____________________

### 4.5 Signatures
| Rôle | Nom | Signature |
|---|---|---|
| Réalisateur du test | | |
| Responsable SI / ADECOB | | |
| UGP/AGORA | | |

## 5. Plan d'action en cas d'incident (PRA — résumé)
1. **Détection** : alerte monitoring / signalement utilisateur ;
2. **Isolation** : diagnostic de l'incident, déclaration ;
3. **Restauration** : application de la procédure de restauration (voir §3) ;
4. **Validation** : tests fonctionnels post-restauration ;
5. **Retour à la normale** : remise en service, communication aux communes ;
6. **Retour d'expérience** : analyse, documentation, amélioration de la procédure.

## 6. Indicateurs de continuité
- **RPO (perte de données acceptable) :** ≤ 24 h (sauvegarde quotidienne) ;
- **RTO (temps de reprise) :** à définir lors du test (objectif ≤ [x] h) ;
- **Disponibilité :** ≥ 99 % sur la période de déploiement ;
- **Fréquence des tests :** au moins 1 test documenté + revue annuelle.

## 7. Conclusion

Le dispositif de continuité (sauvegardes quotidiennes, PCA, PRA, monitoring) est en place. Le test de restauration documenté ci-dessus atteste de la capacité de la plateforme à être **restaurée et remise en service** en cas de sinistre, conformément à l'objectif R2 du TDR.

---
*Document à compléter avec les résultats effectifs du test (PV) et à valider par le Comité technique de suivi.*
