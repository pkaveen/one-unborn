-- ============================================
-- One Unborn - Client Portal Database Schema
-- Complete MySQL Script for All Tables
-- ============================================

-- Table: client_portal_users
-- Purpose: Separate authentication for client portal users
CREATE TABLE IF NOT EXISTS `client_portal_users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_portal_users_username_unique` (`username`),
  UNIQUE KEY `client_portal_users_email_unique` (`email`),
  KEY `client_portal_users_client_id_foreign` (`client_id`),
  CONSTRAINT `client_portal_users_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mikrotik_routers
-- Purpose: MikroTik router inventory with API credentials
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

-- Table: client_links
-- Purpose: Links clients to router interfaces with SLA commitments
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

-- Table: link_monitoring_data
-- Purpose: Time-series metrics for client links (collected every 5 minutes)
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

-- Table: sla_reports
-- Purpose: Monthly SLA calculation results
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

-- Table: notification_settings
-- Purpose: Admin-controlled notification configuration per company
CREATE TABLE IF NOT EXISTS `notification_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  
  -- SLA Notifications
  `sla_breach_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sla_breach_recipients` text DEFAULT NULL COMMENT 'JSON array of emails',
  `sla_breach_to_client` tinyint(1) NOT NULL DEFAULT 1,
  `sla_breach_to_operations` tinyint(1) NOT NULL DEFAULT 1,
  
  -- Real-time Link Down Alerts
  `link_down_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `link_down_threshold_minutes` int(11) NOT NULL DEFAULT 5 COMMENT 'Alert after X minutes down',
  `link_down_recipients` text DEFAULT NULL COMMENT 'JSON array of emails',
  
  -- High Latency Alerts
  `high_latency_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `high_latency_threshold_ms` int(11) NOT NULL DEFAULT 50 COMMENT 'Alert when latency > X ms',
  `high_latency_duration_minutes` int(11) NOT NULL DEFAULT 10 COMMENT 'Sustained for X minutes',
  `high_latency_recipients` text DEFAULT NULL COMMENT 'JSON array of emails',
  
  -- High Packet Loss Alerts
  `high_packet_loss_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `high_packet_loss_threshold_percent` decimal(5,2) NOT NULL DEFAULT 2.00,
  `high_packet_loss_duration_minutes` int(11) NOT NULL DEFAULT 10,
  `high_packet_loss_recipients` text DEFAULT NULL COMMENT 'JSON array of emails',
  
  -- WhatsApp Notifications
  `whatsapp_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `whatsapp_numbers` text DEFAULT NULL COMMENT 'JSON array of phone numbers',
  
  -- Email Settings
  `email_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `email_from` varchar(255) DEFAULT NULL,
  
  -- Alert Cooldown (prevent spam)
  `alert_cooldown_minutes` int(11) NOT NULL DEFAULT 30,
  
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_settings_company_id_unique` (`company_id`),
  CONSTRAINT `notification_settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: notification_logs
-- Purpose: Track all sent notifications with success/failure status
CREATE TABLE IF NOT EXISTS `notification_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_link_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `notification_type` varchar(100) NOT NULL COMMENT 'sla_breach, link_down, high_latency, high_packet_loss',
  `channel` varchar(50) NOT NULL COMMENT 'email, whatsapp',
  `recipients` text NOT NULL COMMENT 'JSON array',
  `message` text NOT NULL,
  `metadata` json DEFAULT NULL COMMENT 'Additional context',
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
-- Sample Data Insert Statements
-- ============================================

-- Insert default notification settings for existing companies
-- Run this after creating the tables
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
    `id` as company_id,
    1, -- sla_breach_enabled
    1, -- sla_breach_to_client
    1, -- sla_breach_to_operations
    1, -- link_down_enabled
    5, -- link_down_threshold_minutes
    1, -- high_latency_enabled
    50, -- high_latency_threshold_ms
    10, -- high_latency_duration_minutes
    1, -- high_packet_loss_enabled
    2.00, -- high_packet_loss_threshold_percent
    10, -- high_packet_loss_duration_minutes
    1, -- email_enabled
    0, -- whatsapp_enabled
    30, -- alert_cooldown_minutes
    NOW(),
    NOW()
FROM `companies`
WHERE NOT EXISTS (
    SELECT 1 FROM `notification_settings` WHERE `notification_settings`.`company_id` = `companies`.`id`
);

-- ============================================
-- Useful Queries for Admin/Operations
-- ============================================

-- Get all active links with latest status
SELECT 
    cl.id,
    cl.link_name,
    cl.interface_name,
    c.company_name as client_name,
    mr.name as router_name,
    lmd.interface_status,
    lmd.latency_ms,
    lmd.packet_loss_percent,
    lmd.collected_at as last_checked
FROM client_links cl
LEFT JOIN clients c ON cl.client_id = c.id
LEFT JOIN mikrotik_routers mr ON cl.router_id = mr.id
LEFT JOIN link_monitoring_data lmd ON cl.id = lmd.client_link_id 
    AND lmd.id = (
        SELECT MAX(id) FROM link_monitoring_data WHERE client_link_id = cl.id
    )
WHERE cl.status = 'active'
ORDER BY cl.id;

-- Get SLA breach summary for current month
SELECT 
    cl.link_name,
    c.company_name as client_name,
    sr.report_month,
    sr.uptime_percentage,
    sr.avg_latency_ms,
    sr.sla_met,
    sr.breach_details
FROM sla_reports sr
JOIN client_links cl ON sr.client_link_id = cl.id
JOIN clients c ON cl.client_id = c.id
WHERE sr.sla_met = 0
    AND sr.report_month >= DATE_FORMAT(NOW(), '%Y-%m-01')
ORDER BY sr.report_month DESC, c.company_name;

-- Get notification statistics per company
SELECT 
    c.company_name,
    nl.notification_type,
    COUNT(*) as total_notifications,
    SUM(CASE WHEN nl.sent_successfully = 1 THEN 1 ELSE 0 END) as successful,
    SUM(CASE WHEN nl.sent_successfully = 0 THEN 1 ELSE 0 END) as failed
FROM notification_logs nl
JOIN companies c ON nl.company_id = c.id
WHERE nl.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY c.company_name, nl.notification_type
ORDER BY c.company_name, nl.notification_type;

-- Get links that are currently down
SELECT 
    cl.id,
    cl.link_name,
    c.company_name as client_name,
    mr.name as router_name,
    mr.management_ip,
    cl.interface_name,
    lmd.collected_at as down_since,
    TIMESTAMPDIFF(MINUTE, lmd.collected_at, NOW()) as down_minutes
FROM client_links cl
JOIN clients c ON cl.client_id = c.id
JOIN mikrotik_routers mr ON cl.router_id = mr.id
JOIN link_monitoring_data lmd ON cl.id = lmd.client_link_id
    AND lmd.id = (
        SELECT MAX(id) FROM link_monitoring_data WHERE client_link_id = cl.id
    )
WHERE cl.status = 'active'
    AND lmd.interface_status = 'down'
ORDER BY lmd.collected_at ASC;

-- Get average performance metrics for last 24 hours per link
SELECT 
    cl.id,
    cl.link_name,
    c.company_name as client_name,
    COUNT(*) as samples,
    AVG(lmd.latency_ms) as avg_latency,
    AVG(lmd.packet_loss_percent) as avg_packet_loss,
    AVG(lmd.rx_rate_mbps) as avg_rx_mbps,
    AVG(lmd.tx_rate_mbps) as avg_tx_mbps,
    SUM(CASE WHEN lmd.interface_status = 'up' THEN 1 ELSE 0 END) / COUNT(*) * 100 as uptime_percentage
FROM client_links cl
JOIN clients c ON cl.client_id = c.id
LEFT JOIN link_monitoring_data lmd ON cl.id = lmd.client_link_id
    AND lmd.collected_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
WHERE cl.status = 'active'
GROUP BY cl.id, cl.link_name, c.company_name
ORDER BY cl.id;

-- ============================================
-- Maintenance Queries
-- ============================================

-- Delete old monitoring data (keep only last 90 days)
DELETE FROM link_monitoring_data 
WHERE collected_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- Delete old notification logs (keep only last 180 days)
DELETE FROM notification_logs 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 180 DAY);

-- Recount and fix SLA reports if needed
-- (Run this if you suspect data inconsistencies)
-- This is just a template - actual SLA recalculation is done via artisan command:
-- php artisan sla:calculate-monthly --month=2025-11

-- ============================================
-- Indexes for Performance Optimization
-- ============================================

-- Additional composite indexes for frequently queried combinations
CREATE INDEX idx_monitoring_link_time ON link_monitoring_data(client_link_id, collected_at DESC);
CREATE INDEX idx_monitoring_status_time ON link_monitoring_data(interface_status, collected_at DESC);
CREATE INDEX idx_sla_reports_link_month ON sla_reports(client_link_id, report_month DESC);
CREATE INDEX idx_notification_logs_company_time ON notification_logs(company_id, created_at DESC);

-- ============================================
-- Database Maintenance Recommendations
-- ============================================

-- 1. Run OPTIMIZE TABLE monthly on large tables:
-- OPTIMIZE TABLE link_monitoring_data;
-- OPTIMIZE TABLE notification_logs;

-- 2. Set up automated backup schedule (daily recommended)

-- 3. Monitor table sizes:
-- SELECT 
--     table_name AS 'Table',
--     ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
-- FROM information_schema.TABLES
-- WHERE table_schema = 'your_database_name'
--     AND table_name IN (
--         'client_portal_users',
--         'mikrotik_routers',
--         'client_links',
--         'link_monitoring_data',
--         'sla_reports',
--         'notification_settings',
--         'notification_logs'
--     )
-- ORDER BY (data_length + index_length) DESC;

-- 4. Set up monitoring for:
--    - Table growth rate (especially link_monitoring_data)
--    - Failed notifications (notification_logs.sent_successfully = 0)
--    - Links that haven't reported data recently
--    - SLA breach trends
