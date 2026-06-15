# 🛠️ Guide d'Implémentation - Politique de Mots de Passe

## Pour Développeurs - Ajouter la Politique à d'Autres Contrôleurs

### 📋 Checklist Rapide

Si tu as besoin d'ajouter la validation de mot de passe robuste à un autre contrôleur :

```
□ Importer StrongPassword
□ Créer un FormRequest personnalisé
□ Utiliser le FormRequest dans le contrôleur
□ Afficher les critères dans la vue
□ Ajouter le JavaScript de validation (optionnel)
```

---

## 🔧 Étape 1: Importer et Utiliser

### Option A: Utiliser un FormRequest Existant

```php
use App\Http\Requests\RegisterRequest;

class MyAuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        // $validated['password'] est maintenant sécurisé
    }
}
```

### Option B: Créer un FormRequest Personnalisé

```php
// app/Http/Requests/MyCustomRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\StrongPassword;

class MyPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'confirmed',
                new StrongPassword() // ← La règle
            ],
            'new_password_confirmation' => 'required|same:new_password'
        ];
    }

    public function messages(): array
    {
        return [
            'new_password.required' => 'Le nouveau mot de passe est obligatoire.',
            'new_password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ];
    }
}
```

---

## 🖼️ Étape 2: Afficher les Critères dans la Vue

### Option A: Passer les Critères depuis le Contrôleur

```php
// Dans le contrôleur
public function showChangePasswordForm()
{
    $passwordCriteria = PasswordPolicy::getCriteria();
    return view('my.change-password', compact('passwordCriteria'));
}
```

### Option B: Inclure dans la Vue

```blade
{{-- resources/views/my/change-password.blade.php --}}

@php
    use App\Services\PasswordPolicy;
    $passwordCriteria = PasswordPolicy::getCriteria();
@endphp

<form method="POST" action="{{ route('password.change') }}">
    @csrf
    
    <div class="mb-3">
        <label for="new_password">Nouveau Mot de Passe</label>
        <input type="password" id="new_password" name="new_password" class="form-control">
        
        {{-- Critères --}}
        <div class="password-criteria mt-2">
            <ul class="list-unstyled">
                @foreach($passwordCriteria as $criterion)
                    <li class="criteria-item" data-regex="{{ $criterion['regex'] }}">
                        <i class="fas {{ $criterion['icon'] }}"></i>
                        {{ $criterion['requirement'] }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</form>
```

---

## 🎯 Étape 3: Ajouter la Validation JavaScript (Optionnel)

### Code Simple

```javascript
// Dans la vue
<script>
const passwordInput = document.getElementById('new_password');
const criteriaItems = document.querySelectorAll('.criteria-item');

const criteria = [
    { regex: /.{10,}/, name: 'Longueur' },
    { regex: /[A-Z]/, name: 'Majuscules' },
    { regex: /[a-z]/, name: 'Minuscules' },
    { regex: /[0-9]/, name: 'Chiffres' },
    { regex: /[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/, name: 'Spéciaux' }
];

passwordInput.addEventListener('input', function() {
    criteria.forEach((criterion, index) => {
        const isMet = criterion.regex.test(this.value);
        const item = criteriaItems[index];
        
        if (isMet) {
            item.classList.add('met');
            item.classList.remove('unmet');
        } else {
            item.classList.add('unmet');
            item.classList.remove('met');
        }
    });
});
</script>

<style>
.criteria-item.met {
    color: #28a745;
}

.criteria-item.unmet {
    color: #6c757d;
}
</style>
```

---

## 💻 Exemple Complet: Changement de Mot de Passe

### Contrôleur

```php
<?php
namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function showChangePasswordForm()
    {
        return view('password.change');
    }

    public function updatePassword(ChangePasswordRequest $request)
    {
        try {
            $validated = $request->validated();
            
            // Vérifier le mot de passe actuel
            if (!Hash::check($validated['current_password'], auth()->user()->password)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }
            
            // Mettre à jour le mot de passe
            auth()->user()->update([
                'password' => Hash::make($validated['new_password'])
            ]);
            
            return redirect('/')->with('success', 'Mot de passe changé avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Une erreur est survenue.']);
        }
    }
}
```

### FormRequest

```php
<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\StrongPassword;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, auth()->user()->password)) {
                        $fail('Le mot de passe actuel est incorrect.');
                    }
                }
            ],
            'new_password' => [
                'required',
                'string',
                'confirmed',
                'different:current_password',
                new StrongPassword()
            ],
            'new_password_confirmation' => 'required|same:new_password'
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'new_password.required' => 'Le nouveau mot de passe est obligatoire.',
            'new_password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'new_password.different' => 'Le nouveau mot de passe doit être différent de l\'actuel.',
        ];
    }
}
```

### Vue

```blade
@extends('layouts.app')

@section('title', 'Changer Mot de Passe')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Changer de Mot de Passe</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        
                        {{-- Mot de passe actuel --}}
                        <div class="mb-3">
                            <label for="current_password">Mot de Passe Actuel</label>
                            <input type="password" id="current_password" name="current_password" 
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nouveau mot de passe --}}
                        <div class="mb-3">
                            <label for="new_password">Nouveau Mot de Passe</label>
                            <input type="password" id="new_password" name="new_password" 
                                   class="form-control @error('new_password') is-invalid @enderror"
                                   required>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirmation --}}
                        <div class="mb-3">
                            <label for="new_password_confirmation">Confirmer</label>
                            <input type="password" id="new_password_confirmation" 
                                   name="new_password_confirmation" 
                                   class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Mettre à Jour</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 🔌 Intégration avec Autres Systèmes

### Utiliser dans un Service

```php
<?php
namespace App\Services;

use App\Services\PasswordPolicy;

class UserService
{
    public function validatePassword($password): bool
    {
        $result = PasswordPolicy::validate($password);
        return $result['valid'];
    }

    public function getPasswordErrors($password): array
    {
        $result = PasswordPolicy::validate($password);
        return $result['errors'];
    }
}
```

### Utiliser dans une API

```php
<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PasswordPolicy;

class PasswordValidationController extends Controller
{
    public function validate(Request $request)
    {
        $result = PasswordPolicy::validate($request->password);

        return response()->json([
            'valid' => $result['valid'],
            'strength' => $result['strength'],
            'percentage' => $result['percentage'],
            'errors' => $result['errors']
        ]);
    }
}
```

---

## 📝 Classe Utile: PasswordPolicy

### Méthodes Disponibles

```php
use App\Services\PasswordPolicy;

// Valider un mot de passe
$result = PasswordPolicy::validate('MyPass123!');
// Retourne: ['valid' => true, 'errors' => [], 'strength' => 'très-fort', ...]

// Obtenir les critères
$criteria = PasswordPolicy::getCriteria();
// Retourne: [['label' => '...', 'requirement' => '...', 'regex' => '...', 'icon' => '...'], ...]

// Obtenir les erreurs en HTML
$html = PasswordPolicy::getErrorsHtml($errors);
// Retourne: <div class="alert alert-danger"><ul><li>...</li></ul></div>

// Générer un exemple
$example = PasswordPolicy::generateExample();
// Retourne: 'AaBb1234!@'
```

---

## ✅ Checklist Finale

Avant de mettre en production:

```
□ FormRequest créé avec StrongPassword
□ Contrôleur utilise le FormRequest
□ Vue affiche les critères
□ Messages d'erreur en français
□ Test inscriptions avec mot de passe faible ❌
□ Test inscriptions avec mot de passe robuste ✅
□ Barre de force fonctionne (optionnel)
□ Hachage bcrypt utilisé pour le stockage
□ Aucun mot de passe stocké en clair
```

---

## 🐛 Troubleshooting

### L'erreur StrongPassword n'est pas reconnu
**Solution:** Vérifier l'import:
```php
use App\Rules\StrongPassword;
```

### Les critères ne s'affichent pas
**Solution:** Vérifier que PasswordPolicy est passé à la vue:
```php
$passwordCriteria = PasswordPolicy::getCriteria();
return view('...', compact('passwordCriteria'));
```

### La validation passe alors qu'elle ne devrait pas
**Solution:** Vérifier que StrongPassword est dans le tableau de règles:
```php
'password' => [
    'required',
    'string',
    'confirmed',
    new StrongPassword() // ← Ne pas oublier!
]
```

---

## 📞 Support

Questions ou problèmes?
- 📧 secretariatadecob@yahoo.fr
- ☎️ 0195647373
- 🕐 Lun - Ven : 08h - 12h30 et 15h - 17h30

Ou consulter: [PASSWORD_POLICY_GUIDE.md](documentation/PASSWORD_POLICY_GUIDE.md)

---

**Créé:** 3 Avril 2026  
**Statut:** 🟢 **GUIDE COMPLET**
