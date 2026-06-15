
Note: la commande `/skill:redesign` cible une refonte **visuelle** (palette / typo / layout). Ta demande est une **refonte d'architecture des rôles et espaces** d'une app Laravel — je ne déclenche donc pas le skill redesign et je te propose plutôt un plan d'exécution structuré.

---

## Phase 0 — Nettoyage & cohérence (rapide, avant la refonte)

Objectif : aligner la base avant de toucher à l'architecture.

1. **Unification du nom de la plateforme**
   - Définir le nom officiel (ex. "ADECOB — Plateforme de gestion des infrastructures du Borgou").
   - Remplacer toutes les variantes dans : `config/app.php` (`APP_NAME`), `.env.example`, `resources/views/layouts/app.blade.php`, `welcome.blade.php`, `home.blade.php`, emails (`resources/views/emails/*`), `contact.blade.php`, footer, balises `<title>` et meta.
2. **Page Contact — données réelles**
   - Remplacer adresse / téléphone / email fictifs par les vraies coordonnées ADECOB (à fournir).
   - Vérifier `ContactController` + mail destinataire (`config/mail.php`, `App\Mail\ContactFormMail`).
3. **Vérification de l'erreur 500 (déjà corrigée par toi)**
   - Revue rapide de `app/Exceptions/Handler.php`, `ErrorHandlingMiddleware`, `errors/500.blade.php`, et des logs récents pour confirmer qu'aucune trace de la cause initiale ne subsiste.

Livrable : 1 PR "chore: nom plateforme + contact réel + check 500".

---

## Phase 1 — Refonte des rôles et espaces (le gros morceau)

### 1.1 Matrice des rôles (source de vérité)

| Rôle | Inscription | Validation par | Périmètre données | Actions |
|---|---|---|---|---|
| **public** | aucune | — | toutes communes, lecture stats publiques | voir page d'accueil + dashboard public (stats + filtres commune/type) |
| **agent collecteur** (défaut à l'inscription) | oui (avec commune) | super admin OU admin de sa commune | sa commune (lecture) | CRUD **uniquement sur ses propres données** |
| **admin commune** | non (promu depuis agent par super admin) | — | sa commune uniquement | CRUD sur toutes les données de sa commune + valider inscriptions de sa commune |
| **super admin** | non (seed initial + nommé par un autre super admin) | — | toutes communes | tout + nommer admins commune + nommer super admins + valider toute inscription + vider BDD |

Règles invariantes :
- 1 utilisateur ↔ 1 commune.
- 1 utilisateur ↔ 1 rôle effectif (super_admin | commune_admin | agent).
- Plusieurs admins possibles pour la même commune.
- Le rôle `public_user` est supprimé (le public ne s'inscrit plus).

### 1.2 Page d'accueil publique (refonte)

- `GET /` → nouvelle landing publique (présentation ADECOB + stats agrégées + CTA "S'inscrire" / "Se connecter").
- `GET /infrastructures/public` → vue publique en lecture seule, filtrable par commune et par type, **sans données nominatives** ni actions.
- Supprimer la redirection actuelle `/` → `register.form`.

### 1.3 Inscription & validation

- Formulaire d'inscription : `nom`, `prenom`, `telephone`, `email`, `commune_id` (select), `password`, `password_confirmation`, case CGU.
- À l'inscription : `role = 'agent'`, `is_approved = false`, `commune_id` obligatoire.
- Redirection systématique vers `/registration/pending` jusqu'à validation.
- Notifications : email à tous les super admins + admins de la commune choisie.
- Validation : super admin (toutes communes) ou admin commune (sa commune uniquement). Approbation = `is_approved=true`, `approved_at=now()`. Rejet = soft delete + notification.

### 1.4 Espaces (3 layouts distincts)

```
/                          → landing publique
/infrastructures/public    → vue publique
/login, /register, ...     → auth

/super-admin/...           → middleware: auth + super.admin
/commune-admin/...         → middleware: auth + commune.admin
/agent/...                 → middleware: auth + agent
```

Chaque espace a **son propre layout Blade** (`layouts/super-admin.blade.php`, `layouts/commune-admin.blade.php`, `layouts/agent.blade.php`) avec sa nav latérale dédiée.

**Espace super admin** : dashboard global (KPIs par commune), gestion utilisateurs (lister, approuver, rejeter, nommer admin commune, nommer super admin, désactiver), gestion communes (CRUD), toutes infrastructures (CRUD), audit logs, import/export, "vider BDD".

**Espace admin commune** : dashboard de sa commune, gestion utilisateurs de sa commune (approuver/rejeter agents), infrastructures de sa commune (CRUD complet), audit logs de sa commune, import/export limité à sa commune.

**Espace agent collecteur** : dashboard limité à ses propres collectes, ajouter infrastructure (forcément dans sa commune, `created_by = self`), modifier/supprimer **uniquement ses créations**, vue lecture seule des autres données de sa commune.

### 1.5 Autorisations (Policies + Gates)

- Créer `InfrastructurePolicy`, `UserPolicy`, `CommunePolicy` avec règles `viewAny`, `view`, `create`, `update`, `delete` exprimant la matrice ci-dessus.
- Remplacer les `if ($user->role === ...)` éparpillés par `$this->authorize(...)` dans les contrôleurs.
- Helpers `User::scopeToVisibleInfrastructures()` pour centraliser le filtrage par commune/ownership.

### 1.6 Traçabilité (audit)

- Étendre `Auditable` trait à `Infrastructure`, `InfrastructureWork`, `MairieAgentData`, `User`, `Commune`.
- Champs loggés : `user_id`, `action`, `model`, `model_id`, `changes (json)`, `ip`, `user_agent`, `created_at`.
- Vue audit accessible : super admin (tout), admin commune (sa commune uniquement).

### 1.7 Migrations DB

- `users` : retirer `public_user` de l'enum `role` (data migration : aucun en prod normalement), s'assurer que `commune_id` est `NOT NULL` pour `agent` et `commune_admin`, ajouter `telephone`, `prenom` si absents, ajouter `rejected_at`.
- `infrastructures` : confirmer `user_id` (créateur) + `commune_id` `NOT NULL`.
- Supprimer/déprécier le système `access_code` des communes (mis en pause selon ta demande).

### 1.8 Redirections post-login

`AuthController::login` redirige selon rôle :
- super_admin → `/super-admin/dashboard`
- commune_admin → `/commune-admin/dashboard`
- agent → `/agent/dashboard`
- non approuvé → `/registration/pending`

### 1.9 Middleware

- Garder `super.admin`, renommer/clarifier `commune.admin` (sa commune uniquement), créer `agent` middleware.
- `check.approval` reste mais redirige vers `/registration/pending` au lieu du home.
- Retirer `EnsureCommuneSelected` (la commune est désormais portée par l'utilisateur, plus de sélection runtime).

---

## Détails techniques

- **Stack** : Laravel 10+ (PHP, Blade, MySQL), Bootstrap actuel conservé.
- **Tests** : feature tests par rôle dans `tests/Feature/Roles/` (super_admin, commune_admin, agent, public) couvrant accès et CRUD.
- **Seeders** : `SuperAdminSeeder` (1 compte par défaut), `CommuneSeeder` (8 communes du Borgou), pas de fake data en prod (`--env=production` skip).
- **Audit existant** : la table `audit_logs` a été créée puis droppée par les migrations 2026_04_04 — il faudra **recréer la table** avant d'activer la traçabilité étendue.

---

## Ordre d'exécution proposé (PRs séparées, courtes, vérifiables)

1. **PR1 — Nettoyage** : nom plateforme + contact réel + check 500.
2. **PR2 — Migrations & modèle rôles** : enum role nettoyé, champs users, suppression access_code, recréation audit_logs.
3. **PR3 — Landing publique + vue infrastructures publique**.
4. **PR4 — Inscription refondue + workflow validation** (super admin + admin commune).
5. **PR5 — Espace super admin** (layout + dashboard + gestion users/communes).
6. **PR6 — Espace admin commune** (layout + dashboard + validation users de sa commune + CRUD scoped).
7. **PR7 — Espace agent collecteur** (layout + dashboard + CRUD ownership-only).
8. **PR8 — Policies + audit étendu + tests par rôle**.

---

## Questions avant de coder

1. **Compte super admin initial** : email/nom à seeder par défaut ? (sinon je mets `admin@adecob.bj` + mot de passe à changer au 1er login)
2. **Coordonnées Contact réelles** : adresse / téléphone / email officiels ADECOB ?
3. **Nom officiel exact de la plateforme** à figer partout ?
4. **Périmètre public** : la page publique montre quoi exactement — uniquement compteurs agrégés, ou liste détaillée d'infrastructures (sans auteur) ?

Dis-moi sur lesquels tu veux que je démarre (je suggère **PR1 + réponses aux 4 questions** en parallèle).
