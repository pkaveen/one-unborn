-- ============================================
-- One Unborn - Client Portal Install Script
-- Import this file in phpMyAdmin (MySQL/MariaDB)
-- Adds client portal auth fields + creates monitoring tables
-- Compatible with MariaDB 10.4+ and MySQL 5.7+/8.0
-- ============================================

START TRANSACTION;

-- ============================================
-- Add Client Portal Authentication Fields (clients table)
-- ============================================
-- NOTE: Run on a DB that already has `clients` table

ALTER TABLE `clients`
  ADD COLUMN `portal_username` varchar(255) NULL AFTER `support_spoc_email`;

ALTER TABLE `clients`
  ADD COLUMN `portal_password` varchar(255) NULL AFTER `portal_username`;

ALTER TABLE `clients`
  ADD COLUMN `portal_active` tinyint(1) NOT NULL DEFAULT 0 AFTER `portal_password`;

ALTER TABLE `clients`
  ADD COLUMN `portal_last_login` timestamp NULL DEFAULT NULL AFTER `portal_active`;

ALTER TABLE `clients`
  ADD COLUMN `remember_token` varchar(100) NULL AFTER `portal_last_login`;

-- Indexes for portal auth
CREATE UNIQUE INDEX `clients_portal_username_unique` ON `clients` (`portal_username`);
CREATE INDEX `clients_portal_active_index` ON `clients` (`portal_active`);

-- ============================================
-- MikroTik Routers
-- ============================================
CREATE TABLE IF NOT EXISTS `mikrotik_routers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `management_ip` varchar(45) NOT NULL,
  `api_port` int(11) NOT NULL DEFAULT 8728,
  `api_username` varchar(255) NOT NULL,
  `api_password` text NOT NULL COMMENT 'Encrypted password',
  `use_ssl` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `location` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `last_connected_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mikrotik_routers_company_id_foreign` (`company_id`),
  KEY `mikrotik_routers_status_index` (`status`),
  CONSTRAINT `mikrotik_routers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Client Links
-- ============================================
CREATE TABLE IF NOT EXISTS `client_links` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `deliverable_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `router_id` bigint(20) UNSIGNED NOT NULL,
  `interface_name` varchar(100) NOT NULL,
  `circuit_id` varchar(255) DEFAULT NULL,
  `link_name` varchar(255) DEFAULT NULL,
  `link_type` varchar(50) NOT NULL DEFAULT 'ILL',
  `bandwidth_committed` int(11) NOT NULL COMMENT 'Mbps',
  `sla_uptime` decimal(5,2) NOT NULL DEFAULT 99.50 COMMENT 'Percentage',
  `sla_latency` int(11) NOT NULL DEFAULT 20 COMMENT 'Max milliseconds',
  `sla_packet_loss` decimal(5,2) NOT NULL DEFAULT 1.00 COMMENT 'Max percentage',
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `activation_date` date DEFAULT NULL,
  `grafana_dashboard_uid` varchar(255) DEFAULT NULL,
  `monitoring_config` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_links_deliverable_id_foreign` (`deliverable_id`),
  KEY `client_links_client_id_foreign` (`client_id`),
  KEY `client_links_router_id_foreign` (`router_id`),
  KEY `client_links_status_index` (`status`),
  CONSTRAINT `client_links_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_links_deliverable_id_foreign` FOREIGN KEY (`deliverable_id`) REFERENCES `deliverables` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_links_router_id_foreign` FOREIGN KEY (`router_id`) REFERENCES `mikrotik_routers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Link Monitoring Data (5-min intervals)
-- ============================================
CREATE TABLE IF NOT EXISTS `link_monitoring_data` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_link_id` bigint(20) UNSIGNED NOT NULL,
  `interface_status` varchar(20) NOT NULL COMMENT 'up or down',
  `rx_bytes` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `tx_bytes` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `rx_packets` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `tx_packets` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `rx_errors` int(11) NOT NULL DEFAULT 0,
  `tx_errors` int(11) NOT NULL DEFAULT 0,
  `rx_rate_mbps` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tx_rate_mbps` decimal(10,2) NOT NULL DEFAULT 0.00,
  `latency_ms` decimal(10,2) DEFAULT NULL,
  `packet_loss_percent` decimal(5,2) DEFAULT NULL,
  `collected_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `link_monitoring_data_client_link_id_foreign` (`client_link_id`),
  KEY `link_monitoring_data_collected_at_index` (`collected_at`),
  KEY `link_monitoring_data_link_collected_index` (`client_link_id`, `collected_at`),
  CONSTRAINT `link_monitoring_data_client_link_id_foreign` FOREIGN KEY (`client_link_id`) REFERENCES `client_links` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SLA Reports (Monthly)
-- ============================================
CREATE TABLE IF NOT EXISTS `sla_reports` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_link_id` bigint(20) UNSIGNED NOT NULL,
  `report_month` date NOT NULL COMMENT 'First day of the month',
  `uptime_percentage` decimal(8,4) NOT NULL,
  `total_downtime_seconds` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `avg_latency_ms` decimal(10,2) NOT NULL DEFAULT 0.00,
  `avg_packet_loss_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sla_met` tinyint(1) NOT NULL DEFAULT 1,
  `breach_details` json DEFAULT NULL COMMENT 'Array of breach reasons',
  `calculated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sla_reports_link_month_unique` (`client_link_id`, `report_month`),
  KEY `sla_reports_client_link_id_foreign` (`client_link_id`),
  KEY `sla_reports_report_month_index` (`report_month`),
  KEY `sla_reports_sla_met_index` (`sla_met`),
  CONSTRAINT `sla_reports_client_link_id_foreign` FOREIGN KEY (`client_link_id`) REFERENCES `client_links` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Notification Settings
-- ============================================
CREATE TABLE IF NOT EXISTS `notification_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `sla_breach_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sla_breach_recipients` text DEFAULT NULL COMMENT 'JSON array of emails',
  `sla_breach_to_client` tinyint(1) NOT NULL DEFAULT 1,
  `sla_breach_to_operations` tinyint(1) NOT NULL DEFAULT 1,
  `link_down_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `link_down_threshold_minutes` int(11) NOT NULL DEFAULT 5,
  `link_down_recipients` text DEFAULT NULL COMMENT 'JSON array of emails',
  `high_latency_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `high_latency_threshold_ms` int(11) NOT NULL DEFAULT 50,
  `high_latency_duration_minutes` int(11) NOT NULL DEFAULT 10,
  `high_latency_recipients` text DEFAULT NULL COMMENT 'JSON array of emails',
  `high_packet_loss_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `high_packet_loss_threshold_percent` decimal(5,2) NOT NULL DEFAULT 2.00,
  `high_packet_loss_duration_minutes` int(11) NOT NULL DEFAULT 10,
  `high_packet_loss_recipients` text DEFAULT NULL COMMENT 'JSON array of emails',
  `whatsapp_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `whatsapp_numbers` text DEFAULT NULL COMMENT 'JSON array of phone numbers',
  `email_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `email_from` varchar(255) DEFAULT NULL,
  `alert_cooldown_minutes` int(11) NOT NULL DEFAULT 30,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_settings_company_id_unique` (`company_id`),
  CONSTRAINT `notification_settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Notification Logs
-- ============================================
CREATE TABLE IF NOT EXISTS `notification_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_link_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `notification_type` varchar(100) NOT NULL,
  `channel` varchar(50) NOT NULL,
  `recipients` text NOT NULL COMMENT 'JSON array',
  `message` text NOT NULL,
  `metadata` json DEFAULT NULL,
  `sent_successfully` tinyint(1) NOT NULL DEFAULT 0,
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_logs_client_link_id_foreign` (`client_link_id`),
  KEY `notification_logs_company_id_foreign` (`company_id`),
  KEY `notification_logs_link_type_created_index` (`client_link_id`, `notification_type`, `created_at`),
  KEY `notification_logs_type_index` (`notification_type`),
  CONSTRAINT `notification_logs_client_link_id_foreign` FOREIGN KEY (`client_link_id`) REFERENCES `client_links` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Performance Indexes
-- ============================================
CREATE INDEX IF NOT EXISTS idx_monitoring_link_time ON link_monitoring_data(client_link_id, collected_at);
CREATE INDEX IF NOT EXISTS idx_monitoring_status_time ON link_monitoring_data(interface_status, collected_at);
CREATE INDEX IF NOT EXISTS idx_sla_reports_link_month ON sla_reports(client_link_id, report_month);
CREATE INDEX IF NOT EXISTS idx_notification_logs_company_time ON notification_logs(company_id, created_at);

-- ============================================
-- Seed Defaults (per company)
-- ============================================
INSERT INTO `notification_settings` (
    `company_id`,
    `sla_breach_enabled`,
    `sla_breach_to_client`,
    `sla_breach_to_operations`,
    `link_down_enabled`,
    `link_down_threshold_minutes`,
    `high_latency_enabled`,
    `high_latency_threshold_ms`,
    `high_latency_duration_minutes`,
    `high_packet_loss_enabled`,
    `high_packet_loss_threshold_percent`,
    `high_packet_loss_duration_minutes`,
    `email_enabled`,
    `whatsapp_enabled`,
    `alert_cooldown_minutes`,
    `created_at`,
    `updated_at`
)
SELECT 
    c.`id` as company_id,
    1, 1, 1,
    1, 5,
    1, 50, 10,
    1, 2.00, 10,
    1, 0, 30,
    NOW(), NOW()
FROM `companies` c
WHERE NOT EXISTS (
    SELECT 1 FROM `notification_settings` ns WHERE ns.`company_id` = c.`id`
);

COMMIT;

-- ============================================
-- Rollback (Manual)
-- ============================================
-- To rollback portal fields:
-- ALTER TABLE `clients` DROP INDEX `clients_portal_active_index`;
-- ALTER TABLE `clients` DROP INDEX `clients_portal_username_unique`;
-- ALTER TABLE `clients` DROP COLUMN `remember_token`;
-- ALTER TABLE `clients` DROP COLUMN `portal_last_login`;
-- ALTER TABLE `clients` DROP COLUMN `portal_active`;
-- ALTER TABLE `clients` DROP COLUMN `portal_password`;
-- ALTER TABLE `clients` DROP COLUMN `portal_username`;
