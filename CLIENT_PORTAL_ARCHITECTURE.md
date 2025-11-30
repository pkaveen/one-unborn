# ILL Link Client Portal - Architecture & Implementation Plan

## Project Overview
Build a comprehensive client portal for ILL (Internet Leased Line) customers to monitor link availability, SLA compliance, traffic statistics, and billing details using MikroTik Router API integration.

---

## System Architecture

### High-Level Components

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT PORTAL                             │
│  (Laravel Web Interface - Client Login)                         │
│  - Dashboard: Real-time Link Status                             │
│  - SLA Reports: Uptime, Packet Loss, Latency                   │
│  - Traffic Graphs: Live bandwidth utilization                   │
│  - Billing: Invoices, Payment History (future)                  │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ↓
┌─────────────────────────────────────────────────────────────────┐
│                   LARAVEL BACKEND SERVICES                       │
│  - MikroTik API Client (PHP)                                    │
│  - Data Collection Service (Queue Jobs)                         │
│  - SLA Calculation Engine                                       │
│  - Notification System (Email/WhatsApp)                         │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ↓
┌─────────────────────────────────────────────────────────────────┐
│                    TIME-SERIES DATABASE                          │
│  Option A: MySQL (for basic metrics storage)                    │
│  Option B: InfluxDB (optimized for time-series data)           │
│  Option C: Prometheus + MySQL hybrid                            │
│  - Stores: Traffic stats, uptime data, latency metrics         │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ↓ (Polling every 1-5 minutes)
┌─────────────────────────────────────────────────────────────────┐
│                     MIKROTIK ROUTERS                             │
│  Router 1 (Management IP: 192.168.1.1)                         │
│  Router 2 (Management IP: 192.168.1.2)                         │
│  ...                                                             │
│  - Exposed via RouterOS API (Port 8728 or 8729 SSL)            │
│  - Metrics: Interface stats, ping results, bandwidth           │
└─────────────────────────────────────────────────────────────────┘
```

---

## Technology Stack

### Monitoring Tools Comparison

| Tool | Purpose | Pros | Cons | Recommendation |
|------|---------|------|------|----------------|
| **RRDTool** | Round-robin database for time-series data | Lightweight, proven, disk-efficient | Requires manual graphing, no native API | ✅ Use for long-term storage |
| **MRTG** | Traffic graphing (built on RRDTool) | Simple, auto-generates graphs | Limited to SNMP, outdated UI | ❌ Skip (legacy) |
| **Prometheus** | Modern monitoring system | Powerful querying (PromQL), scalable | Complex setup, resource-intensive | ✅ Recommended for production |
| **Grafana** | Visualization layer | Beautiful dashboards, supports multiple data sources | Requires separate backend | ✅ Use for client dashboards |

### Chosen Architecture: **Prometheus + Grafana + Laravel**

**Rationale:**
- **Prometheus**: Collects metrics from MikroTik routers via custom exporter
- **Grafana**: Provides interactive, embeddable dashboards for clients
- **Laravel**: Manages users, SLA logic, billing, and integrates Grafana iframes

---

## Database Schema

### New Tables

#### 1. `client_portal_users` (Client Login Credentials)
```sql
CREATE TABLE `client_portal_users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `client_id` BIGINT UNSIGNED NOT NULL,
    `username` VARCHAR(255) UNIQUE NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `last_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE,
    INDEX (`client_id`),
    INDEX (`username`),
    INDEX (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 2. `mikrotik_routers` (Router Management)
```sql
CREATE TABLE `mikrotik_routers` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `management_ip` VARCHAR(45) NOT NULL,
    `api_port` INT DEFAULT 8728,
    `api_username` VARCHAR(255) NOT NULL,
    `api_password` VARCHAR(255) NOT NULL,
    `use_ssl` BOOLEAN DEFAULT FALSE,
    `location` VARCHAR(255) NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `last_poll` TIMESTAMP NULL,
    `status` ENUM('online', 'offline', 'unreachable') DEFAULT 'offline',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (`management_ip`),
    INDEX (`is_active`),
    INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3. `client_links` (Links assigned to clients)
```sql
CREATE TABLE `client_links` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `deliverable_id` BIGINT UNSIGNED NOT NULL,
    `client_id` BIGINT UNSIGNED NOT NULL,
    `router_id` BIGINT UNSIGNED NOT NULL,
    `interface_name` VARCHAR(255) NOT NULL COMMENT 'e.g., ether1, pppoe-out1',
    `circuit_id` VARCHAR(255) NULL,
    `link_name` VARCHAR(255) NOT NULL,
    `committed_speed_mbps` INT NOT NULL,
    `committed_sla_uptime` DECIMAL(5,2) DEFAULT 99.50 COMMENT 'e.g., 99.50%',
    `committed_sla_latency_ms` INT DEFAULT 50,
    `committed_sla_packet_loss` DECIMAL(5,2) DEFAULT 1.00 COMMENT 'e.g., 1.00%',
    `activation_date` DATE NOT NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `grafana_dashboard_uid` VARCHAR(255) NULL COMMENT 'Grafana dashboard UID for iframe',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`deliverable_id`) REFERENCES `deliverables`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`router_id`) REFERENCES `mikrotik_routers`(`id`) ON DELETE CASCADE,
    INDEX (`client_id`),
    INDEX (`router_id`),
    INDEX (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 4. `link_monitoring_data` (Time-series metrics - optional if using MySQL)
```sql
CREATE TABLE `link_monitoring_data` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `link_id` BIGINT UNSIGNED NOT NULL,
    `timestamp` TIMESTAMP NOT NULL,
    `rx_bytes` BIGINT UNSIGNED DEFAULT 0,
    `tx_bytes` BIGINT UNSIGNED DEFAULT 0,
    `rx_packets` BIGINT UNSIGNED DEFAULT 0,
    `tx_packets` BIGINT UNSIGNED DEFAULT 0,
    `rx_errors` BIGINT UNSIGNED DEFAULT 0,
    `tx_errors` BIGINT UNSIGNED DEFAULT 0,
    `latency_ms` DECIMAL(10,2) NULL,
    `packet_loss_percent` DECIMAL(5,2) NULL,
    `link_status` ENUM('up', 'down') DEFAULT 'up',
    FOREIGN KEY (`link_id`) REFERENCES `client_links`(`id`) ON DELETE CASCADE,
    INDEX (`link_id`, `timestamp`),
    INDEX (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Partition by month for performance
ALTER TABLE `link_monitoring_data` 
PARTITION BY RANGE (UNIX_TIMESTAMP(`timestamp`)) (
    PARTITION p202411 VALUES LESS THAN (UNIX_TIMESTAMP('2024-12-01')),
    PARTITION p202412 VALUES LESS THAN (UNIX_TIMESTAMP('2025-01-01')),
    PARTITION p202501 VALUES LESS THAN (UNIX_TIMESTAMP('2025-02-01')),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

#### 5. `sla_reports` (Monthly SLA calculations)
```sql
CREATE TABLE `sla_reports` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `link_id` BIGINT UNSIGNED NOT NULL,
    `report_month` DATE NOT NULL COMMENT 'First day of month',
    `total_minutes` INT NOT NULL COMMENT 'Total minutes in month',
    `uptime_minutes` INT NOT NULL,
    `downtime_minutes` INT NOT NULL,
    `uptime_percentage` DECIMAL(5,2) NOT NULL,
    `avg_latency_ms` DECIMAL(10,2) NULL,
    `avg_packet_loss` DECIMAL(5,2) NULL,
    `sla_met` BOOLEAN NOT NULL,
    `sla_breach_details` TEXT NULL COMMENT 'JSON: reasons for breach',
    `generated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`link_id`) REFERENCES `client_links`(`id`) ON DELETE CASCADE,
    UNIQUE KEY (`link_id`, `report_month`),
    INDEX (`report_month`),
    INDEX (`sla_met`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Implementation Phases

### Phase 1: Foundation (Week 1-2)
**Goal:** Basic infrastructure setup

✅ **Tasks:**
1. Install Prometheus on server (Docker or native)
2. Install MikroTik Exporter for Prometheus:
   - Use: https://github.com/nshttpd/mikrotik-exporter (Go-based)
3. Create Laravel migrations for new tables
4. Install MikroTik PHP API library: `composer require routeros-api-php/routeros-api`
5. Create `MikroTikService` class in Laravel for API calls

**Deliverables:**
- Prometheus scraping metrics from 1 test MikroTik router
- Laravel can authenticate and query MikroTik API

---

### Phase 2: Data Collection (Week 3)
**Goal:** Automated metric collection

✅ **Tasks:**
1. Create Laravel queue job: `CollectRouterMetrics`
   - Polls MikroTik routers every 5 minutes
   - Collects: interface stats, ping results, CPU/memory
2. Store raw data in `link_monitoring_data` table (optional) OR rely on Prometheus
3. Create cron job: `php artisan schedule:run` with job dispatching

**Deliverables:**
- Metrics flowing into Prometheus
- Laravel job logs show successful polling

---

### Phase 3: Client Portal Frontend (Week 4)
**Goal:** Client login and dashboard

✅ **Tasks:**
1. Create client authentication guard (separate from internal users)
2. Build client portal routes:
   - `/client/login`
   - `/client/dashboard`
   - `/client/links`
   - `/client/sla-reports`
3. Install Grafana and create dashboards:
   - Dashboard per client showing all their links
   - Metrics: Bandwidth usage, uptime, latency graphs
4. Embed Grafana iframe in Laravel views with authentication

**Deliverables:**
- Clients can log in and see their assigned links
- Real-time traffic graphs visible via Grafana

---

### Phase 4: SLA Engine (Week 5)
**Goal:** Automated SLA calculation and reporting

✅ **Tasks:**
1. Create command: `php artisan sla:calculate-monthly`
   - Runs on 1st of each month
   - Queries Prometheus for uptime data
   - Calculates packet loss, latency averages
   - Inserts into `sla_reports` table
2. Generate PDF reports using `barryvdh/laravel-dompdf`
3. Email SLA reports to clients automatically
4. Add SLA breach notifications (Email/WhatsApp)

**Deliverables:**
- Monthly SLA reports auto-generated
- Clients receive PDF via email

---

### Phase 5: Billing Integration (Future)
**Goal:** Payment tracking and invoice generation

✅ **Tasks:**
1. Create `invoices` and `payments` tables
2. Link invoices to `client_links`
3. Integrate payment gateways (Razorpay, PayU, Stripe)
4. Auto-generate invoices based on billing cycle

---

## MikroTik API Integration Details

### Required API Access
Enable API on MikroTik routers:
```
/ip service
set api port=8728 disabled=no
set api-ssl port=8729 certificate=auto disabled=no
```

Create API user with limited permissions:
```
/user group add name=api-readonly policy=read,api,!local,!ssh
/user add name=laravel-api password=StrongPassword123 group=api-readonly
```

### PHP Code Example (MikroTikService.php)

```php
<?php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;
use Illuminate\Support\Facades\Log;

class MikroTikService
{
    public function connect($ip, $username, $password, $port = 8728, $ssl = false)
    {
        try {
            $config = (new \RouterOS\Config())
                ->set('host', $ip)
                ->set('user', $username)
                ->set('pass', $password)
                ->set('port', $port);

            if ($ssl) {
                $config->set('ssl', true);
            }

            return new Client($config);
        } catch (\Exception $e) {
            Log::error("MikroTik connection failed: {$ip}", ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function getInterfaceStats($client, $interfaceName)
    {
        $query = (new Query('/interface/print'))
            ->where('name', $interfaceName);

        $response = $client->query($query)->read();
        return $response[0] ?? null;
    }

    public function getInterfaceTraffic($client, $interfaceName)
    {
        $query = (new Query('/interface/monitor-traffic'))
            ->equal('interface', $interfaceName)
            ->equal('once', '');

        $response = $client->query($query)->read();
        return $response[0] ?? null;
    }

    public function pingHost($client, $address, $count = 5)
    {
        $query = (new Query('/ping'))
            ->equal('address', $address)
            ->equal('count', $count);

        $response = $client->query($query)->read();
        
        $totalRtt = 0;
        $received = 0;
        foreach ($response as $ping) {
            if (isset($ping['time'])) {
                $totalRtt += intval(str_replace('ms', '', $ping['time']));
                $received++;
            }
        }

        return [
            'sent' => $count,
            'received' => $received,
            'packet_loss' => (($count - $received) / $count) * 100,
            'avg_latency' => $received > 0 ? $totalRtt / $received : null,
        ];
    }
}
```

---

## Prometheus Configuration

### MikroTik Exporter Setup
Using https://github.com/nshttpd/mikrotik-exporter

```yaml
# prometheus.yml
global:
  scrape_interval: 1m

scrape_configs:
  - job_name: 'mikrotik'
    static_configs:
      - targets:
          - router1.example.com:9436
          - router2.example.com:9436
    relabel_configs:
      - source_labels: [__address__]
        target_label: instance
```

### Grafana Dashboard JSON
Create dashboard with panels:
- **Bandwidth**: `rate(mikrotik_interface_rx_bytes[5m])`
- **Packet Loss**: `mikrotik_ping_packet_loss`
- **Uptime**: `(time() - mikrotik_system_uptime) / 86400`

---

## Security Considerations

1. **API Credentials**: Store encrypted in database (`encrypt()`/`decrypt()`)
2. **Client Portal**: Separate authentication guard, 2FA optional
3. **Grafana Access**: Use JWT or API keys, restrict embed domains
4. **MikroTik Firewall**: Allow API only from Laravel server IP
5. **SSL**: Use API-SSL (port 8729) for MikroTik connections

---

## Cost Estimation (Self-Hosted)

| Component | Cost | Notes |
|-----------|------|-------|
| Prometheus | Free | Open-source, ~2GB RAM |
| Grafana | Free | Open-source, ~1GB RAM |
| MikroTik Exporter | Free | Open-source, ~500MB RAM |
| Additional Server Resources | $20-50/month | VPS upgrade if needed |

**Total:** $0-50/month (depends on existing infrastructure)

---

## Next Steps

1. **Approve architecture**: Review and confirm approach
2. **Provision server**: Ensure Docker or native install capability
3. **Router access**: Provide 1-2 test MikroTik router IPs + credentials
4. **Start Phase 1**: Install Prometheus + Exporter + Laravel migrations

Would you like me to:
1. Start creating the Laravel migrations and models now?
2. Provide Docker Compose file for Prometheus + Grafana stack?
3. Build the MikroTikService class with full API methods?
