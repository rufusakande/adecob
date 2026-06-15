# 🔧 Guide de Dépannage - Politique de Mots de Passe

## Pour Administrateurs & Support Technique

---

## 🆘 Problèmes Courants & Solutions

### ❌ Problème 1: "Le mot de passe n'est pas assez robuste"

**Symptôme:** Utilisateur reçoit ce message lors de l'inscription ou du reset.

**Causes possibles:**
1. Mot de passe < 10 caractères
2. Pas de majuscule
3. Pas de minuscule
4. Pas de chiffre
5. Pas de caractère spécial (!@#$%^&*, etc.)

**Solution pour l'utilisateur:**
```
Instructions à donner à l'utilisateur:

1. Vérifiez votre mot de passe a AU MOINS:
   ✓ 10 caractères (ex: 12345678901)
   ✓ Une MAJUSCULE (A-Z)
   ✓ Une minuscule (a-z)
   ✓ Un chiffre (0-9)
   ✓ Un caractère spécial (!@#$%^&*etc)

2. Exemple valide: MyPassword123!

3. Exemple INVALIDE: password123 (manque majuscule et spécial)
```

**Pour tester rapidement:**
```bash
# Tester avec la API
curl -X POST http://votresite.com/register \
  -d "password=MyPass123!" \
  | jq .
# Doit accepter

curl -X POST http://votresite.com/register \
  -d "password=weak" \
  | jq .
# Doit rejeter
```

---

### ❌ Problème 2: "Les mots de passe ne correspondent pas"

**Symptôme:** Utilisateur remplit les 2 champs de mots de passe mais obtient cette erreur.

**Causes:**
1. Typo dans la confirmation
2. Les deux champs n'ont pas exactement le même texte
3. Espaces accidentels

**Solution:**
1. Demander à l'utilisateur de voir/masquer les mots de passe (icon 👁️)
2. Vérifier caractère par caractère
3. Particulièrement vérifier:
   - Pas d'espaces avant/après
   - MAJUSCULES bien saisies
   - Chiffres 0/O, 1/l, 5/S bien tapés

**Tester:**
```javascript
// Dans la console du navigateur
const pwd = document.querySelector('[name="password"]').value;
const conf = document.querySelector('[name="password_confirmation"]').value;
console.log("Mot de passe:", pwd);
console.log("Confirmation:", conf);
console.log("Identiques?", pwd === conf);
```

---

### ❌ Problème 3: Barre de force ne s'affiche pas

**Symptôme:** Pas de barre colorée (rouge/jaune/bleu/vert) en bas du champ.

**Causes:**
1. JavaScript désactivé dans le navigateur
2. Erreur dans le code JavaScript
3. CSS pas chargé

**Solution:**

**Vérifier JavaScript:**
1. F12 → Console tab
2. Chercher erreurs en rouge
3. Vérifier que la page charge sans erreurs

**Vérifiable:**
```javascript
// Dans la console
document.querySelector('.progress') !== null
// Doit retourner "true"

document.getElementById('new_password') !== null
// Doit retourner "true"
```

**Vérifier CSS:**
1. F12 → Elements/Inspector
2. Chercher la classe `progress`
3. Verifier les styles pour width, background-color, etc.

**Réactiver JavaScript:**
1. Chrome: Settings → Privacy → Site Settings → JavaScript
2. Firefox: about:config → javascript.enabled = true
3. Safari: Preferences → Security → Enable JavaScript

---

### ❌ Problème 4: "Ce lien de réinitialisation a expiré"

**Symptôme:** Utilisateur reçoit le lien de reset par email mais obtient cette erreur.

**Causes:**
1. Lien > 60 minutes
2. Token mal copié depuis l'email
3. Token changé/modifié
4. URL corrompue

**Solution:**

**Pour l'utilisateur:**
1. Demander une nouveaux lien: Aller sur `/forgot-password`
2. Entrer son email
3. Vérifier les spams (GMail, Yahoo, Outlook)
4. Utiliser le lien RAPIDEMENT (< 60 minutes)
5. NE PAS MODIFIER l'URL

**Pour tester en tant que support:**
```bash
# Vérifier la base de données
mysql -u user -p database << EOF
SELECT * FROM password_resets WHERE email = 'user@example.com';
EOF

# Vérifier le token (avant 60 min)
SELECT TIMESTAMPDIFF(MINUTE, created_at, NOW()) as minutes_old 
FROM password_resets 
WHERE email = 'user@example.com';

# Si > 60, le lien a expiré
```

---

### ❌ Problème 5: Utilisateur bloqué de son compte

**Symptôme:** L'utilisateur dit "Je ne peux plus me connecter avec mon mot de passe".

**Causes:**
1. Mauvais mot de passe
2. Compte non approuvé (pour les agents)
3. Compte supprimé
4. Erreur de saisi de l'email

**Solution:**

**Vérifier l'email:**
```bash
# Vérifier que l'email existe
mysql -u user -p database << EOF
SELECT id, email, approved FROM users WHERE email = 'user@example.com';
EOF

# Résultats possibles:
# 1. Aucun résultat: Utiliser /register ou /forgot-password
# 2. approved = 0: Compte pas encore approuvé (voir section plus bas)
# 3. approved = 1: Compte OK, prob de mot de passe
```

**Vérifier le mot de passe:**
Les mots de passe Bcrypt ne peuvent PAS être "déchiffrés". Pour tester:
1. Utiliser `/reset-password` - forcer le reset
2. Ou réinitialiser en base de données:

```bash
# Ne JAMAIS mettre le mot de passe en clair!
mysql -u user -p database << EOF
-- Utiliser Laravel Tinker pour hasher
-- php artisan tinker
-- > Hash::make('TempPassword123!')
UPDATE users 
SET password = '$2y$10$...[HASH GENERE]...'
WHERE email = 'user@example.com';
EOF

# OU réinitialiser via interface
# Admin → Users → Actions → Reset Password
```

**Tester la connexion:**
```bash
# Après reset, tester la connexion
curl -X POST http://votresite.com/login \
  -d "email=user@example.com&password=TempPassword123!" \
  -c cookies.txt

# Si successful_login dans cookies.txt, ça fonctionne
```

---

### ❌ Problème 6: Compte "Non Approuvé"

**Symptôme:** Utilisateur voit "Votre compte est en attente d'approbation".

**Causes:**
1. Les agents nécessitent l'approbation d'un admin
2. L'admin a pas encore approuvé le compte
3. L'admin a refusé le compte

**Solution pour Admin:**

```bash
# 1. Voir les comptes en attente
mysql -u user -p database << EOF
SELECT id, name, email, user_type, approved, created_at 
FROM users 
WHERE approved = 0
ORDER BY created_at DESC;
EOF

# 2. Approuver un compte
UPDATE users SET approved = 1 WHERE email = 'agent@example.com';

# 3. Refuser un compte
UPDATE users SET approved = -1 WHERE email = 'spammer@example.com';

# 4. Voir l'historique
SELECT * FROM notifications 
WHERE user_id = [ID] 
ORDER BY created_at DESC;
```

---

### ❌ Problème 7: Email de réinitialisation ne reçu pas

**Symptôme:** Utilisateur remplit `/forgot-password` mais reçoit pas l'email.

**Causes possibles:**
1. Email en spams
2. Email invalide
3. Service maileur pas configuré
4. Erreur SMTP

**Solution:**

**Vérifications rapides:**
1. Vérifier spams/courrier indésirable
2. Vérifier que c'est pas un email faux (typo)
3. Pendant quelques secondes vérifier aussi les emails valides

**Vérifier configuration mail:**
```bash
# Fichier .env
cat .env | grep MAIL

# Résultats attendus:
MAIL_DRIVER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=user@example.com
MAIL_PASSWORD=****
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@adecob.com
MAIL_FROM_NAME="ADECOB"
```

**Tester SMTP:**
```bash
# Laravel Tinker
php artisan tinker

# Envoyer test email
Mail::raw('Test', function($msg) {
    $msg->to('user@example.com')->subject('Test');
});

# Vérifier les logs
tail -f storage/logs/*.log | grep -i mail
```

**Solution finale:**
- Contacter fournisseur email (si SMTP)
- Vérifier les logs en `/storage/logs/`
- Chercher les erreurs de type "SMTP", "Mail", "Connection"

---

### ❌ Problème 8: Hash de mot de passe en clair visible

**Symptôme:** On voit un mot de passe en clair dans les logs ou la base.

**Danger:** ⚠️ CRITIQUE - SÉCURITÉ COMPROMISE

**Actions IMMÉDIAT:**
1. ⛔ ARRÊTER le site (maintenance mode)
2. 🔍 Identifier le mot de passe exposé
3. 🔄 Forcer le reset du mot de passe pour cet utilisateur
4. 🧹 Supprimer les logs contenant le mot de passe:

```bash
# Trouver les fichiers
grep -r "MyPassword123!" storage/logs/

# Supprimer
find storage/logs -name "*.log" -delete

# Recréer les dossiers logs
mkdir -p storage/logs
chmod 777 storage/logs

# Vérifier la base
grep -r "MyPassword123!" .
# NE doit rien trouver sauf les vérifications ci-dessous
```

**Vérifier la base:**
```bash
mysql -u user -p database << EOF
-- Chercher les mots de passe en clair (très mauvais!)
SELECT * FROM users WHERE password NOT LIKE '$2y$%';
-- Doit retourner 0 résultats

-- Tous les mots de passe doivent commencer par $2y$10$
SELECT DISTINCT SUBSTRING(password, 1, 8) FROM users;
-- Doit montrer: $2y$10$
EOF
```

---

### ⚠️ Problème 9: Trop de tentatives de reset

**Symptôme:** Utilisateur essaye trop de fois `/forgot-password` et système bloque.

**Causes:**
1. Utilisateur oublietis son email
2. Email incorrect dans le système
3. Quelqu'un essaye de spammer les resets

**Solution:**

**Vérifier les resets récents:**
```bash
mysql -u user -p database << EOF
SELECT email, COUNT(*) as count, MAX(created_at) 
FROM password_resets 
GROUP BY email 
HAVING COUNT(*) > 5
ORDER BY created_at DESC;
EOF
```

**Nettoyer les anciennes demandes:**
```bash
# Supprimer les resets > 24 heures
DELETE FROM password_resets 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR);

# Vérifier
SELECT COUNT(*) FROM password_resets;
```

---

### ⚠️ Problème 10: Performance lente lors de la reset

**Symptôme:** Page `/reset-password?token=...` charge lentement (> 2 secondes).

**Causes:**
1. Hashage bcrypt prend du temps (le 2e plus dépendant du `COST`)
2. Requête base de données lente
3. Serveur surchargé

**Solution:**

**Vérifier le coût bcrypt:**
```php
// Dans config/hashing.php
'bcrypt' => [
    'rounds' => 10, // Coût par défaut
],

// Plus il y a de rounds, plus c'est sûr mais lent
// Tester des valeurs:
// 8 = très rapide mais moins sûr
// 10 = recommandé (prend ~50ms)
// 12 = lent (prend ~200ms)
```

**Tester la performance:**
```bash
# Vérifier le temps de hachage
php artisan tinker

# Mesurer le temps
$start = microtime(true);
$hash = Hash::make('MyPassword123!');
echo (microtime(true) - $start) . " secondes";

# Doit être < 0.1 secondes normalement
```

**Solutions:**
1. Réduire les `rounds` de 12 à 10 ou 8
2. Ajouter plus de RAM serveur
3. Vérifier les requêtes DB avec `php artisan tinker`:
   ```php
   DB::enableQueryLog();
   // ... faire l'action ...
   dd(DB::getQueryLog());
   ```

---

## 🔑 Réinitialiser un Mot de Passe (Admin)

### Méthode 1: Interface Web (Recommandée)

```
Si tu as un panneau admin:
1. Aller à: /admin/users
2. Rechercher l'utilisateur
3. Cliquer "Reset Password"
4. Envoyer un lien de reset
```

### Méthode 2: Ligne de Commande (CLI)

```bash
# Accéder au serveur
ssh user@votresite.com
cd /var/www/adecob

# Laravel Tinker
php artisan tinker

# Réinitialiser le mot de passe
$user = User::where('email', 'user@example.com')->first();
$user->update(['password' => Hash::make('TempPassword123!')]);

# Vérifier
auth()->attempt(['email' => 'user@example.com', 'password' => 'TempPassword123!'])
// Doit retourner true
```

### Méthode 3: Base de Données Direct (Danger!)

```bash
mysql -u user -p adecob_db

# ATTENTION: Ne JAMAIS mettre le mot de passe en clair!
# JAMAIS faire: UPDATE users SET password = 'MyPassword123!'
# UTILISER hash via PHP/Laravel

# Utiliser Laravel pour générer le hash:
# php artisan tinker
# Hash::make('TempPassword123!')

# Puis mettre à jour:
UPDATE users 
SET password = '$2y$10$...[HASH GENERE]...' 
WHERE email = 'user@example.com';

# Vérifier
SELECT email, password FROM users WHERE email = 'user@example.com';
```

---

## 📊 Statistiques & Monitoring

### Voir les mots de passe les plus forts

```bash
# Ceci ne peut PAS être fait car les mots de passe sont hashés (bon signe!)
# Mais on peut voir les utilisateurs actifs:

mysql -u user -p adecob_db << EOF
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as approved_users FROM users WHERE approved = 1;
SELECT COUNT(*) as pending_approval FROM users WHERE approved = 0;
EOF
```

### Voir les tentatives de connexion échouées

```bash
# Si tu as logging configuré
tail -f storage/logs/laravel.log | grep -i "login\|password\|failed"

# Pour les tentatives de reset
mysql -u user -p adecob_db << EOF
SELECT * FROM password_resets 
ORDER BY created_at DESC 
LIMIT 20;
EOF
```

### Audit des comptes créés

```bash
mysql -u user -p adecob_db << EOF
SELECT 
  DATE(created_at) as date,
  COUNT(*) as new_accounts,
  SUM(CASE WHEN approved = 1 THEN 1 ELSE 0 END) as approved,
  SUM(CASE WHEN approved = 0 THEN 1 ELSE 0 END) as pending,
  SUM(CASE WHEN user_type = 'agent' THEN 1 ELSE 0 END) as agents,
  SUM(CASE WHEN user_type = 'public_user' THEN 1 ELSE 0 END) as public_users
FROM users
GROUP BY DATE(created_at)
ORDER BY date DESC;
EOF
```

---

## 🛡️ Sécurité: Checklist Admin

```
□ Aucun mot de passe en clair dans les logs
□ Tous les mots de passe commencent par $2y$ (bcrypt)
□ Tokens de reset expireront après 60 minutes
□ Tokens de reset supprimés après utilisation
□ Pas d'accès direct à voir les mots de passe
□ Tous les logins sont enregistrés
□ Les tentatives échouées peuvent être détectées
□ TLS/HTTPS activé pour les formulaires
□ CSRF tokens présents sur toutes les formes
```

---

## 🆘 Escalade

Si tu ne peux pas résoudre le problème:

**Contact Support ADECOB:**
- 📧 secretariatadecob@yahoo.fr
- ☎️ 0195647373
- 🕐 Lun - Ven : 08h - 12h30 et 15h - 17h30

**Informations à fournir:**
1. Quel est le problème exactement? (dépannage #X?)
2. Quand ça a commencé?
3. Combien d'utilisateurs affectés?
4. Messages d'erreur exacts (copier/coller)
5. Navigateur & système d'exploitation
6. Étapes pour reproduire

---

## 📝 Logs Utiles

### Voir les erreurs d'authentification

```bash
# Terminal
tail -100 storage/logs/laravel.log

# Chercher spécifiquement
grep -i "password\|auth\|login" storage/logs/laravel.log | tail -20
```

### Voir les requêtes échouées

```bash
# Si Apache
tail -f /var/log/apache2/error.log | grep POST

# Si Nginx
tail -f /var/log/nginx/error.log | grep POST
```

### Dump complet des infos serveur

```bash
# Laravel diagnostic
php artisan tinker
App::info()

# Ou depuis la ligne de commande
php artisan about
```

---

## ✅ Checklist Dépannage Rapide

Avant d'escalader:

```
□ Utilisateur a tapé bon email? (vérifier spams)
□ Utilisateur a attendu < 60 min après le reset?
□ Mot de passe a au moins 10 caractères?
□ Mot de passe a majuscule, minuscule, chiffre, spécial?
□ Compte est approuvé (approved = 1)?
□ Email est valide en base de données?
□ Vérifier les logs pour erreurs?
□ Redémarrer le navigateur?
□ Vider le cache du navigateur?
□ Utiliser incognito/privé?
```

---

**Créé:** 3 Avril 2026  
**Statut:** 🟢 **GUIDE COMPLET**  
**Dernière mise à jour:** 3 Avril 2026
