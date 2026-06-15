# System de Gestion des Erreurs - Documentation

## Vue d'ensemble

Un système complet de gestion des erreurs a été mis en place pour assurer que:
- Les erreurs sont bien documentées et loggées
- Les utilisateurs reçoivent des messages clairs et utiles
- Les détails techniques sont cachés en production

## Composants du système

### 1. **Gestionnaire d'Exceptions Global** (`app/Exceptions/Handler.php`)

Gère tous les types d'exceptions et retourne les réponses appropriées.

**Fonctionnalités:**
- Capture des erreurs 404 (Page non trouvée)
- Capture des erreurs 403 (Accès refusé)
- Logging structuré de toutes les exceptions
- Réponses JSON pour les API
- Vues conviviales pour les erreurs

---

### 2. **Middleware de Gestion des Erreurs** (`app/Http/Middleware/ErrorHandlingMiddleware.php`)

Intercepte les requêtes et gère les exceptions en temps réel.

**Fonctionnalités:**
- Logging avec ID de trace unique (`TRACE_ID`)
- Enregistrement des détails de la requête
- Sauvegarde des erreurs dans des fichiers JSON pour l'audit
- Masquage des données sensibles

**Format du log:**
```json
{
  "trace_id": "TRACE_20260403_1234567890",
  "timestamp": "2026-04-03T10:30:45+00:00",
  "user_id": 5,
  "exception": "App\\Exceptions\\CustomException",
  "message": "Description de l'erreur",
  "file": "app/Http/Controllers/DashboardController.php",
  "line": 42
}
```

---

### 3. **Trait ErrorHandler** (`app/Traits/ErrorHandler.php`)

Un trait à utiliser dans les contrôleurs pour une gestion cohérente des erreurs.

**Méthodes disponibles:**

#### `handleException()`
```php
try {
    // Votre logique
} catch (\Exception $e) {
    return $this->handleException($e, $request, 'route.name');
}
```

#### `ensureAuthorization()`
```php
$user = $this->ensureAuthorization(auth()->user(), isCommuneAdmin: true);
```

#### `validateWithCustomMessages()`
```php
$data = $this->validateWithCustomMessages($request, [
    'email' => 'required|email',
    'code' => 'required|min:4'
], [
    'email.required' => 'L\'email est obligatoire.',
    'code.min' => 'Le code doit contenir au minimum 4 caractères.'
]);
```

#### `logAction()`
```php
$this->logAction('create', 'Infrastructure', $infrastructure->id, [
    'name' => $infrastructure->name,
    'type' => $infrastructure->type
]);
```

---

### 4. **Vues d'Erreur Conviviales**

#### 403 - Accès Refusé (`resources/views/errors/403.blade.php`)
- Message clair sur l'accès refusé
- Boutons de navigation
- Informations de contact

#### 404 - Page Non Trouvée (`resources/views/errors/404.blade.php`)
- Message explicatif
- Liens vers les pages populaires
- Support utilisateur

#### 500 - Erreur Serveur (`resources/views/errors/500.blade.php`)
- Détails de l'erreur en mode développement
- Message rassurant en production
- Boutons d'action

---

## Utilisation dans les Contrôleurs

### Exemple 1 : Gestion basique des erreurs

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ErrorHandler;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    use ErrorHandler;

    public function store(Request $request)
    {
        try {
            // Valider les données
            $data = $this->validateWithCustomMessages($request, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
            ]);

            // Vérifier les permissions
            $user = $this->ensureAuthorization(
                auth()->user(),
                isCommuneAdmin: true
            );

            // Créer la ressource
            $resource = Resource::create($data);

            // Logger l'action
            $this->logAction('create', 'Resource', $resource->id, $data);

            return redirect()->route('resources.show', $resource)
                ->with('success', 'Ressource créée avec succès.');

        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'resources.index');
        }
    }
}
```

### Exemple 2 : Exception personnalisée

```php
public function delete(Request $request, $id)
{
    try {
        $resource = Resource::findOrFail($id);
        
        if (!auth()->user()->can('delete', $resource)) {
            throw new \Exception('Vous n\'avez pas la permission de supprimer cette ressource.');
        }

        $resource->delete();
        
        $this->logAction('delete', 'Resource', $id);

        return redirect()->route('resources.index')
            ->with('success', 'Ressource supprimée avec succès.');

    } catch (\Exception $e) {
        return $this->handleException($e, $request, 'resources.index');
    }
}
```

---

## Fichiers de Log

### Emplacements

**Production/Development:**
- `storage/logs/laravel.log` - Logs généraux
- `storage/logs/errors/` - Logs d'erreurs structurés (JSON par jour)

**Accès à un log d'erreur spécifique:**
```bash
cat storage/logs/errors/2026-04-03_errors.json | jq .
```

---

## Variables d'Environnement

Aucune variable spéciale requise. Le système utilise `APP_ENV` pour déterminer:
- `local` / `testing` → Afficher les détails techniques
- `production` → Messages génériques pour l'utilisateur

---

## Bonnes Pratiques

✅ **À faire:**
- Toujours utiliser `try-catch` avec `handleException()`
- Logger les actions importantes
- Valider les entrées utilisateur
- Vérifier les permissions au début des méthodes

❌ **À éviter:**
- Laisser les exceptions non gérées
- Afficher les détails techniques à l'utilisateur (en production)
- Logger les données sensibles (mots de passe, tokens)
- Ignorer les erreurs de validation

---

## Exemple : Dashboard Controller Mise à Jour

```php
public function dashboard(Request $request)
{
    try {
        $user = auth()->user();
        if (!$user || !$user->isCommuneAdmin() || !$user->commune) {
            abort(403, 'Accès refusé. Vous devez être administrateur de commune.');
        }
        
        $commune = $user->commune;
        $stats = [
            'total_infrastructures' => $commune->infrastructures()->count(),
            'active_works' => $commune->infrastructures()
                ->join('infrastructure_works', 'infrastructures.id', '=', 'infrastructure_works.infrastructure_id')
                ->where('infrastructure_works.status', '!=', 'completed')
                ->count(),
        ];
        
        return view('commune.dashboard', compact('commune', 'stats'));
    } catch (\Exception $e) {
        \Log::error('Erreur tableau de bord commune admin: ' . $e->getMessage());
        return redirect()->route('home')->with('error', 'Une erreur est survenue.');
    }
}
```

---

## Troubleshooting

### L'erreur ne s'affiche pas
1. Vérifier le fichier `storage/logs/laravel.log`
2. S'assurer que le dossier `storage/logs` est accessible en écriture
3. Vérifier `APP_DEBUG=true` en développement

### Les logs JSON ne sont pas créés
1. Créer le dossier: `mkdir -p storage/logs/errors`
2. Verifier les permissions: `chmod 755 storage/logs/errors`

### L'utilisateur voit les détails techniques en production
1. Vérifier que `APP_ENV=production` est défini
2. Vérifier que `APP_DEBUG=false` est défini

---

## Support

Pour plus d'informations ou des questions, contactez:
- **Email:** secretariatadecob@yahoo.fr
- **Téléphone:** 0195647373
- **Horaires:** Lun - Ven : 08h - 12h30 et 15h - 17h30
