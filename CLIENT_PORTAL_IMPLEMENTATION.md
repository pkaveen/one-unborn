# Client Portal - Implementation Summary

## ✅ Completed Features

### Phase 1: Foundation (Database & API) ✅
- ✅ 5 database tables created with migrations
- ✅ 5 Eloquent models with relationships
- ✅ MikroTik RouterOS API service (10+ methods)
- ✅ Background job for metric collection
- ✅ Docker Compose monitoring stack (Prometheus, Grafana, MikroTik Exporter)
- ✅ Comprehensive setup documentation

### Phase 3: Client Portal Frontend ✅
- ✅ Separate authentication guard (`client`) for portal users
- ✅ ClientPortalController with 7 methods:
  - Login/Logout
  - Dashboard with link status cards
  - Links listing
  - Link details with 24-hour graphs
  - SLA reports with filters
  - PDF download for SLA reports
- ✅ 6 Blade templates:
  - Login page
  - Dashboard with quick stats
  - Links table view
  - Link details with Chart.js graphs
  - SLA reports table with filtering
  - Responsive layout with navigation
- ✅ Routes configured under `/client` prefix
- ✅ Grafana dashboard embedding via iframe

### Phase 4: SLA Calculation Engine ✅
- ✅ Artisan command `sla:calculate-monthly`
- ✅ Automatic uptime percentage calculation
- ✅ Average latency and packet loss metrics
- ✅ Downtime incident tracking
- ✅ SLA breach detection logic
- ✅ Detailed breach reporting
- ✅ Scheduled monthly execution (1st day, 00:30)
- ✅ Email notifications for SLA breaches
- ✅ Beautiful HTML email template

## 📁 Files Created/Modified

### New Files (26 total)
1. `app/Console/Commands/CalculateMonthlySla.php`
2. `app/Http/Controllers/ClientPortalController.php`
3. `app/Jobs/CollectLinkMetrics.php`
4. `app/Mail/SlaBreachNotification.php`
5. `app/Models/ClientLink.php`
6. `app/Models/ClientPortalUser.php`
7. `app/Models/LinkMonitoringData.php`
8. `app/Models/MikrotikRouter.php`
9. `app/Models/SlaReport.php`
10. `app/Services/MikrotikService.php`
11. `database/migrations/2025_11_30_000001_create_client_portal_users_table.php`
12. `database/migrations/2025_11_30_000002_create_mikrotik_routers_table.php`
13. `database/migrations/2025_11_30_000003_create_client_links_table.php`
14. `database/migrations/2025_11_30_000004_create_link_monitoring_data_table.php`
15. `database/migrations/2025_11_30_000005_create_sla_reports_table.php`
16. `resources/views/client_portal/dashboard.blade.php`
17. `resources/views/client_portal/layout.blade.php`
18. `resources/views/client_portal/link_details.blade.php`
19. `resources/views/client_portal/links.blade.php`
20. `resources/views/client_portal/login.blade.php`
21. `resources/views/client_portal/sla_reports.blade.php`
22. `resources/views/emails/sla_breach.blade.php`
23. `docker-compose.monitoring.yml`
24. `prometheus/prometheus.yml`
25. `mikrotik-exporter/config.yml`
26. `CLIENT_PORTAL_ARCHITECTURE.md`
27. `CLIENT_PORTAL_SETUP.md`
28. `CPANEL_DEPLOYMENT.md`

### Modified Files (5 total)
1. `config/auth.php` - Added `client` guard and provider
2. `routes/web.php` - Added client portal routes
3. `routes/console.php` - Added scheduled tasks
4. `composer.json` - Added routeros-api-php dependency
5. `app/Models/ClientLink.php` - Fixed relationships and scopes

## 🔑 Key Features

### Client Portal
- **URL**: `https://yourdomain.com/client/login`
- **Authentication**: Separate guard using `ClientPortalUser` model
- **Dashboard**: Real-time link status, traffic stats, SLA summaries
- **Link Monitoring**: 24-hour traffic and latency graphs using Chart.js
- **SLA Reports**: Monthly reports with filtering, PDF download
- **Responsive Design**: Mobile-friendly using Tailwind CSS

### MikroTik Integration
- **API Methods**:
  - `connect()` - Establish RouterOS connection
  - `getInterfaceStats()` - Interface configuration
  - `getInterfaceTraffic()` - Real-time RX/TX rates
  - `getInterfaceCounters()` - Cumulative byte/packet counters
  - `pingHost()` - Latency and packet loss measurement
  - `getSystemResources()` - CPU, memory, uptime
  - `isInterfaceUp()` - Boolean status check
  - `testConnection()` - Connection validation
- **Security**: Encrypted password storage, SSL support
- **Logging**: Comprehensive error and connection logging

### Background Jobs
- **CollectLinkMetrics**: Runs every 5 minutes
  - Connects to MikroTik routers
  - Collects interface stats, traffic, ping results
  - Stores in `link_monitoring_data` table
- **Queue**: Database queue with Laravel jobs

### SLA Calculation
- **Command**: `php artisan sla:calculate-monthly`
- **Schedule**: Monthly on 1st day at 00:30
- **Calculations**:
  - Uptime percentage based on 5-minute samples
  - Total downtime duration
  - Average latency and packet loss
  - SLA breach detection (uptime, latency, packet loss)
  - Downtime incident tracking
- **Output**: Stored in `sla_reports` table

### Notifications
- **SLA Breach Emails**: Automatically sent when SLA not met
- **Recipients**: All active client portal users for that client
- **Template**: Beautiful HTML email with:
  - Link information
  - Performance metrics vs commitments
  - Detailed breach reasons
  - Downtime incidents
  - Link to view full report

## 🚀 Deployment Steps

### 1. On cPanel Server
```bash
# Pull latest code
cd ~/public_html/one-unborn
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Configure MikroTik Routers
```routeros
# Enable API
/ip service
set api-ssl port=8729 certificate=auto disabled=no

# Create API user
/user group add name=api-readonly policy=read,api,test
/user add name=laravel-api password=SecurePass123 group=api-readonly

# Whitelist cPanel server
/ip firewall filter
add chain=input action=accept protocol=tcp dst-port=8729 src-address=CPANEL_IP
```

### 3. Add Routers to Database
```bash
php artisan tinker
```
```php
use App\Models\MikrotikRouter;

MikrotikRouter::create([
    'name' => 'Core Router 1',
    'management_ip' => '192.168.1.1',
    'api_port' => 8729,
    'api_username' => 'laravel-api',
    'api_password' => 'SecurePass123',
    'use_ssl' => true,
    'status' => 'active',
    'company_id' => 1,
]);
```

### 4. Create Client Portal Users
```php
use App\Models\ClientPortalUser;
use Illuminate\Support\Facades\Hash;

ClientPortalUser::create([
    'client_id' => 1,
    'username' => 'client_user',
    'email' => 'client@example.com',
    'password' => Hash::make('SecurePassword123'),
    'status' => 'active',
]);
```

### 5. Create Client Links
```php
use App\Models\ClientLink;

ClientLink::create([
    'deliverable_id' => 1,
    'client_id' => 1,
    'router_id' => 1,
    'interface_name' => 'ether5',
    'link_type' => 'ILL',
    'bandwidth_committed' => 100,
    'sla_uptime' => 99.5,
    'sla_latency' => 20,
    'sla_packet_loss' => 1.0,
    'status' => 'active',
    'activation_date' => now(),
]);
```

### 6. Setup Cron Jobs (via cPanel)
```bash
* * * * * cd /home/username/public_html/one-unborn && php artisan schedule:run >> /dev/null 2>&1
```

### 7. Setup Queue Worker (Supervisor)
Create `/etc/supervisor/conf.d/one-unborn-worker.conf`:
```ini
[program:one-unborn-worker]
command=/opt/cpanel/ea-php82/root/usr/bin/php /home/username/public_html/one-unborn/artisan queue:work
autostart=true
autorestart=true
user=username
numprocs=2
```

### 8. Start Monitoring Stack (Optional - if Docker available)
```bash
docker-compose -f docker-compose.monitoring.yml up -d
```

## 🧪 Testing

### Test MikroTik Connection
```bash
php artisan tinker
```
```php
use App\Services\MikrotikService;
use App\Models\MikrotikRouter;

$router = MikrotikRouter::first();
$service = new MikrotikService();
$service->testConnection($router);
```

### Test Metric Collection
```php
use App\Jobs\CollectLinkMetrics;
dispatch(new CollectLinkMetrics());
```

### Test SLA Calculation
```bash
php artisan sla:calculate-monthly --month=2025-11
```

### Access Client Portal
1. Visit: `https://yourdomain.com/client/login`
2. Login with client credentials
3. View dashboard, links, SLA reports

## 📊 Database Schema

### client_portal_users
- Separate authentication for clients
- Fields: username, email, password, client_id, status

### mikrotik_routers
- Router inventory with encrypted credentials
- Fields: name, management_ip, api_port, api_username, api_password (encrypted), use_ssl, status

### client_links
- Links clients to router interfaces
- Fields: deliverable_id, client_id, router_id, interface_name, link_type, bandwidth_committed, sla_uptime, sla_latency, sla_packet_loss, status

### link_monitoring_data
- Time-series metrics (no timestamps, uses collected_at)
- Fields: client_link_id, interface_status, rx_bytes, tx_bytes, rx_packets, tx_packets, latency_ms, packet_loss_percent, collected_at

### sla_reports
- Monthly SLA calculations
- Fields: client_link_id, report_month, uptime_percentage, total_downtime_seconds, avg_latency_ms, avg_packet_loss_percent, sla_met, breach_details (JSON), calculated_at

## 📈 Monitoring Flow

1. **CollectLinkMetrics Job** (every 5 minutes)
   ↓
2. **MikrotikService** connects to routers via API
   ↓
3. Collect interface stats, traffic, ping results
   ↓
4. Store in **link_monitoring_data** table
   ↓
5. **Client Portal** displays real-time data
   ↓
6. Monthly: **CalculateMonthlySla Command**
   ↓
7. Calculate uptime, latency, packet loss averages
   ↓
8. Detect SLA breaches
   ↓
9. Store in **sla_reports** table
   ↓
10. Send **SLA Breach Emails** if needed

## 🎯 Next Steps (Future Enhancements)

### Phase 5: Billing Integration
- [ ] Create invoices and payments tables
- [ ] Auto-generate invoices from client links
- [ ] Integrate payment gateways (Razorpay, PayU, Stripe)
- [ ] Link invoices to SLA breaches (credits/refunds)

### Additional Features
- [ ] Real-time alerts via WhatsApp for link down events
- [ ] Client portal mobile app
- [ ] Advanced analytics and reporting
- [ ] Historical data comparison
- [ ] Network topology visualization
- [ ] Bandwidth utilization forecasting
- [ ] API for third-party integrations

## 📚 Documentation Files

1. **CLIENT_PORTAL_ARCHITECTURE.md** - Complete system design
2. **CLIENT_PORTAL_SETUP.md** - Step-by-step setup instructions
3. **CPANEL_DEPLOYMENT.md** - Comprehensive cPanel deployment guide
4. **This file** - Implementation summary

## 🔒 Security Features

- Separate authentication guard for clients
- Encrypted MikroTik router passwords
- SSL support for RouterOS API connections
- CSRF protection on all forms
- Rate limiting on API endpoints
- Password hashing with bcrypt
- Session-based authentication

## ✨ UI/UX Highlights

- **Responsive Design**: Works on mobile, tablet, desktop
- **Real-time Graphs**: Chart.js for traffic and latency visualization
- **Quick Stats Cards**: Dashboard overview with color-coded status
- **SLA Status Indicators**: Green (Met) / Red (Breach) badges
- **Filtering**: Month and link filters on SLA reports
- **PDF Export**: Downloadable SLA reports
- **Grafana Embedding**: iframe integration for advanced dashboards
- **Clean Layout**: Tailwind CSS utility classes

---

## 🎉 Project Status: READY FOR DEPLOYMENT

All client portal features (Phases 1, 3, 4) are complete and committed to the repository. The system is ready for deployment to cPanel server.

**Latest Commits:**
- `f338f91` - docs(deployment): add comprehensive cPanel deployment guide
- `5b7f4f3` - feat(client-portal): add database schema, MikroTik service, and monitoring stack
- `5825720` - docs(client-portal): add comprehensive architecture for ILL link monitoring
- `3987a6b` - feat(client-portal): add authentication, views, SLA calculation and notifications

**Repository:** https://github.com/pkaveen/one-unborn
**Branch:** main
