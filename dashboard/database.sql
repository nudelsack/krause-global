-- Krause Global Deal Desk Dashboard - Database Schema
-- MySQL/MariaDB

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: deals
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `deals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_code` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `deal_type` enum('energy_equipment','energy_commodities','food','fertilizer') NOT NULL,
  `deal_subtype` varchar(100) DEFAULT NULL,
  `status` varchar(100) NOT NULL,
  `incoterms` varchar(50) DEFAULT NULL,
  `origin` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `quantity` decimal(15,2) DEFAULT NULL,
  `quantity_unit` varchar(50) DEFAULT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `commission_value` decimal(15,2) DEFAULT NULL,
  `commission_unit` varchar(50) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `website_category_url` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `deal_code` (`deal_code`),
  KEY `deal_type` (`deal_type`),
  KEY `status` (`status`),
  KEY `archived_at` (`archived_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: parties
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `party_type` enum('company','individual') NOT NULL DEFAULT 'company',
  `company_name` varchar(255) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `address_text` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `company_name` (`company_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: contacts
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `party_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role_title` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `party_id` (`party_id`),
  CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`party_id`) REFERENCES `parties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: deal_parties
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `deal_parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) NOT NULL,
  `party_id` int(11) NOT NULL,
  `role_in_deal` enum('buyer','seller','intermediary','logistics','bank','other') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `deal_id` (`deal_id`),
  KEY `party_id` (`party_id`),
  CONSTRAINT `deal_parties_ibfk_1` FOREIGN KEY (`deal_id`) REFERENCES `deals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `deal_parties_ibfk_2` FOREIGN KEY (`party_id`) REFERENCES `parties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: documents
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `source_type` enum('incoming','outgoing','internal') NOT NULL DEFAULT 'incoming',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `deal_id` (`deal_id`),
  KEY `category` (`category`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`deal_id`) REFERENCES `deals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: document_versions
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `document_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_path` varchar(500) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `sha256` char(64) NOT NULL,
  `version_no` int(11) NOT NULL DEFAULT 1,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `document_id` (`document_id`),
  KEY `sha256` (`sha256`),
  CONSTRAINT `document_versions_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: extracted_texts
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `extracted_texts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_version_id` int(11) NOT NULL,
  `extracted_text` longtext DEFAULT NULL,
  `extracted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `extraction_method` enum('text_layer','pdfparser','manual') NOT NULL,
  `is_manual_override` tinyint(1) NOT NULL DEFAULT 0,
  `manual_text` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_version_id` (`document_version_id`),
  FULLTEXT KEY `extracted_text` (`extracted_text`),
  FULLTEXT KEY `manual_text` (`manual_text`),
  CONSTRAINT `extracted_texts_ibfk_1` FOREIGN KEY (`document_version_id`) REFERENCES `document_versions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: workflow_templates
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `workflow_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_type` varchar(100) NOT NULL,
  `deal_subtype` varchar(100) DEFAULT NULL,
  `template_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `deal_type` (`deal_type`,`deal_subtype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: workflow_steps
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `workflow_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `step_order` int(11) NOT NULL,
  `step_key` varchar(100) NOT NULL,
  `step_title` varchar(255) NOT NULL,
  `required_document_categories_json` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`),
  CONSTRAINT `workflow_steps_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `workflow_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: deal_step_state
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `deal_step_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) NOT NULL,
  `step_id` int(11) NOT NULL,
  `state` enum('open','done','not_applicable') NOT NULL DEFAULT 'open',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `deal_step` (`deal_id`,`step_id`),
  KEY `step_id` (`step_id`),
  CONSTRAINT `deal_step_state_ibfk_1` FOREIGN KEY (`deal_id`) REFERENCES `deals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `deal_step_state_ibfk_2` FOREIGN KEY (`step_id`) REFERENCES `workflow_steps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: audit_log
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actor_user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `object_type` varchar(100) NOT NULL,
  `object_id` int(11) DEFAULT NULL,
  `payload_json` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `actor_user_id` (`actor_user_id`),
  KEY `object_type` (`object_type`,`object_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Initial Data: Create admin user (password: ChangeMe2026!)
-- --------------------------------------------------------
INSERT INTO `users` (`username`, `password_hash`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

COMMIT;
