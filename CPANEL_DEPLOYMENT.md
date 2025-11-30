# cPanel Deployment Guide - Client Portal

Complete deployment checklist for moving the One Unborn application with Client Portal features to cPanel.

## Pre-Deployment Checklist

- [ ] Git repository pushed to GitHub (pkaveen/one-unborn)
- [ ] cPanel credentials and SSH access ready
- [ ] MySQL database created on cPanel
- [ ] Domain/subdomain configured in cPanel

## Step 1: Initial cPanel Setup

### 1.1 Create Database via cPanel MySQL Databases

1. Login to cPanel
2. Go to **MySQL Databases**
3. Create new database: `username_oneunborn`
4. Create database user: `username_oneunborn_user`
5. Grant ALL PRIVILEGES to user
6. Note down: Database name, username, password

### 1.2 SSH into Server

```bash
ssh username@yourdomain.com
```

## Step 2: Deploy Application

### 2.1 Clone Repository

```bash
cd ~/public_html
git clone https://github.com/pkaveen/one-unborn.git
cd one-unborn
```

Or if already cloned, pull latest changes:
```bash
cd ~/public_html/one-unborn
git pull origin main
```

### 2.2 Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# If composer not in PATH, use full path
/usr/local/bin/composer install --no-dev --optimize-autoloader

# Or use EA PHP (common on cPanel)
/opt/cpanel/ea-php82/root/usr/bin/php /usr/local/bin/composer install --no-dev --optimize-autoloader
```

### 2.3 Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Edit environment file
nano .env
```

Update these values in `.env`:

```env
APP_NAME="One Unborn"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_oneunborn
DB_USERNAME=username_oneunborn_user
DB_PASSWORD=your_database_password

QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your_email@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 2.4 Generate Application Key

```bash
php artisan key:generate
```

### 2.5 Set Permissions

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R username:username storage bootstrap/cache
```

## Step 3: Database Setup

### 3.1 Run Migrations

```bash
php artisan migrate --force
```

This creates all tables including:
- Client Portal tables (client_portal_users, mikrotik_routers, client_links, link_monitoring_data, sla_reports)
- Core application tables

### 3.2 Seed Initial Data

```bash
php artisan db:seed --force
```

Or seed specific seeders:
```bash
php artisan db:seed --class=MenuSeeder --force
php artisan db:seed --class=UserTypeSeeder --force
```

### 3.3 Create Queue Jobs Table

```bash
php artisan queue:table
php artisan migrate --force
```

## Step 4: Setup Cron Jobs

### 4.1 Via cPanel Cron Jobs Interface

1. Go to cPanel → **Cron Jobs**
2. Add new cron job:

**Command:**
```bash
cd /home/username/public_html/one-unborn && /opt/cpanel/ea-php82/root/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

**Timing:** Every Minute (* * * * *)

### 4.2 Via SSH (Alternative)

```bash
crontab -e
```

Add this line:
```bash
* * * * * cd /home/username/public_html/one-unborn && /opt/cpanel/ea-php82/root/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

## Step 5: Setup Queue Worker (Background Jobs)

### 5.1 Install Supervisor (if available)

Create supervisor config:
```bash
nano /etc/supervisor/conf.d/one-unborn-worker.conf
```

```ini
[program:one-unborn-worker]
process_name=%(program_name)s_%(process_num)02d
command=/opt/cpanel/ea-php82/root/usr/bin/php /home/username/public_html/one-unborn/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=username
numprocs=2
redirect_stderr=true
stdout_logfile=/home/username/public_html/one-unborn/storage/logs/worker.log
stopwaitsecs=3600
```

Restart supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start one-unborn-worker:*
```

### 5.2 Alternative: Cron-based Queue Processing

If Supervisor not available, add to crontab:
```bash
* * * * * cd /home/username/public_html/one-unborn && /opt/cpanel/ea-php82/root/usr/bin/php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

## Step 6: MikroTik Router Configuration

### 6.1 Add Routers via Tinker

```bash
php artisan tinker
```

```php
use App\Models\MikrotikRouter;
use App\Models\Company;

$company = Company::first();

MikrotikRouter::create([
    'name' => 'Core Router - Mumbai',
    'management_ip' => '192.168.1.1',
    'api_port' => 8729,
    'api_username' => 'laravel-api',
    'api_password' => 'YourStrongPassword123', // Auto-encrypted
    'use_ssl' => true,
    'status' => 'active',
    'company_id' => $company->id,
    'location' => 'Mumbai Data Center',
    'notes' => 'Main core router for ILL links',
]);

// Add more routers as needed
```

### 6.2 Configure MikroTik API on Routers

Connect to each MikroTik router:

```routeros
# Enable API
/ip service
set api port=8728 disabled=no
set api-ssl port=8729 certificate=auto disabled=no

# Create API user group
/user group add name=api-readonly policy=read,api,test,!local,!ssh,!write

# Create API user
/user add name=laravel-api password=YourStrongPassword123 group=api-readonly

# Whitelist cPanel server IP
/ip firewall filter
add chain=input action=accept protocol=tcp dst-port=8729 src-address=YOUR_CPANEL_SERVER_IP comment="Laravel API Access"
```

### 6.3 Test Router Connection

```bash
php artisan tinker
```

```php
use App\Services\MikrotikService;
use App\Models\MikrotikRouter;

$router = MikrotikRouter::first();
$service = new MikrotikService();

// Test connection
$result = $service->testConnection($router);
print_r($result);

// Get interface list
$interfaces = $service->getAllInterfaces($router);
print_r($interfaces);

// Test ping
$ping = $service->pingHost($router, '8.8.8.8', 5);
print_r($ping);
```

## Step 7: Create Client Portal Users

### 7.1 Create Client Portal User

```bash
php artisan tinker
```

```php
use App\Models\ClientPortalUser;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

$client = Client::where('company_name', 'LIKE', '%Client Name%')->first();

ClientPortalUser::create([
    'client_id' => $client->id,
    'username' => 'client_user',
    'email' => 'client@example.com',
    'password' => Hash::make('SecurePassword123'),
    'status' => 'active',
]);
```

## Step 8: Link Client to MikroTik Interfaces

### 8.1 Create Client Links

```bash
php artisan tinker
```

```php
use App\Models\ClientLink;
use App\Models\Deliverables;
use App\Models\Client;
use App\Models\MikrotikRouter;

$deliverable = Deliverables::where('client_id', $client->id)->first();
$router = MikrotikRouter::where('name', 'Core Router - Mumbai')->first();

ClientLink::create([
    'deliverable_id' => $deliverable->id,
    'client_id' => $client->id,
    'router_id' => $router->id,
    'interface_name' => 'ether5', // Actual interface on MikroTik
    'link_type' => 'ILL',
    'bandwidth_committed' => 100, // Mbps
    'sla_uptime' => 99.5, // 99.5% SLA
    'sla_latency' => 20, // 20ms max latency
    'sla_packet_loss' => 1.0, // 1% max packet loss
    'status' => 'active',
    'activation_date' => now(),
]);
```

## Step 9: Configure Monitoring (Optional - Requires Docker)

### 9.1 Check Docker Availability

```bash
docker --version
docker-compose --version
```

If Docker is available on server:

```bash
cd ~/public_html/one-unborn

# Update mikrotik-exporter/config.yml with your router IPs
nano mikrotik-exporter/config.yml

# Start monitoring stack
docker-compose -f docker-compose.monitoring.yml up -d
```

Access points:
- Prometheus: `http://yourserver.com:9090`
- Grafana: `http://yourserver.com:3000` (admin/admin)

### 9.2 If Docker Not Available

Skip Prometheus/Grafana setup. The system will work using direct MikroTik API polling via Laravel jobs.

## Step 10: Testing

### 10.1 Test Application

Visit: `https://yourdomain.com/login`

### 10.2 Test Metric Collection Job

```bash
php artisan tinker
```

```php
use App\Jobs\CollectLinkMetrics;

dispatch(new CollectLinkMetrics());
```

Check logs:
```bash
tail -f storage/logs/laravel.log
```

### 10.3 Verify Data Collection

```bash
php artisan tinker
```

```php
use App\Models\LinkMonitoringData;

$data = LinkMonitoringData::orderBy('collected_at', 'desc')->take(10)->get();
print_r($data->toArray());
```

## Step 11: Security Hardening

### 11.1 Disable Directory Listing

Add to `.htaccess` in public directory:
```apache
Options -Indexes
```

### 11.2 Secure Environment File

```bash
chmod 600 .env
```

### 11.3 Setup SSL Certificate

Via cPanel:
1. Go to **SSL/TLS Status**
2. Enable AutoSSL for domain
3. Or install Let's Encrypt certificate

### 11.4 Configure Document Root

Point domain to `public_html/one-unborn/public` directory:
1. cPanel → **Domains**
2. Edit domain
3. Set Document Root: `/home/username/public_html/one-unborn/public`

## Step 12: Post-Deployment

### 12.1 Clear Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 12.2 Monitor Logs

```bash
tail -f storage/logs/laravel.log
tail -f storage/logs/worker.log
```

### 12.3 Setup Log Rotation

Create `/etc/logrotate.d/one-unborn`:
```
/home/username/public_html/one-unborn/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 username username
    sharedscripts
}
```

## Troubleshooting

### Issue: Composer not found
**Solution:** Use full path `/usr/local/bin/composer` or EA-PHP path

### Issue: PHP version mismatch
**Solution:** Use `ea-php82` or `php82` command explicitly

### Issue: Permission denied errors
**Solution:** 
```bash
chmod -R 775 storage bootstrap/cache
chown -R username:username storage bootstrap/cache
```

### Issue: MikroTik connection timeout
**Solution:**
- Verify cPanel server IP is whitelisted on MikroTik firewall
- Check if API port (8729) is accessible: `telnet 192.168.1.1 8729`
- Verify credentials in database

### Issue: Queue jobs not processing
**Solution:**
- Check if queue:work is running: `ps aux | grep queue:work`
- Verify supervisor configuration
- Check database queue: `SELECT * FROM jobs;`
- Run manually: `php artisan queue:work --once`

### Issue: Storage disk full
**Solution:**
- Clean old logs: `php artisan log:clear`
- Truncate old monitoring data:
```sql
DELETE FROM link_monitoring_data WHERE collected_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

## Maintenance Commands

```bash
# Update application
cd ~/public_html/one-unborn
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
supervisorctl restart one-unborn-worker:*

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Database backup
mysqldump -u username_oneunborn_user -p username_oneunborn > backup_$(date +%Y%m%d).sql

# Check disk usage
du -sh storage/logs/
du -sh storage/app/
```

## Support

- Check logs: `storage/logs/laravel.log`
- Test queue: `php artisan queue:work --once`
- Test router: Use MikrotikService::testConnection()
- Verify cron: `crontab -l`
- Check supervisor: `supervisorctl status`
