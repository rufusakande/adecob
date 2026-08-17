# 🤝 CONVENTION DE SOUS-TRAITANCE (DONNÉES PERSONNELLES)
## Plateforme ARMANI — Hébergement Open.bj (offre DIAMOND)

*Document établi dans le cadre du RGPD et de la loi béninoise sur le numérique — à faire valider par l'association et Open.bj.*

---

## 1. Parties

- **Responsable de traitement (client) :** l'association éditrice de la plateforme ARMANI — [À COMPLÉTER — dénomination, adresse, représentant légal]
- **Sous-traitant :** Open.bj (Open Business) — hébergement cloud, offre DIAMOND — [À COMPLÉTER — adresse, contact]

## 2. Objet

La présente convention encadre le traitement de données à caractère personnel réalisé par le sous-traitant **pour le compte** du responsable de traitement, dans le cadre de l'hébergement de la plateforme ARMANI (application web, base de données MySQL, fichiers de photos, e-mails).

## 3. Description du traitement (Article 28 RGPD — annexe)

| Élément | Description |
|---|---|
| **Nature du traitement** | Hébergement (stockage, sauvegarde, accès technique) des données de la plateforme ARMANI |
| **Catégories de données** | Données d'identification des comptes (nom, prénom, e-mail, commune), données de recensement des infrastructures (localisation GPS, photos, état), journaux de connexion |
| **Catégories de personnes** | Agents, administrateurs de commune, super administrateurs, utilisateurs publics |
| **Durée du traitement** | Durée du contrat d'hébergement |
| **Serveurs / localisation** | [À COMPLÉTER — localisation des datacenters d'Open.bj] |

## 4. Obligations du sous-traitant (Article 28 RGPD)

Le sous-traitant s'engage à :

1. **Ne traiter les données que sur instruction documentée** du responsable de traitement, pour les seules finalités de l'hébergement.
2. **Garantir la confidentialité** des personnes autorisées à traiter les données (obligation de confidentialité, habilitations limitées).
3. **Mettre en œuvre les mesures de sécurité appropriées** : chiffrement des transmissions (SSL/TLS — Let's Encrypt), cloisonnement des comptes, accès authentifiés (SSH/FTP/MySQL), protection physique des serveurs.
4. **Respecter les conditions de sous-traitance ultérieure** (voir point 5).
5. **Assister le responsable de traitement** dans le respect de ses obligations (réponses aux demandes des personnes concernées, sécurité, notification de violation).
6. **Supprimer ou restituer les données** à l'issue des prestations, au choix du responsable de traitement.
7. **Mettre à disposition les informations nécessaires** pour démontrer le respect des obligations RGPD (audits / contrôles, sous réserve des contraintes techniques).
8. **Notifier sans délai** au responsable de traitement toute **violation de données** dont il aurait connaissance, au plus tard dans les **72 heures**.

## 5. Sous-traitance ultérieure

Le sous-traitant ne peut recourir à un autre sous-traitant (sous-traitant ultérieur) sans **autorisation écrite préalable** du responsable de traitement. Tout sous-traitant ultérieur est soumis aux mêmes obligations de protection des données.

## 6. Sécurité de l'offre DIAMOND (moyens mis en œuvre)

- **Certificat SSL Let's Encrypt** : chiffrement des échanges entre les navigateurs et le serveur (HTTPS).
- **Accès sécurisés** : comptes FTP et bases MySQL dédiés, accès panel/cPanel protégés par mot de passe fort + MFA recommandée.
- **Sauvegardes (Backup support)** : incluses dans l'offre — voir le plan détaillé dans `PRA_PCA_SAUVEGARDE.md`.
- **Mise à jour des composants** : PHP, MySQL et applications maintenus à jour par l'hébergeur / l'équipe technique.

## 7. Durée, résiliation et sortie

- **Durée :** durée du contrat d'hébergement souscrit auprès d'Open.bj.
- **Résiliation :** selon les conditions générales d'Open.bj, avec un **préavis** et la **récupération préalable** des données (export base MySQL + fichiers).
- **Fin de contrat :** le sous-traitant restitue ou supprime l'ensemble des données, selon l'instruction du responsable de traitement, et fournit une attestation de suppression si demandée.

## 8. Registre des traitements

La présente convention doit être mentionnée dans le **registre des traitements** de l'association, ainsi que dans le registre du sous-traitant (conformément à l'article 30 RGPD).

## 9. Signatures

Fait à [À COMPLÉTER — ville], le [À COMPLÉTER — date], en deux exemplaires originaux.

**Pour le responsable de traitement** (association)
- Nom, qualité : __________________________________
- Signature : __________________________________

**Pour le sous-traitant** (Open.bj)
- Nom, qualité : __________________________________
- Signature : __________________________________

---

*Modèle à compléter et faire valider juridiquement avant mise en service.*
