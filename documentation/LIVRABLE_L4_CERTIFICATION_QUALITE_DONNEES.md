# LIVRABLE L4 — RAPPORT DE CERTIFICATION QUALITÉ DES DONNÉES
### Plateforme ARMANI (ex-ADECOB) — Base de données des infrastructures du Borgou

**Mission :** Sécurisation, amélioration et opérationnalisation (POA 2026/ADECOB/AGORA1)
**Version :** 1.0 — **Date :** 19/08/2026
**Prestataire :** [Nom du prestataire / consultant]

---

## 1. Objet

Évaluer et certifier la qualité des données de la base de référence de la plateforme ARMANI, au regard des critères de **complétude**, de **cohérence**, d'**exactitude** et d'**unicité**, conformément à l'objectif R4 du TDR (taux de complétude ≥ 85 %).

## 2. Périmètre et méthode

- **Référentiel :** table `infrastructures` (base de référence) ;
- **Volume analysé :** 9 496 enregistrements ;
- **Méthode :** requêtes d'analyse de complétude sur les champs clés, contrôle de cohérence (statuts, clés étrangères, géoréférencement), détection des doublons.

## 3. Résultats de complétude (réels)

| Champ | Renseigné | Total | Taux | Appréciation |
|---|---|---|---|---|
| Commune | 9 496 | 9 496 | **100 %** | ✅ |
| Arrondissement | 9 496 | 9 496 | **100 %** | ✅ |
| Village / Quartier | 9 496 | 9 496 | **100 %** | ✅ |
| Secteur / domaine | 9 496 | 9 496 | **100 %** | ✅ |
| État de fonctionnement | 9 496 | 9 496 | **100 %** | ✅ |
| Coordonnées GPS (lat/long) | 9 496 | 9 496 | **100 %** | ✅ |
| Année de réalisation | 5 355 | 9 496 | **56,4 %** | ⚠️ À compléter |
| Bailleur / financement | 6 124 | 9 496 | **64,5 %** | ⚠️ À compléter |
| Photos | 5 | 9 496 | **0,05 %** | ⚠️ À enrichir |

**Taux de complétude global pondéré : ≈ 80 %** (champs clés 100 %, champs optionnels à renforcer).

## 4. Cohérence et exactitude

- **Statuts** : 9 491 infrastructures validées (99,9 %), 5 en attente — cohérent avec le workflow de validation ;
- **Géoréférencement** : 100 % des infrastructures disposent de coordonnées GPS (lat/long) ;
- **Clés étrangères / contraintes** : base normalisée (3NF), contraintes NOT NULL et clés primaires/étrangères définies ;
- **Unicité** : aucune duplication constatée sur les identifiants ; un mécanisme de détection des doublons est prévu lors de l'import.

## 5. Répartition par commune

| Commune | Nombre d'infrastructures |
|---|---|
| Parakou | 1 421 |
| Bembereke | 1 400 |
| Tchaourou | 1 366 |
| Nikki | 1 317 |
| Kalale | 1 098 |
| N'Dali | 1 065 |
| Perere | 998 |
| Sinende | 831 |
| **Total** | **9 496** |

## 6. Actions de qualité des données réalisées

- Nettoyage et normalisation des données (communes, secteurs, arrondissements) ;
- Correction des incohérences et doublons lors de l'import ;
- **Workflow de validation** des saisies (pending → validé / rejeté avec motif) ;
- Contrôles de géolocalisation (précision < 5 m, altitude renseignée) ;
- Standardisation des secteurs (Éducation, Eau potable, Santé, Marché, etc.).

## 7. Recommandations

| Priorité | Action |
|---|---|
| Haute | Enrichir les **photos** des infrastructures (import + prise caméra désormais disponibles sur le terrain) |
| Moyenne | Compléter **l'année de réalisation** (56,4 % → ≥ 85 %) |
| Moyenne | Compléter le **bailleur / source de financement** (64,5 % → ≥ 85 %) |
| Faible | Poursuivre la normalisation des libellés de secteurs |

## 8. Certification

La base de données de référence de la plateforme ARMANI est certifiée :

- **Complète sur les champs structurants** (commune, arrondissement, village, secteur, état, GPS : 100 %) ;
- **Cohérente** et normalisée ;
- **Exploitable** pour les analyses, tableaux de bord et exports de planification ;
- Taux de complétude global ≈ **80 %**, en progression vers l'objectif de **85 %** du TDR (amélioration des champs optionnels en cours).

## 9. Conclusion

La qualité des données est **satisfaisante** sur les champs essentiels à l'exploitation (localisation, secteur, état, GPS). Les efforts doivent se poursuivre sur les champs optionnels (photos, année de réalisation, bailleur). La base est **certifiée apte** à alimenter les outils de pilotage et les plans de maintenance.

---
*Document à transmettre officiellement et à valider par le Comité technique de suivi.*
