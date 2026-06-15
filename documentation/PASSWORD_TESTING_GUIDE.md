# 🧪 Guide de Test - Politique de Mots de Passe

## Pour Administrateurs & QA - Tester le Système

---

## 🎯 Objectif du Test

Vérifier que la politique de mots de passe robuste fonctionne correctement sur les pages:
- `/register` (Inscription)
- `/reset-password` (Réinitialisation)
- `/login` (Connexion)

---

## 📋 Test 1: Inscription avec Mots de Passe Faibles

### Cas de Test 1.1: Mot de passe trop court (< 10 caractères)

**Actions:**
1. Aller sur `http://votresite.com/register`
2. Remplir les champs:
   - Nom: `Jean Dupont`
   - Email: `jean@example.com`
   - Type d'utilisateur: `Agent`
   - Mot de passe: `weak` (4 caractères)
   - Confirmation: `weak`

**Résultats attendus:**
- ❌ En temps réel: La barre de force montre **0%** (rouge)
- ❌ Critère "Longueur ≥ 10 caractères" reste **non coché** ❌
- ❌ Bouton de soumission reste **disabled** (grisé)
- ❌ À la soumission: Message d'erreur serveur

**Statut du test:** ✅ Pass si tous les critères sont présents

---

### Cas de Test 1.2: Mot de passe sans majuscule

**Actions:**
1. Remplir la page d'inscription
2. Mot de passe: `password1234!`
3. Confirmation: `password1234!`

**Résultats attendus:**
- 🟡 Barre de force montre **60%** (jaune)
- ✅ Critère "Longueur" est coché
- ✅ Critère "Chiffres" est coché
- ✅ Critère "Caractères spéciaux" est coché
- ❌ Critère "Majuscule" reste **non coché**
- ❌ Critère "Minuscule" reste **non coché**
- ❌ Bouton disabled
- Ou message d'erreur à la soumission

**Statut du test:** ✅ Pass si 3/5 critères sont validés

---

### Cas de Test 1.3: Mot de passe sans chiffre

**Actions:**
1. Mot de passe: `MyPassword!ABC`
2. Confirmation: `MyPassword!ABC`

**Résultats attendus:**
- 🟡 Barre de force montre **80%** (bleu clair)
- ✅ Longueur, Majuscule, Minuscule, Spéciaux cochés
- ❌ Chiffre non coché
- ❌ Bouton disabled ou erreur serveur

**Statut du test:** ✅ Pass si 4/5 critères validation

---

### Cas de Test 1.4: Confirmation ne correspond pas

**Actions:**
1. Mot de passe: `MyPass123!Secure`
2. Confirmation: `MyPass123!Wrong`

**Résultats attendus:**
- ✅ Barre de force: **100%** (vert) pour le mot de passe
- 🔴 Message d'erreur: "Les mots de passe ne correspondent pas"
- ❌ Bouton disabled
- ❌ À la soumission: Message d'erreur

**Statut du test:** ✅ Pass si la vérification fonctionne

---

## ✅ Test 2: Inscription avec Mot de Passe Robuste

### Cas de Test 2.1: Mot de passe parfait (5/5 critères)

**Actions:**
1. Aller sur `http://votresite.com/register`
2. Remplir les champs:
   - Nom: `Alice Martin`
   - Email: `alice@example.com`
   - Type: `Public User`
   - Mot de passe: `MyPass123!Secure`
   - Confirmation: `MyPass123!Secure`
   - Cases à cocher: ✅ Accepter les conditions

**Résultats attendus:**
- 🟢 Barre de force: **100%** (vert foncé)
- ✅ Tous les critères sont cochés:
  - ✅ Longueur (16 caractères ≥ 10)
  - ✅ Majuscule (M, P, S)
  - ✅ Minuscule (yass...)
  - ✅ Chiffre (123)
  - ✅ Caractères spéciaux (!)
- ✅ Bouton "S'inscrire" est **activé**
- ✅ À la soumission: Compte créé avec succès

**Vérifications supplémentaires:**
1. Vérifier que le mot de passe est hashé en base de données:
   ```sql
   SELECT password FROM users WHERE email = 'alice@example.com';
   -- Doit montrer un hash bcrypt (commence par $2y$)
   ```
2. Essayer de se connecter avec `alice@example.com` / `MyPass123!Secure`
3. Vérifier que c'est un mot de passe chaîné différent chaque fois

**Statut du test:** ✅ Pass si tous les critères ci-dessus sont remplis

---

### Cas de Test 2.2: Variation de la longueur

**Actions 1 - Exactement 10 caractères:**
- Mot de passe: `Aaa000!!!B`

**Résultats attendus:**
- 🟢 Barre: 100% (vert)
- ✅ Tous critères acceptés

**Actions 2 - 9 caractères:**
- Mot de passe: `Aaa000!!!`

**Résultats attendus:**
- 🟡 Barre: 80% (bleu)
- ❌ Critère longueur non coché

**Statut du test:** ✅ Pass si la limite de 10 est exacte

---

## 🔄 Test 3: Réinitialisation de Mot de Passe

### Cas de Test 3.1: Demander une réinitialisation

**Actions:**
1. Aller sur `http://votresite.com/forgot-password`
2. Entrer un email valide: `jean@example.com`
3. Cliquer "Envoyer le lien de réinitialisation"

**Résultats attendus:**
- ✅ Message: "Un email de réinitialisation vous a été envoyé"
- ✅ Email reçu avec lien de réinitialisation
- ✅ Lien commence par: `http://votresite.com/reset-password?token=...`

**Vérification en base:**
```sql
SELECT * FROM password_resets WHERE email = 'jean@example.com';
-- Doit montrer token et email
```

**Statut du test:** ✅ Pass si email reçu avec bon lien

---

### Cas de Test 3.2: Réinitialiser avec mot de passe faible

**Actions:**
1. Cliquer le lien du email
2. Page `/reset-password?token=...` s'affiche
3. Email pré-rempli (désactivé):
   - Mot de passe: `weak1234`
   - Confirmation: `weak1234`

**Résultats attendus:**
- ❌ Barre: 20% (rouge)
- ❌ Seul "Chiffres" est coché
- ❌ Bouton desactivé
- 🔴 À la soumission: Erreur serveur

**Statut du test:** ✅ Pass si validation fonctionne comme inscription

---

### Cas de Test 3.3: Réinitialiser avec mot de passe robuste

**Actions:**
1. Token valide dans l'URL
2. Mot de passe: `NewPass456!Updated`
3. Confirmation: `NewPass456!Updated`

**Résultats attendus:**
- 🟢 Barre: 100% (vert)
- ✅ Tous critères cochés
- ✅ Bouton activé
- ✅ À la soumission: "Mot de passe changé avec succès"
- ✅ Redirection vers login
- ✅ Connexion possible avec `jean@example.com` / `NewPass456!Updated`

**Vérification:**
- L'ancien mot de passe `weak` ne fonctionne plus
- Le nouveau mot de passe fonctionne

**Statut du test:** ✅ Pass si login fonctionne avec nouveau mot de passe

---

### Cas de Test 3.4: Token expiré (> 60 minutes)

**Actions:**
1. Attendre 61 minutes après la demande de réinitialisation
2. Essayer d'accéder au lien du email
3. Ou modifier l'URL pour utiliser un ancien token

**Résultats attendus:**
- 🔴 Message: "Ce lien de réinitialisation a expiré"
- ❌ Bouton de soumission disabled
- Utilisateur redirigé vers `/forgot-password`

**Statut du test:** ✅ Pass si token invalide ou expiré rejeté

---

## 🔐 Test 4: JavaScript Real-Time (Client-Side)

### Cas de Test 4.1: Barre de force progressive

**Actions:**
1. Sur `/register`
2. Cliquer dans le champ "Mot de passe"
3. Frapper caractères progressivement:

**Séquence de frappe:**
```
a       → 0% (rouge)
secret  → 20% (rouge)
Sec1    → 40% (jaune)
Sec1!   → 60% (jaune)
Secret1 → 80% (bleu)
Secret1! → 100% (vert)
```

**Résultats attendus:**
- Barre se remplit progressivement
- Couleur change à chaque étape
- Critères se cochent/décochent en temps réel
- Aucun rechargement de page

**Statut du test:** ✅ Pass si progression fluide sans page refresh

---

### Cas de Test 4.2: Visibilité du mot de passe

**Actions:**
1. Sur `/register`
2. Entrer: `MySecret123!`
3. Voir des `•••••••••••`
4. Cliquer l'icône 👁️ "Afficher le mot de passe"

**Résultats attendus:**
- ✅ Le mot de passe devient lisible: `MySecret123!`
- ✅ L'icône bascule à 👁️‍🗨️ "Masquer"
- ✅ Re-cliquer cache le mot de passe à nouveau

**Statut du test:** ✅ Pass si toggle fonctionne

---

### Cas de Test 4.3: Confirmation temps réel

**Actions:**
1. Entrer mot de passe: `MyPass123!Secure`
2. Entrer confirmation: `MyPass123!Secu` (mauvais)
3. Voir message d'erreur

**Résultats attendus:**
- 🔴 Message: "Les mots de passe ne correspondent pas"
- ❌ Bouton disabled
- Corriger confirmation à `MyPass123!Secure`
- ✅ Message disparaît
- ✅ Bouton réactivé

**Statut du test:** ✅ Pass si match validation instantanée

---

## 🔐 Test 5: Sécurité Serveur

### Cas de Test 5.1: Contourner JavaScript

**Actions (avec Dev Tools):**
1. Ouvrir `/register`
2. Appuyer F12 → Console
3. Modifier le formulaire en JS:
   ```javascript
   document.querySelector('[name="password"]').value = 'weak';
   document.querySelector('[name="password_confirmation"]').value = 'weak';
   document.querySelector('form').submit();
   ```

**Résultats attendus:**
- 🔴 Validation serveur rejette le mot de passe
- 🔴 Message d'erreur: "Le mot de passe n'est pas assez robuste"
- ❌ Formulaire ne se soumet pas
- ✅ Compte n'est pas créé

**Vérification:**
```bash
# Vérifier la base de données
SELECT COUNT(*) FROM users WHERE email = 'hacker@test.com';
# Doit retourner 0
```

**Statut du test:** ✅ Pass si serveur valide même si JS contourné

---

### Cas de Test 5.2: API Directe (Contournement complet)

**Actions (avec curl):**
```bash
curl -X POST http://votresite.com/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Hacker",
    "email": "hacker2@test.com",
    "password": "weak",
    "password_confirmation": "weak",
    "user_type": "agent"
  }'
```

**Résultats attendus:**
- 🔴 Réponse HTTP 422 (Unprocessable Entity)
- 🔴 Erreur JSON: `{"errors": {"password": ["Le mot de passe n'est pas assez robuste..."]}}`
- ❌ Aucun compte créé

**Vérification:**
```bash
curl http://votresite.com/reset-password -X POST \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"weak","password_confirmation":"weak","token":"any"}'
# Doit également rejeter
```

**Statut du test:** ✅ Pass si API aussi validante que le formulaire

---

### Cas de Test 5.3: Pas de MD5 ou SHA1 dans les logs

**Actions:**
1. Inscrire un utilisateur avec `MyPass123!Secure`
2. Vérifier les fichiers journaux:
   ```bash
   grep -r "MyPass123!Secure" storage/logs/
   # Doit retourner RIEN
   ```

**Résultats attendus:**
- ✅ Aucune occurrence du mot de passe
- ✅ Le hash bcrypt peut apparaître (c'est normal)
- ✅ Logs montrent: `User registered: jean@example.com` (sans mot de passe)

**Vérification dans DB:**
```sql
SELECT password FROM users WHERE email = 'jean@example.com';
-- Doit afficher: $2y$10$... (hash bcrypt)
-- NON: MyPass123!Secure, MD5, SHA1, etc.
```

**Statut du test:** ✅ Pass si aucun mot de passe stocké en clair

---

## 📊 Test 6: Cas Limites

### Cas de Test 6.1: Caractères spéciaux variés

**Mots de passe à tester:**

| Mot de passe | Spéciaux | Attendu |
|------------|----------|---------|
| MyPass123! | ! | ✅ Pass |
| MyPass123@ | @ | ✅ Pass |
| MyPass123# | # | ✅ Pass |
| MyPass123$ | $ | ✅ Pass |
| MyPass123% | % | ✅ Pass |
| MyPass123& | & | ✅ Pass |
| MyPass123* | * | ✅ Pass |
| MyPass123_ | _ | ✅ Pass |
| MyPass123- | - | ✅ Pass |

**Résultats:** Tous acceptés

**Statut du test:** ✅ Pass si tous caractères spéciaux fonctionnent

---

### Cas de Test 6.2: Accents et caractères non-ASCII

**Mot de passe:** `MyPässWörd123!`

**Résultats attendus:**
- ✅ Accepté ou ❌ Rejeté (dépend de la spécification)
- Si rejeté: Message d'erreur clair
- Cohérent avec la validation

**Statut du test:** ✅ Pass si comportement documenté

---

### Cas de Test 6.3: Espaces

**Mot de passe:** `My Pass123! Test`

**Résultats attendus:**
- ✅ Accepté (compte comme caractère)
- Ou ❌ Rejeté si espaces interdits
- Comportement cohérent avec spécifications

**Statut du test:** ✅ Pass si clair dans la documentation

---

## 🎯 Test 7: Performance

### Cas de Test 7.1: Calcul en temps réel

**Actions:**
1. Entrer et modifier rapidement le mot de passe
2. Vérifier que la barre est fluide (pas de lag)
3. JavaScript doit réagir en < 100ms

**Résultats attendus:**
- ✅ Barre se remplit sans saccade
- ✅ Critères mettent à jour instantanément
- ✅ Pas de CPU excessive
- ✅ Pas de requête serveur à chaque caractère

**Statut du test:** ✅ Pass si réactif et smooth

---

### Cas de Test 7.2: Soumission rapide

**Actions:**
1. Remplir le formulaire correctement
2. Cliquer "S'inscrire" 3 fois rapidement

**Résultats attendus:**
- ✅ Une seule soumission (bouton désactivé après le premier clic)
- ✅ Un seul compte créé
- ❌ Pas de création en doublon

**Vérification:**
```sql
SELECT COUNT(*) FROM users WHERE email = 'duplicate@test.com';
-- Doit retourner 1 seulement
```

**Statut du test:** ✅ Pass si protection contre les doublons

---

## 📱 Test 8: Responsif (Mobile)

### Cas de Test 8.1: iPhone/Phone

**Actions:**
1. Ouvrir `/register` sur un téléphone ou DevTools mobile
2. Remplir le formulaire sur petit écran
3. Voir barre de force et critères

**Résultats attendus:**
- ✅ Formulaire lisible sur mobile
- ✅ Barre de force visible
- ✅ Critères affichés sans défilement horizontal
- ✅ Bouton cliquable sur touch
- ✅ Clavier virtuel ne cache pas le formulaire

**Statut du test:** ✅ Pass si usable sur mobile

---

### Cas de Test 8.2: Tablette

**Actions:**
1. Ouvrir sur tablette (iPad, etc.)
2. Paysage et portrait

**Résultats attendus:**
- ✅ Formulaire se redimensionne correctement
- ✅ Aucune cassure de layout
- ✅ Texte lisible

**Statut du test:** ✅ Pass si responsive design OK

---

## 🔍 Test 9: Accessibilité

### Cas de Test 9.1: Keyboard Navigation

**Actions:**
1. Appuyer TAB pour naviguer
2. Remplir le formulaire sans souris

**Résultats attendus:**
- ✅ Focus visible sur chaque champ
- ✅ Tab order logique: Nom → Email → Type → Mot de passe → Confirmation → Termes → Bouton
- ✅ Tous les champs accessibles au clavier
- ✅ Bouton cliquable avec Entrée

**Statut du test:** ✅ Pass si accès clavier complet

---

### Cas de Test 9.2: Screen Reader

**Actions:**
1. Utiliser NVDA (gratuit) ou JAWS sur Windows
2. Naviguer le formulaire

**Résultats attendus:**
- ✅ Les labels annoncés avec les champs
- ✅ Les critères (listes) nommées
- ✅ Les messages d'erreur lus
- ✅ La barre de force décrite

**Statut du test:** ✅ Pass si annonces vocales claires

---

## ✅ Checklist Résumée

```
Inscription:
□ Mot de passe faible (< 10 chars) rejeté
□ Mot de passe sans majuscule rejeté
□ Mot de passe sans minuscule rejeté
□ Mot de passe sans chiffre rejeté
□ Mot de passe sans spécial rejeté
□ Confirmation ne correspond pas = rejet
□ Mot de passe robuste (5/5) accepté
□ Barre de force passe de 0% → 100%
□ Couleurs changent (rouge → jaune → bleu → vert)
□ Critères coché/décoché en temps réel
□ Bouton disabled jusqu'à 5/5 critères
□ Compte créé avec succès
□ Mot de passe hashé en base (bcrypt)

Réinitialisation:
□ Lien email valide (60 min expiration)
□ Même validation qu'inscription
□ Ancien mot de passe ne fonctionne plus
□ Nouveau mot de passe fonctionne

Sécurité:
□ JavaScript contourné = validation serveur rejette
□ API directe validée côté serveur
□ Pas de mot de passe en logs
□ Password est bcrypté en DB
□ Pas d'injection SQL possible

Performance:
□ Barre updating en < 100ms
□ Pas de requête serveur en temps réel
□ Protection contre double-soumission

Mobile:
□ Formulaire lisible sur mobile
□ Responsive design OK
□ Clavier virtuel gère bien l'espace

Accessibilité:
□ Navigation au clavier (TAB)
□ Focus visible
□ Screen readers supportés
```

---

## 📝 Rapport de Test

Après avoir complété tous les tests, créer un rapport:

```
Date: [Date du test]
Testeur: [Votre nom]
Navigateur: Chrome 120 / Firefox 121 / Safari 17
Système: Windows 11 / macOS / iOS

Résultats:
- Test 1 (Mots de passe faibles):     ✅ Pass / ❌ Fail
- Test 2 (Mots de passe robustes):    ✅ Pass / ❌ Fail
- Test 3 (Réinitialisation):          ✅ Pass / ❌ Fail
- Test 4 (JavaScript real-time):      ✅ Pass / ❌ Fail
- Test 5 (Sécurité serveur):          ✅ Pass / ❌ Fail
- Test 6 (Cas limites):               ✅ Pass / ❌ Fail
- Test 7 (Performance):               ✅ Pass / ❌ Fail
- Test 8 (Mobile):                    ✅ Pass / ❌ Fail
- Test 9 (Accessibilité):             ✅ Pass / ❌ Fail

Score global: [X/9] ✅ (tout pass) ou ⚠️ (problèmes détectés)

Problèmes détectés:
- [Lister les problèmes trouvés]

Recommandations:
- [Lister les améliorations suggérées]

Signature: [Nom et date]
```

---

## 🎓 Conclusion

Quand TOUS les tests passent ✅, le système est prêt pour la production! 

Pour les questions: **secretariatadecob@yahoo.fr**

---

**Créé:** 3 Avril 2026  
**Statut:** 🟢 **GUIDE DE TEST COMPLET**
