# Client Portal Setup Instructions

## Prerequisites
- Docker and Docker Compose installed on server
- MikroTik routers with API enabled
- Composer installed for PHP dependencies

## Step 1: Install PHP Dependencies

```bash
cd /path/to/one-unborn
composer install
composer require evilfreelancer/routeros-api-php
```

## Step 2: Run Database Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `client_portal_users`
- `mikrotik_routers`
- `client_links`
- `link_monitoring_data`
- `sla_reports`

## Step 3: Configure MikroTik Routers

On each MikroTik router, enable API and create a user:

```
/ip service
set api port=8728 disabled=no
set api-ssl port=8729 certificate=auto disabled=no

/user group add name=api-readonly policy=read,api,!local,!ssh
/user add name=laravel-api password=YourStrongPassword123 group=api-readonly
```

## Step 4: Add Routers to Database

Use Laravel tinker or create a seeder:

```bash
php artisan tinker
```

```php
use App\Models\MikrotikRouter;

MikrotikRouter::create([
    'name' => 'Router 1 - Mumbai',
    'management_ip' => '192.168.1.1',
    'api_port' => 8728,
    'api_username' => 'laravel-api',
    'api_password' => 'YourStrongPassword123',
    'use_ssl' => false,
    'location' => 'Mumbai Data Center',
    'is_active' => true,
]);
```

## Step 5: Start Monitoring Stack (Prometheus + Grafana)

Edit `mikrotik-exporter/config.yml` with your router IPs and credentials, then:

```bash
docker-compose -f docker-compose.monitoring.yml up -d
```

Access:
- **Prometheus**: http://localhost:9090
- **Grafana**: http://localhost:3000 (admin/change_this_password)

## Step 6: Configure Grafana

1. Login to Grafana: http://localhost:3000
2. Add Prometheus data source:
   - URL: `http://prometheus:9090`
   - Save & Test
3. Import MikroTik dashboard:
   - Dashboard ID: 12055 (from grafana.com)
   - Or create custom dashboard

## Step 7: Create Client Links

Link deliverables to MikroTik interfaces:

```php
use App\Models\ClientLink;

ClientLink::create([
    'deliverable_id' => 1, // From deliverables table
    'client_id' => 5, // From clients table
    'router_id' => 1, // From mikrotik_routers table
    'interface_name' => 'ether1', // Interface name on router
    'circuit_id' => 'CKT12345',
    'link_name' => 'Client A - Primary Link',
    'committed_speed_mbps' => 100,
    'committed_sla_uptime' => 99.50,
    'committed_sla_latency_ms' => 50,
    'committed_sla_packet_loss' => 1.00,
    'activation_date' => now(),
    'is_active' => true,
]);
```

## Step 8: Schedule Monitoring Jobs

Edit `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    // Collect metrics every 5 minutes
    $schedule->job(new \App\Jobs\CollectLinkMetrics())
        ->everyFiveMinutes()
        ->withoutOverlapping();
    
    // Calculate SLA reports on 1st of each month
    $schedule->command('sla:calculate-monthly')
        ->monthlyOn(1, '02:00');
}
```

Start the scheduler:
```bash
php artisan schedule:work
```

Or add to crontab:
```
* * * * * cd /path/to/one-unborn && php artisan schedule:run >> /dev/null 2>&1
```

## Step 9: Test MikroTik Connection

```bash
php artisan tinker
```

```php
use App\Services\MikrotikService;
use App\Models\MikrotikRouter;

$service = new MikrotikService();
$router = MikrotikRouter::first();
$connected = $service->testConnection($router);

if ($connected) {
    echo "✅ Connected successfully!\n";
} else {
    echo "❌ Connection failed!\n";
}
```

## Step 10: Manual Metric Collection Test

```bash
php artisan tinker
```

```php
use App\Jobs\CollectLinkMetrics;

// Collect metrics for all links
CollectLinkMetrics::dispatch();

// Or for specific link
CollectLinkMetrics::dispatch(1); // link_id = 1
```

## Next Steps

1. Create client portal authentication
2. Build client dashboard views
3. Embed Grafana dashboards
4. Implement SLA calculation command
5. Set up email notifications

## Troubleshooting

### Can't connect to MikroTik:
- Check firewall rules (allow port 8728 from Laravel server)
- Verify API user credentials
- Test with `telnet <router_ip> 8728`

### Prometheus not scraping:
- Check `mikrotik-exporter/config.yml` syntax
- Verify router credentials in exporter config
- Check exporter logs: `docker logs mikrotik-exporter`

### Job not running:
- Ensure queue worker is running: `php artisan queue:work`
- Check `failed_jobs` table for errors
- View logs: `storage/logs/laravel.log`

## Security Notes

- Use SSL API (port 8729) in production
- Store router passwords encrypted (done automatically by model)
- Restrict API access to specific IP addresses on MikroTik
- Use strong passwords for Grafana admin
- Enable HTTPS for Grafana in production
