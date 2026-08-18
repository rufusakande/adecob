-- =====================================================================
-- SCRIPT DE MISE À JOUR DU SCHÉMA — PLATEFORME ARMANI
-- Généré le 18/08/2026 19:38
--
-- SÛR ET IDEMPOTENT :
--   • Crée les tables manquantes (sans supprimer celles qui existent)
--   • Ajoute les colonnes manquantes sans toucher aux données existantes
--   • Peut être exécuté plusieurs fois sans erreur
--
-- COUVRE TOUTES LES MIGRATIONS, dont celles d'août 2026 :
--   2026_08_14_000001  audit_logs.action -> VARCHAR(50)
--   2026_08_18_000001  table infrastructure_assignments
--   2026_08_18_000002  colonnes annuelles/triennales (infrastructure_works)
--
-- IMPORTANT :
--   1) Dans phpMyAdmin, SÉLECTIONNEZ d'abord votre base de données
--      (ex : armanib2_b25deftr) PUIS importez ce fichier.
--   2) Cochez « Activer la vérification des clés étrangères » : NON
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------------------------------------
-- 1) Création des tables manquantes (sans risque)
-- ----------------------------------------------------------

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auditable_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `commune_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_index` (`user_id`),
  KEY `audit_logs_action_index` (`action`),
  KEY `audit_logs_auditable_type_index` (`auditable_type`),
  KEY `audit_logs_commune_id_index` (`commune_id`),
  KEY `audit_logs_created_at_index` (`created_at`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `communes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `communes_name_unique` (`name`),
  KEY `communes_created_by_foreign` (`created_by`),
  CONSTRAINT `communes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `import_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_rows` int unsigned DEFAULT NULL,
  `processed_rows` int unsigned NOT NULL DEFAULT '0',
  `skipped_rows` int unsigned NOT NULL DEFAULT '0',
  `errors` text COLLATE utf8mb4_unicode_ci,
  `started_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `import_statuses_user_id_foreign` (`user_id`),
  CONSTRAINT `import_statuses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `infrastructure_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `infrastructure_id` bigint unsigned NOT NULL,
  `assigned_to` bigint unsigned NOT NULL,
  `assigned_by` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'assigned',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `infrastructure_assignments_assigned_by_foreign` (`assigned_by`),
  KEY `infrastructure_assignments_reviewed_by_foreign` (`reviewed_by`),
  KEY `infrastructure_assignments_assigned_to_status_index` (`assigned_to`,`status`),
  KEY `infrastructure_assignments_infrastructure_id_index` (`infrastructure_id`),
  KEY `infrastructure_assignments_status_index` (`status`),
  CONSTRAINT `infrastructure_assignments_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `infrastructure_assignments_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `infrastructure_assignments_infrastructure_id_foreign` FOREIGN KEY (`infrastructure_id`) REFERENCES `infrastructures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `infrastructure_assignments_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `infrastructure_works` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `infrastructure_id` bigint unsigned NOT NULL,
  `work_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `completion_date` date NOT NULL,
  `observations` text COLLATE utf8mb4_unicode_ci,
  `provider_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acteurs_concernes` text COLLATE utf8mb4_unicode_ci,
  `sources_financement` text COLLATE utf8mb4_unicode_ci,
  `annee_execution` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unite` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantite` decimal(15,2) DEFAULT NULL,
  `cout_unitaire` decimal(15,2) DEFAULT NULL,
  `repartition_an1` decimal(15,2) DEFAULT NULL,
  `repartition_an2` decimal(15,2) DEFAULT NULL,
  `repartition_an3` decimal(15,2) DEFAULT NULL,
  `priorite` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `budget_annuel` decimal(15,2) DEFAULT NULL,
  `trimestre_t1` decimal(15,2) DEFAULT NULL,
  `trimestre_t2` decimal(15,2) DEFAULT NULL,
  `trimestre_t3` decimal(15,2) DEFAULT NULL,
  `trimestre_t4` decimal(15,2) DEFAULT NULL,
  `statut_execution` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost` decimal(15,2) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `infrastructure_works_infrastructure_id_foreign` (`infrastructure_id`),
  CONSTRAINT `infrastructure_works_infrastructure_id_foreign` FOREIGN KEY (`infrastructure_id`) REFERENCES `infrastructures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `infrastructures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `nom_enqueteur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_telephone` text COLLATE utf8mb4_unicode_ci,
  `commune` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arrondissement` json DEFAULT NULL,
  `village` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hameau` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `altitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precision` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secteur_domaine` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_infrastructure` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom_infrastructure` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `annee_realisation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bailleur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_materiaux` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat_fonctionnement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `niveau_degradation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode_gestion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode_gestion_preciser` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `defectuosites_relevees` text COLLATE utf8mb4_unicode_ci,
  `mesures_proposees` text COLLATE utf8mb4_unicode_ci,
  `observation_generale` text COLLATE utf8mb4_unicode_ci,
  `photo1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photos` json DEFAULT NULL,
  `photo_count` int NOT NULL DEFAULT '0',
  `rehabilitation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','pending','validated','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `validated_by` bigint unsigned DEFAULT NULL,
  `validated_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `exported_at` timestamp NULL DEFAULT NULL,
  `export_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `commune_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `infrastructures_user_id_foreign` (`user_id`),
  KEY `infrastructures_commune_id_foreign` (`commune_id`),
  KEY `infrastructures_validated_by_foreign` (`validated_by`),
  KEY `infra_status_commune_idx` (`status`,`commune_id`),
  CONSTRAINT `infrastructures_commune_id_foreign` FOREIGN KEY (`commune_id`) REFERENCES `communes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `infrastructures_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `infrastructures_validated_by_foreign` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mairie_agent_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `infrastructure_id` bigint unsigned DEFAULT NULL,
  `nom_enqueteur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commune` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commune_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `secteur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `localisation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activites` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsables` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `personnes_associes` int DEFAULT NULL,
  `source_financement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `montant` decimal(15,2) DEFAULT NULL,
  `periode_2023` tinyint(1) NOT NULL DEFAULT '0',
  `periode_2024` tinyint(1) NOT NULL DEFAULT '0',
  `periode_2025` tinyint(1) NOT NULL DEFAULT '0',
  `periode_2026` tinyint(1) NOT NULL DEFAULT '0',
  `periode_2027` tinyint(1) NOT NULL DEFAULT '0',
  `periode_2028` tinyint(1) NOT NULL DEFAULT '0',
  `periode_2029` tinyint(1) NOT NULL DEFAULT '0',
  `periode_2030` tinyint(1) NOT NULL DEFAULT '0',
  `maintenance_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maintenance_completed_date` date DEFAULT NULL,
  `maintenance_notes` text COLLATE utf8mb4_unicode_ci,
  `custom_planning_years` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mairie_agent_data_infrastructure_id_foreign` (`infrastructure_id`),
  KEY `mairie_agent_data_commune_id_index` (`commune_id`),
  KEY `mairie_agent_data_user_id_index` (`user_id`),
  CONSTRAINT `mairie_agent_data_commune_id_foreign` FOREIGN KEY (`commune_id`) REFERENCES `communes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `mairie_agent_data_infrastructure_id_foreign` FOREIGN KEY (`infrastructure_id`) REFERENCES `infrastructures` (`id`) ON DELETE SET NULL,
  CONSTRAINT `mairie_agent_data_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mfa_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `code_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL DEFAULT '0',
  `expires_at` timestamp NOT NULL,
  `consumed_at` timestamp NULL DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mfa_codes_user_id_consumed_at_index` (`user_id`,`consumed_at`),
  CONSTRAINT `mfa_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` text COLLATE utf8mb4_unicode_ci,
  `role` enum('super_admin','commune_admin','agent','public_user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public_user',
  `commune_id` bigint unsigned DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `role_changed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------
-- 2) Helper : ajoute une colonne UNIQUEMENT si elle n'existe pas
-- ----------------------------------------------------------
DROP PROCEDURE IF EXISTS __ensure_col;
DELIMITER $$
CREATE PROCEDURE __ensure_col(IN tbl VARCHAR(128), IN col VARCHAR(128), IN ddl TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = col
  ) THEN
    SET @__sql = CONCAT('ALTER TABLE `', tbl, '` ADD COLUMN ', ddl);
    PREPARE __stmt FROM @__sql; EXECUTE __stmt; DEALLOCATE PREPARE __stmt;
  END IF;
END$$
DELIMITER ;

-- ----------------------------------------------------------
-- 3) Ajout des colonnes manquantes pour CHAQUE table
-- ----------------------------------------------------------

-- Table `audit_logs` (18 colonnes)
CALL __ensure_col('audit_logs', 'id', '`id` bigint unsigned NOT NULL AUTO_INCREMENT');
CALL __ensure_col('audit_logs', 'user_id', '`user_id` bigint unsigned DEFAULT NULL');
CALL __ensure_col('audit_logs', 'user_name', '`user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('audit_logs', 'action', '`action` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('audit_logs', 'auditable_type', '`auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('audit_logs', 'auditable_id', '`auditable_id` bigint unsigned DEFAULT NULL');
CALL __ensure_col('audit_logs', 'description', '`description` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('audit_logs', 'old_values', '`old_values` json DEFAULT NULL');
CALL __ensure_col('audit_logs', 'new_values', '`new_values` json DEFAULT NULL');
CALL __ensure_col('audit_logs', 'ip_address', '`ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('audit_logs', 'user_agent', '`user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('audit_logs', 'method', '`method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('audit_logs', 'url', '`url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('audit_logs', 'status', '`status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''success''');
CALL __ensure_col('audit_logs', 'error_message', '`error_message` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('audit_logs', 'commune_id', '`commune_id` bigint unsigned DEFAULT NULL');
CALL __ensure_col('audit_logs', 'created_at', '`created_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('audit_logs', 'updated_at', '`updated_at` timestamp NULL DEFAULT NULL');

-- Table `communes` (6 colonnes)
CALL __ensure_col('communes', 'id', '`id` bigint unsigned NOT NULL AUTO_INCREMENT');
CALL __ensure_col('communes', 'name', '`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('communes', 'logo', '`logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('communes', 'created_by', '`created_by` bigint unsigned DEFAULT NULL');
CALL __ensure_col('communes', 'created_at', '`created_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('communes', 'updated_at', '`updated_at` timestamp NULL DEFAULT NULL');

-- Table `failed_jobs` (7 colonnes)
CALL __ensure_col('failed_jobs', 'id', '`id` bigint unsigned NOT NULL AUTO_INCREMENT');
CALL __ensure_col('failed_jobs', 'uuid', '`uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('failed_jobs', 'connection', '`connection` text COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('failed_jobs', 'queue', '`queue` text COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('failed_jobs', 'payload', '`payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('failed_jobs', 'exception', '`exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('failed_jobs', 'failed_at', '`failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');

-- Table `import_statuses` (12 colonnes)
CALL __ensure_col('import_statuses', 'id', '`id` bigint unsigned NOT NULL AUTO_INCREMENT');
CALL __ensure_col('import_statuses', 'user_id', '`user_id` bigint unsigned DEFAULT NULL');
CALL __ensure_col('import_statuses', 'file_name', '`file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('import_statuses', 'status', '`status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''pending''');
CALL __ensure_col('import_statuses', 'total_rows', '`total_rows` int unsigned DEFAULT NULL');
CALL __ensure_col('import_statuses', 'processed_rows', '`processed_rows` int unsigned NOT NULL DEFAULT ''0''');
CALL __ensure_col('import_statuses', 'skipped_rows', '`skipped_rows` int unsigned NOT NULL DEFAULT ''0''');
CALL __ensure_col('import_statuses', 'errors', '`errors` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('import_statuses', 'started_at', '`started_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('import_statuses', 'finished_at', '`finished_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('import_statuses', 'created_at', '`created_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('import_statuses', 'updated_at', '`updated_at` timestamp NULL DEFAULT NULL');

-- Table `infrastructure_assignments` (11 colonnes)
CALL __ensure_col('infrastructure_assignments', 'id', '`id` bigint unsigned NOT NULL AUTO_INCREMENT');
CALL __ensure_col('infrastructure_assignments', 'infrastructure_id', '`infrastructure_id` bigint unsigned NOT NULL');
CALL __ensure_col('infrastructure_assignments', 'assigned_to', '`assigned_to` bigint unsigned NOT NULL');
CALL __ensure_col('infrastructure_assignments', 'assigned_by', '`assigned_by` bigint unsigned NOT NULL');
CALL __ensure_col('infrastructure_assignments', 'status', '`status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''assigned''');
CALL __ensure_col('infrastructure_assignments', 'submitted_at', '`submitted_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('infrastructure_assignments', 'reviewed_by', '`reviewed_by` bigint unsigned DEFAULT NULL');
CALL __ensure_col('infrastructure_assignments', 'reviewed_at', '`reviewed_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('infrastructure_assignments', 'rejection_reason', '`rejection_reason` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('infrastructure_assignments', 'created_at', '`created_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('infrastructure_assignments', 'updated_at', '`updated_at` timestamp NULL DEFAULT NULL');

-- Table `infrastructure_works` (28 colonnes)
CALL __ensure_col('infrastructure_works', 'id', '`id` bigint unsigned NOT NULL AUTO_INCREMENT');
CALL __ensure_col('infrastructure_works', 'infrastructure_id', '`infrastructure_id` bigint unsigned NOT NULL');
CALL __ensure_col('infrastructure_works', 'work_type', '`work_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('infrastructure_works', 'description', '`description` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('infrastructure_works', 'completion_date', '`completion_date` date NOT NULL');
CALL __ensure_col('infrastructure_works', 'observations', '`observations` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('infrastructure_works', 'provider_name', '`provider_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'provider_contact', '`provider_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'acteurs_concernes', '`acteurs_concernes` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('infrastructure_works', 'sources_financement', '`sources_financement` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('infrastructure_works', 'annee_execution', '`annee_execution` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'unite', '`unite` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'quantite', '`quantite` decimal(15,2) DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'cout_unitaire', '`cout_unitaire` decimal(15,2) DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'repartition_an1', '`repartition_an1` decimal(15,2) DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'repartition_an2', '`repartition_an2` decimal(15,2) DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'repartition_an3', '`repartition_an3` decimal(15,2) DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'priorite', '`priorite` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'budget_annuel', '`budget_annuel` decimal(15,2) DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'trimestre_t1', '`trimestre_t1` decimal(15,2) DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'trimestre_t2', '`trimestre_t2` decimal(15,2) DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'trimestre_t3', '`trimestre_t3` decimal(15,2) DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'trimestre_t4', '`trimestre_t4` decimal(15,2) DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'statut_execution', '`statut_execution` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'cost', '`cost` decimal(15,2) DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'status', '`status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''completed''');
CALL __ensure_col('infrastructure_works', 'created_at', '`created_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('infrastructure_works', 'updated_at', '`updated_at` timestamp NULL DEFAULT NULL');

-- Table `infrastructures` (43 colonnes)
CALL __ensure_col('infrastructures', 'id', '`id` bigint unsigned NOT NULL AUTO_INCREMENT');
CALL __ensure_col('infrastructures', 'date', '`date` date DEFAULT NULL');
CALL __ensure_col('infrastructures', 'nom_enqueteur', '`nom_enqueteur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'numero_telephone', '`numero_telephone` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('infrastructures', 'commune', '`commune` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'arrondissement', '`arrondissement` json DEFAULT NULL');
CALL __ensure_col('infrastructures', 'village', '`village` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'hameau', '`hameau` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'latitude', '`latitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'longitude', '`longitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'altitude', '`altitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'precision', '`precision` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'secteur_domaine', '`secteur_domaine` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'type_infrastructure', '`type_infrastructure` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'nom_infrastructure', '`nom_infrastructure` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'annee_realisation', '`annee_realisation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'bailleur', '`bailleur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'type_materiaux', '`type_materiaux` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'etat_fonctionnement', '`etat_fonctionnement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'niveau_degradation', '`niveau_degradation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'mode_gestion', '`mode_gestion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'mode_gestion_preciser', '`mode_gestion_preciser` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'defectuosites_relevees', '`defectuosites_relevees` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('infrastructures', 'mesures_proposees', '`mesures_proposees` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('infrastructures', 'observation_generale', '`observation_generale` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('infrastructures', 'photo1', '`photo1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'photo2', '`photo2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'photo3', '`photo3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'photo4', '`photo4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'photos', '`photos` json DEFAULT NULL');
CALL __ensure_col('infrastructures', 'photo_count', '`photo_count` int NOT NULL DEFAULT ''0''');
CALL __ensure_col('infrastructures', 'rehabilitation', '`rehabilitation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('infrastructures', 'status', '`status` enum(''draft'',''pending'',''validated'',''rejected'') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''pending''');
CALL __ensure_col('infrastructures', 'validated_by', '`validated_by` bigint unsigned DEFAULT NULL');
CALL __ensure_col('infrastructures', 'validated_at', '`validated_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('infrastructures', 'submitted_at', '`submitted_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('infrastructures', 'rejection_reason', '`rejection_reason` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('infrastructures', 'exported_at', '`exported_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('infrastructures', 'export_count', '`export_count` int unsigned NOT NULL DEFAULT ''0''');
CALL __ensure_col('infrastructures', 'created_at', '`created_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('infrastructures', 'updated_at', '`updated_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('infrastructures', 'user_id', '`user_id` bigint unsigned DEFAULT NULL');
CALL __ensure_col('infrastructures', 'commune_id', '`commune_id` bigint unsigned DEFAULT NULL');

-- Table `jobs` (7 colonnes)
CALL __ensure_col('jobs', 'id', '`id` bigint unsigned NOT NULL AUTO_INCREMENT');
CALL __ensure_col('jobs', 'queue', '`queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('jobs', 'payload', '`payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('jobs', 'attempts', '`attempts` tinyint unsigned NOT NULL');
CALL __ensure_col('jobs', 'reserved_at', '`reserved_at` int unsigned DEFAULT NULL');
CALL __ensure_col('jobs', 'available_at', '`available_at` int unsigned NOT NULL');
CALL __ensure_col('jobs', 'created_at', '`created_at` int unsigned NOT NULL');

-- Table `mairie_agent_data` (28 colonnes)
CALL __ensure_col('mairie_agent_data', 'id', '`id` bigint unsigned NOT NULL AUTO_INCREMENT');
CALL __ensure_col('mairie_agent_data', 'infrastructure_id', '`infrastructure_id` bigint unsigned DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'nom_enqueteur', '`nom_enqueteur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('mairie_agent_data', 'commune', '`commune` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('mairie_agent_data', 'commune_id', '`commune_id` bigint unsigned DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'user_id', '`user_id` bigint unsigned DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'secteur', '`secteur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'designation', '`designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('mairie_agent_data', 'localisation', '`localisation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'activites', '`activites` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'responsables', '`responsables` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'personnes_associes', '`personnes_associes` int DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'source_financement', '`source_financement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'montant', '`montant` decimal(15,2) DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'periode_2023', '`periode_2023` tinyint(1) NOT NULL DEFAULT ''0''');
CALL __ensure_col('mairie_agent_data', 'periode_2024', '`periode_2024` tinyint(1) NOT NULL DEFAULT ''0''');
CALL __ensure_col('mairie_agent_data', 'periode_2025', '`periode_2025` tinyint(1) NOT NULL DEFAULT ''0''');
CALL __ensure_col('mairie_agent_data', 'periode_2026', '`periode_2026` tinyint(1) NOT NULL DEFAULT ''0''');
CALL __ensure_col('mairie_agent_data', 'periode_2027', '`periode_2027` tinyint(1) NOT NULL DEFAULT ''0''');
CALL __ensure_col('mairie_agent_data', 'periode_2028', '`periode_2028` tinyint(1) NOT NULL DEFAULT ''0''');
CALL __ensure_col('mairie_agent_data', 'periode_2029', '`periode_2029` tinyint(1) NOT NULL DEFAULT ''0''');
CALL __ensure_col('mairie_agent_data', 'periode_2030', '`periode_2030` tinyint(1) NOT NULL DEFAULT ''0''');
CALL __ensure_col('mairie_agent_data', 'maintenance_status', '`maintenance_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'maintenance_completed_date', '`maintenance_completed_date` date DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'maintenance_notes', '`maintenance_notes` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('mairie_agent_data', 'custom_planning_years', '`custom_planning_years` json DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'created_at', '`created_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('mairie_agent_data', 'updated_at', '`updated_at` timestamp NULL DEFAULT NULL');

-- Table `mfa_codes` (9 colonnes)
CALL __ensure_col('mfa_codes', 'id', '`id` bigint unsigned NOT NULL AUTO_INCREMENT');
CALL __ensure_col('mfa_codes', 'user_id', '`user_id` bigint unsigned NOT NULL');
CALL __ensure_col('mfa_codes', 'code_hash', '`code_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('mfa_codes', 'attempts', '`attempts` tinyint unsigned NOT NULL DEFAULT ''0''');
CALL __ensure_col('mfa_codes', 'expires_at', '`expires_at` timestamp NOT NULL');
CALL __ensure_col('mfa_codes', 'consumed_at', '`consumed_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('mfa_codes', 'ip', '`ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('mfa_codes', 'created_at', '`created_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('mfa_codes', 'updated_at', '`updated_at` timestamp NULL DEFAULT NULL');

-- Table `migrations` (3 colonnes)
CALL __ensure_col('migrations', 'id', '`id` int unsigned NOT NULL AUTO_INCREMENT');
CALL __ensure_col('migrations', 'migration', '`migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('migrations', 'batch', '`batch` int NOT NULL');

-- Table `notifications` (8 colonnes)
CALL __ensure_col('notifications', 'id', '`id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('notifications', 'type', '`type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('notifications', 'notifiable_type', '`notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('notifications', 'notifiable_id', '`notifiable_id` bigint unsigned NOT NULL');
CALL __ensure_col('notifications', 'data', '`data` text COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('notifications', 'read_at', '`read_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('notifications', 'created_at', '`created_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('notifications', 'updated_at', '`updated_at` timestamp NULL DEFAULT NULL');

-- Table `password_reset_tokens` (3 colonnes)
CALL __ensure_col('password_reset_tokens', 'email', '`email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('password_reset_tokens', 'token', '`token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('password_reset_tokens', 'created_at', '`created_at` timestamp NULL DEFAULT NULL');

-- Table `personal_access_tokens` (10 colonnes)
CALL __ensure_col('personal_access_tokens', 'id', '`id` bigint unsigned NOT NULL AUTO_INCREMENT');
CALL __ensure_col('personal_access_tokens', 'tokenable_type', '`tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('personal_access_tokens', 'tokenable_id', '`tokenable_id` bigint unsigned NOT NULL');
CALL __ensure_col('personal_access_tokens', 'name', '`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('personal_access_tokens', 'token', '`token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('personal_access_tokens', 'abilities', '`abilities` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('personal_access_tokens', 'last_used_at', '`last_used_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('personal_access_tokens', 'expires_at', '`expires_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('personal_access_tokens', 'created_at', '`created_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('personal_access_tokens', 'updated_at', '`updated_at` timestamp NULL DEFAULT NULL');

-- Table `users` (17 colonnes)
CALL __ensure_col('users', 'id', '`id` bigint unsigned NOT NULL AUTO_INCREMENT');
CALL __ensure_col('users', 'name', '`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('users', 'prenom', '`prenom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('users', 'email', '`email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('users', 'telephone', '`telephone` text COLLATE utf8mb4_unicode_ci');
CALL __ensure_col('users', 'role', '`role` enum(''super_admin'',''commune_admin'',''agent'',''public_user'') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''public_user''');
CALL __ensure_col('users', 'commune_id', '`commune_id` bigint unsigned DEFAULT NULL');
CALL __ensure_col('users', 'is_approved', '`is_approved` tinyint(1) NOT NULL DEFAULT ''0''');
CALL __ensure_col('users', 'email_verified_at', '`email_verified_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('users', 'password', '`password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
CALL __ensure_col('users', 'remember_token', '`remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
CALL __ensure_col('users', 'created_at', '`created_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('users', 'updated_at', '`updated_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('users', 'is_admin', '`is_admin` tinyint(1) NOT NULL DEFAULT ''0''');
CALL __ensure_col('users', 'approved_at', '`approved_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('users', 'rejected_at', '`rejected_at` timestamp NULL DEFAULT NULL');
CALL __ensure_col('users', 'role_changed_at', '`role_changed_at` timestamp NULL DEFAULT NULL');


-- ----------------------------------------------------------
-- 4) Changements de type de colonnes (migrations récentes)
-- ----------------------------------------------------------

-- 2026_08_14_000001 : audit_logs.action ENUM -> VARCHAR(50)
ALTER TABLE `audit_logs` MODIFY COLUMN `action` VARCHAR(50) NULL;

-- 2026_07_18_232532 : infrastructure_works.annee_execution -> VARCHAR(255)
ALTER TABLE `infrastructure_works` MODIFY COLUMN `annee_execution` VARCHAR(255) NULL;

-- ----------------------------------------------------------
-- 5) Nettoyage
-- ----------------------------------------------------------
DROP PROCEDURE IF EXISTS __ensure_col;
SET FOREIGN_KEY_CHECKS=1;

-- =====================================================================
-- FIN DU SCRIPT — Vérifiez qu'aucune erreur n'est apparue dans phpMyAdmin
-- =====================================================================