-- ============================================================
-- INSERTION DES UTILISATEURS — COMMUNE DE TCHAOUROU (id=3)
-- Généré le 18/08/2026 22:38
-- 1 commune_admin (BABALIYE) + 1 agent (DARI)
-- Mots de passe hachés en bcrypt (cost 12) — identique à l'inscription en production
-- ⚠️ telephone mis à NULL : colonne chiffrée, à remplir via l'application
-- Réexécutable sans risque (INSERT IGNORE : ignore si l'email existe déjà)
-- ============================================================

SET NAMES utf8mb4;

INSERT IGNORE INTO `users`
  (`name`, `prenom`, `email`, `telephone`, `role`, `commune_id`, `is_approved`, `is_admin`, `password`, `email_verified_at`, `approved_at`, `rejected_at`, `role_changed_at`, `created_at`, `updated_at`)
VALUES
  ('BABALIYE', 'Olivie', 'obabaliye@mairie.bj', NULL, 'commune_admin', 3, 1, 0, '$2y$12$Qj2biO4b2G/0PWsOv/oA3u0Rj6AKma2ma4HIAquXny4mLlV0tcwQy', NULL, NULL, NULL, NOW(), NOW(), NOW()),
  ('DARI S.', 'François', 'fdari@mairie.bj', NULL, 'agent', 3, 1, 0, '$2y$12$koty/cWhTc/k4x0KlVLCBOjeZWK6zNDp3ojSeef7E/E4VvVO8QCG.', NULL, NULL, NULL, NOW(), NOW(), NOW());
