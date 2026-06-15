# Guide d'Intégration - Système d'Audit

## Intégration Rapide

### 1. Ajouter l'Audit à un Modèle

Pour auditer automatiquement toutes les modifications d'un modèle, ajoutez simplement le trait:

```php
<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class YourModel extends Model
{
    use Auditable;
    // Voilà! Maintenant toutes les modifications sont auditées
}
```

**Modèles déjà auditisés:**
- ✅ User
- ✅ Commune  
- ✅ Infrastructure

### 2. Enregistrer une Exportation

```php
<?php
namespace App\Http\Controllers;

use App\Services\AuditService;
use App\Models\Infrastructure;

class InfrastructureController
{
    public function export(Request $request)
    {
        $infrastructures = Infrastructure::where(...)->get();
        
        // Enregistrer l'export
        AuditService::logExport('Infrastructures', count($infrastructures), [
            'filters' => $request->all()
        ]);
        
        // Générer et retourner le fichier...
        return response()->download($file);
    }
}
```

### 3. Enregistrer une Importation

```php
public function import(Request $request)
{
    $count = 0;
    
    // Process import...
    
    // Enregistrer l'import
    AuditService::logImport('Infrastructures', $count, [
        'source' => 'Excel',
        'file' => $request->file('file')->getClientOriginalName(),
    ]);
}
```

### 4. Accéder à l'Historique

```php
use App\Services\AuditService;
use App\Models\Infrastructure;

// Historique d'un utilisateur
$logs = AuditService::getUserActivity($user);

// Historique d'une entité
$logs = AuditService::getModelHistory($infrastructure);

// Logs récents (7 jours)
$logs = AuditService::getRecentLogs(7);

// Logs spécifiques
$logs = AuditService::getAuditLogs('create', null, Infrastructure::class);
```

---

## Détails Techniques

### Événements Disponibles

Le trait `Auditable` utilise les événements de modèle Laravel:

| Événement | Quand | Données Capturées |
|-----------|-------|------------------|
| `created` | Après insertion | Toutes les nouvelles valeurs |
| `updated` | Après modification | Avant et après pour chaque champ |
| `deleted` | Après suppression | Toutes les anciennes valeurs |

### Enregistrement Manuel Avancé

Pour des cas spéciaux:

```php
use App\Services\AuditService;

// Enregistrement personnalisé complet
AuditService::log(
    action: 'custom_action',
    model: $infrastructure,
    oldValues: ['field' => 'old_value'],
    newValues: ['field' => 'new_value'],
    status: 'success',
    description: 'Description personnalisée',
    errorMessage: null
);

// Enregistrer une erreur
AuditService::logError(
    'create',
    $infrastructure,
    'Validation failed: latitude is required'
);
```

### Accès aux Logs dans le Code

```php
// Récupérer les logs d'un modèle
$infrastructure = Infrastructure::find(1);
$logs = $infrastructure->auditLogs(); // Via le trait

// Filtrer les logs
$logs = AuditLog::where('action', 'create')
    ->where('user_id', auth()->id())
    ->latest()
    ->get();

// Accéder aux détails
foreach ($logs as $log) {
    echo $log->user_name;           // Qui
    echo $log->action;               // Quoi
    echo $log->created_at;           // Quand
    echo $log->ip_address;           // D'où
    echo $log->getChangedFields();   // Changements détaillés
}
```

---

## Nettoyage et Maintenance

### Via Artisan

```bash
# Supprimer les logs de plus d'un an
php artisan audit:clear-old --days=365

# Sans confirmation
php artisan audit:clear-old --days=365 --force

# Supprimer les logs de plus d'un mois
php artisan audit:clear-old --days=30 --force
```

### Via Scheduled Task

Ajouter à `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Nettoyer les logs de plus d'un an, une fois par mois
    $schedule->command('audit:clear-old', ['--days' => 365, '--force'])
        ->monthly()
        ->at('02:00');
}
```

### Via Interface Web

Les super-admins peuvent nettoyer les logs via:
```
/admin/audit → Bouton "Clear Old Logs"
```

---

## Vérification et Tests

### Vérifier que l'Audit Fonctionne

```php
<?php
// En tinker ou dans un test
php artisan tinker

// Créer une entité
>>> $infrastructure = \App\Models\Infrastructure::create([...]);
=> // L'audit devrait avoir créé une entrée

// Vérifier les logs
>>> \App\Models\AuditLog::latest()->first();
=> AuditLog { action: 'create', ... }
```

### Test Unitaire

```php
use App\Models\Infrastructure;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_infrastructure_creation_is_audited()
    {
        $this->actingAs($user = User::factory()->create());
        
        Infrastructure::create([
            'name' => 'Test Infrastructure'
        ]);
        
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'create',
            'user_id' => $user->id,
            'auditable_type' => Infrastructure::class,
        ]);
    }
}
```

---

## Troubleshooting

### Les logs ne s'enregistrent pas

**Problème:** Aucun log créé quand j'enregistre une entité

**Solutions:**
1. Vérifier que le trait `Auditable` est ajouté au modèle
2. Vérifier que la table `audit_logs` existe: `php artisan migrate`
3. Vérifier les logs Laravel: `storage/logs/laravel.log`
4. Tester en tinker: `php artisan tinker`

### Les connexions ne sont pas loggées

**Problème:** Les events Login/Logout ne créent pas de logs

**Solutions:**
1. Vérifier que les listeners sont enregistrés dans `EventServiceProvider`
2. Vérifier que vous utilisez les événements Auth de Laravel (pas vos propres événements)
3. Tester en te connectant/déconnectant et vérifier les logs

### Les vues ne s'affichent pas

**Problème:** /admin/audit retourne 404 ou permission denied

**Solutions:**
1. Vérifier que vous êtes connecté en tant que super-admin
2. Vérifier que `super_admin` middleware fonctionne
3. Vérifier que les routes sont enregistrées: `php artisan route:list | grep audit`
4. Vérifier que les fichiers vue existent dans `resources/views/audit/`

---

## Performance et Optimisation

### Index de Base de Données

Les indexes suivants sont créés automatiquement:
- `user_id` - Recherches par utilisateur (rapide ✓)
- `action` - Recherches par type d'action (rapide ✓)
- `auditable_type` - Recherches par type d'entité (rapide ✓)
- `created_at` - Recherches par date (rapide ✓)

### Requêtes Recommended

✅ **Rapide (< 100ms)**
```php
AuditLog::where('user_id', $userId)->latest()->paginate();
AuditLog::where('action', 'create')->count();
AuditLog::whereDate('created_at', today())->get();
```

❌ **Lent (sans indexes)**
```php
AuditLog::where('description', 'like', '%something%')->get(); // Usa LIKE
AuditLog::where('new_values->key', 'value')->get(); // JSON path pas indexé
```

### Optimisation

1. **Nettoyer régulièrement:** `php artisan audit:clear-old --days=180 --force`
2. **Paginer les résultats:** Toujours utiliser `paginate()` pas `get()`
3. **Limiter les requêtes:** `limit()` dans les requêtes
4. **Cache les statistiques:** Les nombres peuvent être mis en cache 1 heure

---

## Bonnes Pratiques

✅ **À Faire**
- Utiliser le trait `Auditable` pour l'audit automatique
- Enregistrer les exports/imports importants
- Nettoyer les vieux logs régulièrement
- Inclure des descriptions lisibles
- Tester l'audit lors du développement

❌ **À Éviter**
- Modifier directement les tables `audit_logs`
- Enregistrer des données sensibles (mots de passe, tokens)
- Utiliser des descriptions vides
- Laisser s'accumuler les logs (> 1 million d'entrées)
- Oublier de tester les logs des permissions

---

## Questions Fréquentes

**Q: Où sont stockés les logs?**
R: Base de données MySQL, table `audit_logs`

**Q: Combien de place ça prend?**
R: ~1-2 KB par log. 100 000 logs ≈ 200 MB

**Q: Je peux supprimer un log?**
R: Oui, via `AuditLog::find($id)->delete()` mais ce n'est pas recommandé

**Q: Comment exporter tous les logs?**
R: Via l'UI `/admin/audit` → Bouton "Exporter", ou en tinker/console

**Q: Les logs incluent les mots de passe?**
R: Non, utilisez `hidden` ou `casts` pour les masquer

**Q: Je peux auditer les lectures aussi?**
R: Non par défaut car ça crée trop de logs. Utilisez un log custom si nécessaire

---

## Ressources

- **Documentation Complète:** `/documentation/AUDIT_SYSTEM.md`
- **Code Source:** 
  - Modèle: `app/Models/AuditLog.php`
  - Service: `app/Services/AuditService.php`
  - Trait: `app/Traits/Auditable.php`
  - Controller: `app/Http/Controllers/AuditLogController.php`
- **Routes:** `routes/web.php` (cherchez `audit`)
- **Vues:** `resources/views/audit/`

---

**Version:** 1.0
**Dernière mise à jour:** 04/04/2026
**Statut:** ✅ Production Ready
