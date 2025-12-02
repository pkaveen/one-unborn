# Client Portal - Team Handoff Document
**Date:** December 2, 2025  
**Project:** One Unborn - Client Portal with MikroTik Monitoring

---

## ✅ Completed Work

### 1. **Database Schema** 
- Added portal authentication to `clients` table
- Created 7 new tables: `mikrotik_routers`, `client_links`, `link_monitoring_data`, `sla_reports`, `notification_settings`, `notification_logs`
- Migration files in `database/migrations/2025_11_30_*`
- Import-ready SQL: `client_portal_install.sql`

### 2. **Backend Services**
- **MikrotikService** (`app/Services/MikrotikService.php`) - Full RouterOS API integration
  - Interface monitoring, traffic stats, ping tests
  - 10+ API methods for link management
- **NotificationService** (`app/Services/NotificationService.php`) - Admin-controlled alerts
  - SLA breach notifications
  - Real-time link down alerts
  - High latency/packet loss detection
  - Email + WhatsApp integration
  - Cooldown system to prevent spam

### 3. **Models**
- `Client` (updated) - Now extends Authenticatable for portal login
- `MikrotikRouter` - Router inventory with encrypted credentials
- `ClientLink` - Links clients to router interfaces with SLA commitments
- `LinkMonitoringData` - Time-series metrics (5-min intervals)
- `SlaReport` - Monthly SLA calculations
- `NotificationSetting` - Per-company notification config
- `NotificationLog` - Audit trail for all notifications

### 4. **Controllers**
- `ClientPortalController` - Client-facing portal with 7 methods
- `NotificationSettingsController` - Admin interface for notification management

### 5. **Views (Blade Templates)**
- Client Portal:
  - `login.blade.php` - Client authentication
  - `dashboard.blade.php` - Overview with Chart.js graphs
  - `links.blade.php` - All client links listing
  - `link_details.blade.php` - Detailed metrics per link
  - `sla_reports.blade.php` - Monthly SLA report viewer
- Admin:
  - `notification_settings/index.blade.php` - Notification configuration UI

### 6. **Background Jobs & Commands**
- `CollectLinkMetrics` job - Runs every 5 minutes, collects MikroTik data
- `CalculateMonthlySla` command - Monthly SLA report generation
- Scheduled in Laravel's task scheduler

### 7. **Authentication**
- Separate `client` guard configured in `config/auth.php`
- Uses `clients` table with `portal_username`, `portal_password`, `portal_active`
- Routes protected by client auth middleware

### 8. **Monitoring Stack (Docker)**
- Prometheus + Grafana configuration
- MikroTik exporter setup
- `docker-compose.yml` and configs in `mikrotik-exporter/`

### 9. **Documentation**
- `CLIENT_PORTAL_ARCHITECTURE.md` (444 lines) - System architecture
- `CLIENT_PORTAL_IMPLEMENTATION.md` (374 lines) - Implementation guide
- `CPANEL_DEPLOYMENT.md` (535 lines) - Deployment instructions
- `CLIENT_PORTAL_DATABASE_SCHEMA.sql` - Complete schema with queries
- `client_portal_install.sql` - phpMyAdmin import script

---

## ⏳ Pending Work (For Team)

### 1. **Email Templates** (HIGH PRIORITY)
Create Blade views for notification emails:
- `resources/views/emails/alerts/link_down.blade.php`
- `resources/views/emails/alerts/high_latency.blade.php`
- `resources/views/emails/alerts/high_packet_loss.blade.php`

**Reference:** Check `app/Mail/LinkDownAlert.php`, `HighLatencyAlert.php`, `HighPacketLossAlert.php` for required data structure.

### 2. **Notification Logs View**
Create `resources/views/notification_settings/logs.blade.php`
- Paginated view (50 per page)
- Filters by type, date range, success/failure
- Export to CSV functionality
- Route already exists: `/notification-settings/logs`

### 3. **Client Portal Features**
- [ ] Password reset functionality for clients
- [ ] Client profile page (view/edit support contact info)
- [ ] Download SLA reports as PDF
- [ ] Real-time dashboard updates (consider WebSockets/Pusher)

### 4. **Admin Features**
- [ ] MikroTik router CRUD interface (add/edit/delete routers)
- [ ] Client link management UI (assign links to clients)
- [ ] Bulk link configuration import (CSV/Excel)
- [ ] Router connection testing tool

### 5. **Testing**
- [ ] Write PHPUnit tests for:
  - MikrotikService API methods
  - NotificationService alert logic
  - SLA calculation accuracy
- [ ] Test client portal authentication flow
- [ ] Test notification delivery (email/WhatsApp)

### 6. **Deployment**
- [ ] Run migrations on production: `php artisan migrate`
- [ ] Seed notification settings: `php artisan db:seed --class=MenuSeeder`
- [ ] Configure queue worker: `php artisan queue:work`
- [ ] Set up Laravel scheduler cron job
- [ ] Configure email SMTP settings in `.env`
- [ ] Add MikroTik router credentials

### 7. **Monitoring & Maintenance**
- [ ] Set up Prometheus data retention policies
- [ ] Configure Grafana dashboards
- [ ] Set up log rotation for `link_monitoring_data` table
- [ ] Implement data archival (keep 90 days, archive older)

---

## 📋 Quick Start for Team

### 1. Clone & Setup
```bash
git clone https://github.com/pkaveen/one-unborn.git
cd one-unborn
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### 2. Database Setup
```bash
# Import SQL in phpMyAdmin or via CLI
mysql -u root -p your_database < client_portal_install.sql

# Or run migrations
php artisan migrate
php artisan db:seed --class=MenuSeeder
```

### 3. Run Development Environment
```bash
# Start all services
composer dev

# Or manually:
php artisan serve           # Web server
php artisan queue:listen    # Queue worker
npm run dev                 # Vite
```

### 4. Test Client Portal
- Create a client with portal credentials:
  ```sql
  UPDATE clients 
  SET portal_username = 'testclient', 
      portal_password = '$2y$12$...', -- bcrypt hash
      portal_active = 1 
  WHERE id = 1;
  ```
- Login at: `/client-portal/login`

### 5. Configure Notifications
- Login as superadmin/admin
- Navigate to Settings → Notification Settings
- Configure thresholds, recipients, enable/disable alerts

---

## 🔑 Important Notes

### Security
- All MikroTik API passwords are encrypted in database
- Client portal passwords use bcrypt hashing
- CSRF protection on all forms
- Multi-company access control via middleware

### Performance
- Background jobs prevent blocking main requests
- Indexes on all foreign keys and frequently queried columns
- Consider Redis for queue driver in production

### API Rate Limits
- MikroTik API calls limited by router capacity
- 5-minute collection interval prevents overload
- Implement retry logic with exponential backoff

### Data Growth
- `link_monitoring_data` table grows ~288 rows/day per link
- Plan archival strategy (recommended: 90-day retention)
- Monitor table sizes monthly

---

## 📞 Questions?

**Code Location:**
- Backend: `app/Services/`, `app/Models/`, `app/Http/Controllers/`
- Frontend: `resources/views/client_portal/`, `resources/views/notification_settings/`
- Database: `database/migrations/2025_11_30_*`
- Docs: `CLIENT_PORTAL_*.md` files in root

**Key Dependencies:**
- `evilfreelancer/routeros-api-php` - MikroTik RouterOS API
- Laravel 12 (PHP 8.2+)
- Chart.js (frontend graphs)
- Tailwind CSS + Bootstrap 5

**Git Commits:**
- All work committed with conventional commit messages
- Latest: `db1e23d` (phpMyAdmin import script)
- Full history: `git log --oneline`

---

## ✨ Next Steps Priority

1. **Create email template views** (blocks notifications from sending)
2. **Deploy to production** (test with 1-2 routers first)
3. **Build admin router management UI** (allow adding routers via web interface)
4. **Write tests** (ensure SLA calculations are accurate)
5. **Add PDF export** (for SLA reports)

Good luck team! 🚀
