# Système de Journaux d'Audit (Audit Logging)

## Overview

Un système complet de journalisation d'audit qui enregistre toutes les actions importantes dans l'application, notamment:

- **Connexions/Déconnexions** - Traçabilité des accès utilisateur
- **Opérations CRUD** - Création, lecture, mise à jour, suppression d'entités
- **Exports** - Téléchargement de données
- **Imports** - Chargement de données
- **Modifications d'entités** - Suivi des changements de données

---

## Architecture

### Structure de la Base de Données

**Table `audit_logs`**

```sql
- id: BigInt (Primary Key)
- user_id: BigInt Foreign Key (Utilisateur qui a effectué l'action)
- user_name: String (Nom de l'utilisateur, même si l'utilisateur est supprimé)
- action: Enum (login, logout, create, read, update, delete, export, import, password_reset, profile_update)
- auditable_type: String (Type de modèle affecté, ex: App\Models\Infrastructure)
- auditable_id: BigInt (ID de l'entité affectée)
- description: Text (Description lisible de l'action)
- old_values: JSON (Anciennes valeurs avant modification)
- new_values: JSON (Nouvelles valeurs après modification)
- ip_address: String (Adresse IP du client)
- user_agent: String (User Agent du navigateur)
- method: String (Méthode HTTP: GET, POST, PUT, DELETE)
- url: String (URL demandée)
- status: Enum (success, error, warning)
- error_message: Text (Message d'erreur si applicable)
- created_at / updated_at: Timestamp
```

**Indexes:**
- `user_id` - Pour les recherches rapides par utilisateur
- `action` - Pour les recherches rapides par action
- `auditable_type` - Pour les recherches rapides par type d'entité
- `created_at` - Pour les recherches par date

---

## Composants

### 1. Model: `AuditLog` [app/Models/AuditLog.php]

Représente une entrée du journal d'audit avec les fonctionnalités suivantes:

```php
// Relations
$auditLog->user           // L'utilisateur qui a effectué l'action
$auditLog->auditable      // L'entité modifiée (polymorphe)

// Méthodes helper
$auditLog->getActionLabel()        // Étiquette d'action lisible
$auditLog->isSuccessful()          // Vérifier si l'action a réussi
$auditLog->getChangedFields()      // Obtenir les champs modifiés
```

### 2. Service: `AuditService` [app/Services/AuditService.php]

Service central pour l'enregistrement des audits. Principales méthodes:

```php
// Enregistrement général
AuditService::log($action, $model, $oldValues, $newValues, $status, $description)

// Enregistrement spécifique
AuditService::logLogin($user)
AuditService::logLogout($user)
AuditService::logCreate($model, $attributes)
AuditService::logUpdate($model, $oldValues, $newValues)
AuditService::logDelete($model, $attributes)
AuditService::logExport($type, $count, $filters)
AuditService::logImport($type, $count, $details)
AuditService::logPasswordReset($user)

// Récupération des logs
AuditService::getAuditLogs($action, $userId, $auditableType, $limit)
AuditService::getRecentLogs($days, $limit)
AuditService::getUserActivity($user, $limit)
AuditService::getModelHistory($model, $limit)
```

### 3. Trait: `Auditable` [app/Traits/Auditable.php]

Ajoute les capacités d'audit automatique aux modèles. Utilise les événements Laravel pour suivre automatiquement les changements:

```php
use App\Traits\Auditable;

class Infrastructure extends Model
{
    use Auditable;
    // Les événements du modèle sont maintenant audités
}
```

**Événements suivis:**
- `created` - Enregistre la création avec les nouvelles valeurs
- `updated` - Enregistre les modifications avec avant/après
- `deleted` - Enregistre la suppression avec les anciennes valeurs

### 4. Listeners

#### `LogAuthenticatedLogin` [app/Listeners/LogAuthenticatedLogin.php]
Enregistre les connexions utilisateur quand l'événement `Login` se déclenche.

#### `LogAuthenticatedLogout` [app/Listeners/LogAuthenticatedLogout.php]
Enregistre les déconnexions utilisateur quand l'événement `Logout` se déclenche.

**Enregistrement dans EventServiceProvider:**
```php
protected $listen = [
    Login::class => [LogAuthenticatedLogin::class],
    Logout::class => [LogAuthenticatedLogout::class],
];
```

### 5. Controller: `AuditLogController` [app/Http/Controllers/AuditLogController.php]

Gère l'affichage et l'exportation des logs d'audit:

```php
// Routes disponibles
$controller->index()              // Affiche tous les logs avec filtrage
$controller->show($auditLog)      // Affiche les détails d'un log
$controller->userHistory($user)   // Historique d'un utilisateur spécifique
$controller->modelHistory()       // Historique d'une entité spécifique
$controller->export()             // Exporte les logs en CSV
$controller->clearOldLogs()       // Nettoie les vieux logs (admin seulement)
```

---

## Utilisation

### 1. Activation Automatique pour un Modèle

Ajoutez le trait `Auditable` au modèle:

```php
<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Infrastructure extends Model
{
    use Auditable;
}
```

À partir de maintenant, tous les changements du modèle seront automatiquement enregistrés!

### 2. Enregistrement Manuel des Actions

Pour des actions spéciales, utilisez `AuditService` directement:

```php
use App\Services\AuditService;
use App\Models\Infrastructure;

// Enregistrer une exportation
AuditService::logExport('Infrastructures', count($infrastructures), [
    'commune_id' => request('commune_id'),
    'sector' => request('sector'),
]);

// Enregistrer un import
AuditService::logImport('Infrastructures', 250, [
    'source' => 'Excel',
    'file' => 'infrastructure_data.xlsx',
]);

// Enregistrer un message d'erreur
AuditService::logError('create', $model, 'Validation failed: missing required fields');
```

### 3. Enregistrement des Exports

Dans votre contrôleur d'export:

```php
public function export(Request $request)
{
    $infrastructures = Infrastructure::where(...)->get();
    
    // Enregistrer l'export
    AuditService::logExport('Infrastructure', count($infrastructures), [
        'filters' => $request->all()
    ]);
    
    // Générer le fichier
    // ...
}
```

### 4. Visualisation des Logs

Accédez au tableau de bord d'audit (réservé aux super-admin):

```
URL: /admin/audit
Permissions: super_admin
```

---

## Routes

Toutes les routes d'audit sont protégées par l'authentification et nécessitent le rôle `super_admin`:

```php
// Affiche le dashboard d'audit avec statistiques
GET /admin/audit

// Affiche les détails d'un log spécifique
GET /admin/audit/{auditLog}

// Affiche l'historique d'un utilisateur
GET /admin/audit/user/{user}/history

// Affiche l'historique d'une entité
GET /admin/audit/model/history?type=App\Models\Infrastructure&id=1

// Exporte les logs en CSV
POST /admin/audit/export

// Supprime les logs antérieurs à X jours
POST /admin/audit/clear-old
```

---

## Modèles Audités

Les modèles suivants ont le trait `Auditable` activé:

1. **User** - Suivi des modifications de profil utilisateur
2. **Commune** - Suivi des modifications de communes
3. **Infrastructure** - Suivi complet des créations, modifications, suppressions

---

## Vues

### Dashboard Principal (`audit/index`)
- Statistiques globales (total, aujourd'hui, cette semaine, ce mois)
- Actions les plus courantes
- Utilisateurs les plus actifs
- Filtre avancé par action, utilisateur, date, statut
- Tableau avec pagination
- Export CSV

### Détail Log (`audit/show`)
- Informations générales du log
- Informations utilisateur
- Détails de la requête HTTP
- Entité et modifications associées
- Vue détaillée des changements avant/après

### Historique Utilisateur (`audit/user-history`)
- Historique complet des actions d'un utilisateur
- Filtre par action
- Timeline chronologique

### Historique Modèle (`audit/model-history`)
- Historique de modification d'une entité spécifique
- Tous les contributeurs et changements
- Changelog complet

---

## Sécurité et Permissions

### Accès au Système d'Audit
- **Super Admin seulement** - Peut voir tous les logs
- **Autres utilisateurs** - Pas d'accès

### Données Sensibles
- Les mots de passe ne sont JAMAIS enregistrés
- Les tokens d'authentification ne sont pas enregistrés
- Les données sensibles devraient être masquées dans les migrations

### Protection
- Tous les logs d'audit sont read-only (visiones uniquement)
- Les super admins peuvent nettoyer les vieux logs
- Les logs incluent l'IP et le User Agent pour la traçabilité

---

## Maintenance

### Nettoyage des Logs Anciens

Pour nettoyer les logs de plus d'un an:

```php
// Via Artisan
php artisan audit:clear-old --days=365

// Ou via l'interface web
POST /admin/audit/clear-old (avec days=365)
```

Vous pouvez aussi créer une scheduled task:

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('audit:clear-old', ['--days' => 365])->monthly();
}
```

### Metrics et Reporting

```php
// Obtenir les statistiques
$stats = [
    'total_logs' => AuditLog::count(),
    'today' => AuditLog::whereDate('created_at', today())->count(),
    'by_action' => AuditLog::selectRaw('action, COUNT(*) as count')
                            ->groupBy('action')
                            ->get(),
];

// Obtenir le top 10 des utilisateurs
$topUsers = AuditLog::selectRaw('user_name, COUNT(*) as count')
                     ->groupBy('user_name')
                     ->limit(10)
                     ->get();
```

---

## Exemples d'Utilisation Réelle

### Exemple 1: Tracker les Modifications d'Infrastructure

```php
class InfrastructureController extends Controller
{
    public function update(Request $request, Infrastructure $infrastructure)
    {
        $oldValues = $infrastructure->toArray();
        
        $infrastructure->update($request->validated());
        
        // Le trait Auditable enregistre automatiquement!
        // Pas besoin d'appel manuel ici
        
        return redirect()->back()->with('success', 'Infrastructure mise à jour');
    }
}
```

### Exemple 2: Export avec Audit

```php
public function exportInfrastructures(Request $request)
{
    $infrastructures = Infrastructure::where(
        'commune_id', $request->commune_id
    )->get();
    
    // Enregistrer l'export
    AuditService::logExport('Infrastructures', count($infrastructures), [
        'commune_id' => $request->commune_id,
        'filters' => $request->except('_token'),
    ]);
    
    // Générer et retourner le fichier
    return response()->download($file);
}
```

### Exemple 3: Consulter l'Historique

```php
// Obtenir les 50 dernières actions d'un utilisateur
$userActivity = AuditService::getUserActivity($user, 50);

foreach ($userActivity as $log) {
    echo "{$log->created_at}: {$log->user_name} - {$log->action}";
}

// Obtenir l'historique complet d'une infrastructure
$history = AuditService::getModelHistory($infrastructure, 100);

// Obtenir les logs de ce mois
$monthlyLogs = AuditService::getRecentLogs(30);
```

---

## Dépannage

### Les logs ne s'enregistrent pas

1. Vérifier que le trait `Auditable` est ajouté au modèle
2. Vérifier les listeners dans `EventServiceProvider`
3. Vérifier que la table `audit_logs` existe
4. Vérifier les logs d'erreur: `storage/logs/laravel.log`

### Les vues ne s'affichent pas

1. Vérifier que vous êtes connecté en tant que super admin
2. Vérifier que les routes d'audit sont enregistrées
3. Vérifier que le répertoire `resources/views/audit` existe

### Performance Lente

1. Purger les vieux logs: `php artisan audit:clear-old --days=90`
2. Ajouter des indexes additionnels si nécessaire
3. Considérer l'archivage des logs très anciens

---

## Points Clés à Retenir

✅ **Automatique** - Les modèles avec le trait `Auditable` enregistrent tout automatiquement
✅ **Complet** - Toutes les actions importantes (connexions, CRUD, exports) sont capturées
✅ **Sécurisé** - Seuls les super admins peuvent voir les logs
✅ **Traçable** - IP, User Agent, URL sont enregistrés
✅ **Queryable** - Recherche facile par utilisateur, action, entité, date
✅ **Exportable** - Export en CSV pour analyse externe
✅ **Maintenable** - Nettoyage automatique des vieux logs possible

---

## Fichiers Clés

| Fichier | Rôle |
|---------|------|
| `app/Models/AuditLog.php` | Modèle d'entité |
| `app/Services/AuditService.php` | Service de logging |
| `app/Traits/Auditable.php` | Trait pour audit automatique |
| `app/Listeners/LogAuthenticated*.php` | Listeners pour login/logout |
| `app/Http/Controllers/AuditLogController.php` | Contrôleur d'affichage |
| `database/migrations/*_create_audit_logs_table.php` | Migration BD |
| `resources/views/audit/` | Vues du dashboard |
| `routes/web.php` | Routes d'audit |

---

**Version:** 1.0
**Date de Création:** 04/04/2026
**Status:** ✅ Production Ready
