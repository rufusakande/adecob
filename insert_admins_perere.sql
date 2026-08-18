-- ============================================================
-- INSERTION DES ADMINISTRATEURS — PLATEFORME ARMANI
-- Généré le 18/08/2026 22:17
-- 1 super_admin (GBAGUIDI) + 8 commune_admin (commune Perere, id=7)
-- Mots de passe hachés en bcrypt (cost 12) — identique à l'inscription en production
-- ⚠️ telephone mis à NULL : colonne chiffrée, à remplir via l'application
-- Réexécutable sans risque (INSERT IGNORE : ignore si l'email existe déjà)
-- ============================================================

SET NAMES utf8mb4;

INSERT IGNORE INTO `users`
  (`name`, `prenom`, `email`, `telephone`, `role`, `commune_id`, `is_approved`, `is_admin`, `password`, `email_verified_at`, `approved_at`, `rejected_at`, `role_changed_at`, `created_at`, `updated_at`)
VALUES
  ('GBAGUIDI', 'Ahotondji Philémon', 'pagbaguidi@mairie.bj', NULL, 'super_admin', NULL, 1, 0, '$2y$12$pe/mHLWdOL1SZLjD3qqMNOQ26FS2H/fMV8Ic1Jm.xTUclOa4Roibq', NULL, NULL, NULL, NOW(), NOW(), NOW()),
  ('NAKOU', 'Tagnon Raudace', 'raudace.nakou@mairie.bj', NULL, 'commune_admin', 7, 1, 0, '$2y$12$I4/zNtgecvuYRov2tMeBx.fpPUceDvjukWGR/WVLH5R/7SF.ui67m', NULL, NULL, NULL, NOW(), NOW(), NOW()),
  ('MONTIN', 'Mahoukèdè Gladstone', 'gladstone.montin@mairie.bj', NULL, 'commune_admin', 7, 1, 0, '$2y$12$gnXyLxKz5psMe0blFlNIJOMVspASvjvUNE1F4SFKpwxZj0BYZknRm', NULL, NULL, NULL, NOW(), NOW(), NOW()),
  ('CHABI SOUBO', 'Bourandi', 'bourandi.chabisoubo@mairie.bj', NULL, 'commune_admin', 7, 1, 0, '$2y$12$tkQ201A0NfF3.gBu/rnD0OS9xNNvbmGJoD7A0q2TXa4fNnTguoeSu', NULL, NULL, NULL, NOW(), NOW(), NOW()),
  ('SABI GOURA', 'Daouda', 'daouda.sabigoura@mairie.bj', NULL, 'commune_admin', 7, 1, 0, '$2y$12$jNmedSjI4KHZgIMdXFFnLOz6Iz3Vl915yFdXQ1ca8gUKdJ7rpPKOy', NULL, NULL, NULL, NOW(), NOW(), NOW()),
  ('GNIMASSOU', 'Franck', 'franck.gnimassou@mairie.bj', NULL, 'commune_admin', 7, 1, 0, '$2y$12$H6s7f3PjMLJg8go920NCd.vJQM0AlREkTbY5TeqbztLXd7xlpGIwm', NULL, NULL, NULL, NOW(), NOW(), NOW()),
  ('CHABIMARO', 'Taïrou', 'tairou.chabimaro@mairie.bj', NULL, 'commune_admin', 7, 1, 0, '$2y$12$8T.n/jCjHxxINnSUy8m.P.dTpq31BOHsWkS0AZHSvaYZ2baN7SZVC', NULL, NULL, NULL, NOW(), NOW(), NOW()),
  ('BOURANDI', 'Noel Bah Sourou', 'sourou.bourandi@mairie.bj', NULL, 'commune_admin', 7, 1, 0, '$2y$12$5z3on9FNKRzTlbgYDxgOpuMwyu8M.N2iy/xvno2Qpy/LSI8ZyKoZ6', NULL, NULL, NULL, NOW(), NOW(), NOW()),
  ('YAYA', 'Nourou-Dine', 'nouroudine.yaya@mairie.bj', NULL, 'commune_admin', 7, 1, 0, '$2y$12$xIJmpfWgpgxauf88yZtF/.AeOQMVvqkvudexTOgv/.APlIhc8iAeG', NULL, NULL, NULL, NOW(), NOW(), NOW());
