
# PR14 — Refonte complète du CRUD Infrastructures + workflow de validation

## Objectifs
1. Workflow de validation : agent soumet → admin (commune ou super) valide/rejette → statut "validé"
2. Formulaire d'ajout premium, dynamique, avec validation stricte côté serveur et client
3. Page de détails dédiée par infrastructure (accessible à l'auteur, à son commune_admin et au super_admin)
4. Édition + suppression par l'agent auteur (uniquement si non validé, ou toujours pour admins)
5. Design premium + UX fluide (stepper multi-étapes, autosave brouillon, feedback temps réel)

## 1. Base de données (migration)
Nouvelle migration `2026_07_02_120000_add_validation_workflow_to_infrastructures.php` :
- `status` ENUM('draft','pending','validated','rejected') défaut `pending`
- `validated_by` (FK users, nullable)
- `validated_at` (timestamp, nullable)
- `rejection_reason` (text, nullable)
- `submitted_at` (timestamp, nullable)
- Index composé (`status`, `commune_id`)

Rétrocompatibilité : toutes les infrastructures existantes → `status = 'validated'`.

## 2. Modèle Infrastructure
- Ajout scopes `pending()`, `validated()`, `rejected()`
- Ajout relation `validator()` (User)
- Ajout `canBeValidatedBy($user)` : super_admin OR commune_admin de la même commune
- `canBeManagedBy` mis à jour : agent ne peut éditer/supprimer que si `status !== 'validated'` (une fois validé, seuls les admins peuvent modifier)
- Statistiques publiques (`PublicController`, tableaux de bord commune/super) filtrent sur `status = 'validated'` uniquement

## 3. Contrôleur InfrastructureController
Refactor du `store`/`update` :
- Validation renforcée (regex téléphone Bénin `/^(\+229|00229)?[0-9]{8,10}$/`, latitude/longitude numériques bornées, année 1900-année courante, whitelist enums)
- Extraction d'un `FormRequest` : `App\Http\Requests\InfrastructureRequest`
- Sur `store` par un agent : `status = 'pending'`, `submitted_at = now()`
- Sur `store` par un admin : `status = 'validated'`, `validated_by = auth()->id()`
- Nouveau `show(Infrastructure $infrastructure)` (déjà routé) : contrôle d'accès via `visibleTo`
- Nouvelles actions : `validate()`, `reject(Request)`, `resubmit()` (agent renvoie après rejet)
- `index` : ajouter un onglet/section "En attente de validation" pour les admins et un badge par ligne

## 4. Nouvelles routes (protégées)
```
POST   /infrastructures/{infrastructure}/validate   → admin.access + mfa.verified
POST   /infrastructures/{infrastructure}/reject     → admin.access + mfa.verified
POST   /infrastructures/{infrastructure}/resubmit   → auteur uniquement
GET    /infrastructures/pending                      → liste dédiée pour admins
```

## 5. Vues Blade

### `infrastructures/create.blade.php` et `edit.blade.php` (refonte premium)
Formulaire en **stepper 5 étapes** avec barre de progression :
1. **Enquêteur & localisation** (nom, téléphone Bénin, date, commune (readonly si agent), arrondissement, village, hameau)
2. **Géolocalisation** (bouton "Utiliser ma position", latitude/longitude/altitude/précision + preview carte Leaflet)
3. **Infrastructure** (secteur, type, nom, année, bailleur, matériaux)
4. **État & gestion** (état fonctionnement, niveau dégradation, mode gestion, défectuosités, mesures, observation, rehabilitation)
5. **Photos** (4 emplacements, drag & drop + caméra intégrée, preview instantané, suppression)

Fonctionnalités UX :
- Validation JavaScript en temps réel par étape (Zod-like via HTML5 + JS custom léger)
- Sauvegarde brouillon localStorage (`infra_draft_{user_id}`) → restauré à l'ouverture
- Champs conditionnels dynamiques (`mode_gestion_preciser` visible seulement si mode_gestion = "Autre")
- Indicateur qualité (score de complétude en % en temps réel)
- Design cards + shadows douces, palette existante commune, animations `transition-all`

### `infrastructures/show.blade.php` (nouveau ou refonte)
Page détail premium :
- Header avec badge statut (draft/pending/validated/rejected) + boutons contextuels (Modifier, Supprimer, Valider, Rejeter, Renvoyer)
- Bloc infos regroupées par section (mêmes 5 blocs que le formulaire)
- Galerie photos (lightbox)
- Mini-carte Leaflet avec le marqueur
- Historique de validation (qui, quand, motif si rejeté)
- Lien vers `works` si présents

### `infrastructures/index.blade.php`
- Colonne statut avec badges colorés
- Onglets : "Validées" | "En attente" (admins) | "Rejetées" (auteur + admins) | "Mes brouillons" (agents)
- Bouton "Valider" / "Rejeter" (modal avec motif) sur les lignes en attente

## 6. Sécurité
- `InfrastructureRequest` : authorization via `canBeManagedBy` sur update, création restreinte aux rôles agent/commune_admin/super_admin
- `validate`/`reject` : middleware `admin.access` + vérif commune scope pour commune_admin
- Suppression : agent peut supprimer uniquement ses saisies non-validées ; admins toujours ; log audit via trait `Auditable`
- Photos : validation MIME côté serveur, taille max 10 Mo, extension whitelist
- CSRF déjà en place, on garde
- Rate-limit `throttle:60,1` sur `store`/`update`

## 7. Tests
`tests/Feature/InfrastructureCrudTest.php` :
- Agent crée → status pending
- Agent voit ses propres infras uniquement
- Admin voit toutes celles de sa commune
- Admin valide → status validated
- Admin rejette avec motif → agent peut renvoyer
- Agent ne peut pas éditer une infra validée
- Statistiques publiques ne comptent que les validées

## Fichiers créés
- `database/migrations/2026_07_02_120000_add_validation_workflow_to_infrastructures.php`
- `app/Http/Requests/InfrastructureRequest.php`
- `resources/views/infrastructures/show.blade.php` (si absent, sinon refonte)
- `resources/views/infrastructures/partials/_form-stepper.blade.php`
- `resources/views/infrastructures/partials/_status-badge.blade.php`
- `tests/Feature/InfrastructureCrudTest.php`

## Fichiers modifiés
- `app/Models/Infrastructure.php`
- `app/Http/Controllers/InfrastructureController.php`
- `app/Http/Controllers/PublicController.php` (filtrer validated)
- `app/Http/Controllers/Admin/SuperAdminDashboardController.php` (badge en attente)
- `app/Http/Controllers/Admin/CommuneAdminDashboardController.php` (badge en attente)
- `resources/views/infrastructures/create.blade.php`
- `resources/views/infrastructures/edit.blade.php`
- `resources/views/infrastructures/index.blade.php`
- `resources/views/layouts/partials/nav-authenticated.blade.php` (lien "À valider" + compteur)
- `routes/web.php`

## Notes UX / design
- Palette existante (bleu institutionnel + accents) — on ne change pas l'identité, on renforce la hiérarchie visuelle.
- Stepper responsive (accordéon sur mobile).
- Feedback (toasts) après chaque action de validation/rejet.
- Accessibilité : labels explicites, aria-invalid sur champs en erreur, focus trap dans les modaux.

Souhaites-tu que je démarre l'implémentation immédiatement, ou veux-tu ajuster un point (par ex. rendre la validation à double niveau, ou permettre à l'agent de supprimer même après validation) ?
