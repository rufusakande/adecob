# Chiffrement des données — At-rest & In-transit

Document opérationnel ADECOB. À jour avec PR9 → PR13.

---

## 1. Chiffrement en transit (HTTPS / TLS 1.3)

### 1.1 Niveau application (déjà fait dans le code)
- `URL::forceScheme('https')` en production (`AppServiceProvider`).
- Middleware global `ForceHttps` → redirige 301 toute requête HTTP vers HTTPS hors environnement local.
- En-tête **HSTS** envoyé sur HTTPS : `Strict-Transport-Security: max-age=31536000; includeSubDomains; preload` (`SecurityHeaders`).
- Cookies de session : `Secure`, `HttpOnly`, `SameSite=Strict`, contenu chiffré (`config/session.php`).
- CSP, X-Frame-Options, Referrer-Policy, Permissions-Policy actifs.

### 1.2 Niveau serveur web (à configurer côté hébergeur)
**Nginx — exemple TLS 1.3 :**
```nginx
server {
    listen 443 ssl http2;
    server_name adecob.bj;

    ssl_certificate     /etc/letsencrypt/live/adecob.bj/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/adecob.bj/privkey.pem;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers 'TLS_AES_256_GCM_SHA384:TLS_CHACHA20_POLY1305_SHA256:TLS_AES_128_GCM_SHA256:ECDHE-RSA-AES256-GCM-SHA384';
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 1d;
    ssl_session_tickets off;

    # OCSP stapling
    ssl_stapling on;
    ssl_stapling_verify on;

    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
}

server {
    listen 80;
    server_name adecob.bj;
    return 301 https://$host$request_uri;
}
```

**Apache — équivalent :**
```apache
SSLProtocol -all +TLSv1.2 +TLSv1.3
SSLCipherSuite TLSv1.3 TLS_AES_256_GCM_SHA384:TLS_CHACHA20_POLY1305_SHA256:TLS_AES_128_GCM_SHA256
SSLHonorCipherOrder on
SSLSessionTickets off
```

### 1.3 Certificats
- **Let's Encrypt** (gratuit) via `certbot` → renouvellement auto via cron `0 3 * * * certbot renew --quiet`.
- Vérification mensuelle via [ssllabs.com/ssltest](https://www.ssllabs.com/ssltest/) — cible note **A+**.

---

## 2. MySQL — TLS entre application et base

### 2.1 Côté serveur MySQL (`my.cnf`)
```ini
[mysqld]
require_secure_transport = ON
ssl_ca = /etc/mysql/ssl/ca.pem
ssl_cert = /etc/mysql/ssl/server-cert.pem
ssl_key = /etc/mysql/ssl/server-key.pem
tls_version = TLSv1.2,TLSv1.3
```

### 2.2 Côté Laravel — `.env` de production
```env
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/mysql-ca.pem
MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=true
```
Configuration déjà câblée dans `config/database.php`. La connexion échoue si TLS n'est pas négocié.

### 2.3 Vérification
```sql
SHOW STATUS LIKE 'Ssl_cipher';
-- Doit renvoyer une suite type TLS_AES_256_GCM_SHA384
```

---

## 3. Chiffrement at-rest

### 3.1 Champs sensibles applicatifs (PR11 — déjà actif)
Chiffrement automatique via cast Eloquent `'encrypted'` (AES-256-CBC, clé `APP_KEY`) :

| Modèle | Champ | Raison |
|---|---|---|
| `User` | `telephone` | Donnée personnelle (DCP — APDP Bénin) |
| `Commune` | `access_code_plain` | Code d'accès partagé entre agents |
| `Infrastructure` | `numero_telephone` | Contact terrain |

Mots de passe : hash bcrypt (cast `'hashed'`), jamais en clair, jamais déchiffrables.

### 3.2 Sessions
`config/session.php` : `'encrypt' => true` → données de session chiffrées sur disque/Redis.

### 3.3 Stockage MySQL
- Activer `innodb_redo_log_encrypt`, `innodb_undo_log_encrypt` et le chiffrement par tablespace côté hébergeur.
- Sauvegardes : `mysqldump … | gpg --encrypt -r ops@adecob.bj > backup.sql.gpg`.

### 3.4 Rotation de `APP_KEY`
Sans précaution, la rotation rend illisibles tous les champs `encrypted`. Procédure sûre (Laravel ≥ 10) :
1. Conserver l'ancienne clé : `APP_PREVIOUS_KEYS=base64:ancienneCle`
2. Générer la nouvelle : `php artisan key:generate`
3. Laravel déchiffre encore les anciennes valeurs avec `APP_PREVIOUS_KEYS`, puis ré-écrit avec la nouvelle clé au prochain `save()`.
4. Après ré-écriture complète, retirer `APP_PREVIOUS_KEYS`.

---

## 4. Sécurisation des échanges API entre modules

### 4.1 État actuel
- API Sanctum (`auth:sanctum`) sur `/api/*` — token Bearer obligatoire.
- CSRF actif sur toutes les routes web (`VerifyCsrfToken`).
- Rate-limit nommés (`login`, `register`, `password-reset`, `contact`) — voir `AppServiceProvider`.

### 4.2 Recommandations
- Tokens Sanctum à **portée limitée** (`$user->createToken('mobile-agent', ['infrastructures:read'])`).
- Expiration : `config/sanctum.php` → `'expiration' => 60 * 24` (24h pour mobile).
- Rotation des tokens à chaque mise à jour de mot de passe.
- En cas d'intégration externe (SIG communal, mairies) : exiger **mTLS** ou signature HMAC + IP whitelisting.

---

## 5. Checklist de validation (à exécuter avant mise en production)

- [ ] `curl -I https://adecob.bj` → `Strict-Transport-Security` présent, statut 200.
- [ ] `curl http://adecob.bj` → 301 vers `https://`.
- [ ] [ssllabs.com/ssltest](https://www.ssllabs.com/ssltest/) → note **A** ou **A+**, TLS 1.3 activé.
- [ ] `mysql --ssl-mode=REQUIRED -h <host> -u <user> -p` → connexion OK.
- [ ] `SHOW STATUS LIKE 'Ssl_cipher';` → suite TLS 1.3.
- [ ] `php artisan tinker` → `User::first()->telephone` (lisible) ; `User::first()->getRawOriginal('telephone')` (chiffré base64).
- [ ] Cookies de session : `Secure`, `HttpOnly`, `SameSite=Strict` (DevTools → Application → Cookies).
- [ ] APP_KEY de production stockée hors dépôt (vault, secrets manager).

---

*Document de référence PSSI — section Chiffrement. Voir aussi `documentation/PASSWORD_POLICY_GUIDE.md` et la PSSI publique (`/pssi`).*
