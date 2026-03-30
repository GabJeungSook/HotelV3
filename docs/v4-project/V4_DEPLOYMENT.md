# V4 Deployment & Infrastructure

Production deployment on DigitalOcean with Nginx, Redis, Supervisor, and queue/scheduler setup.

---

## Server Stack

| Component | Technology | Purpose |
|-----------|-----------|---------|
| **Cloud** | DigitalOcean Droplet | VPS hosting |
| **OS** | Ubuntu 24.04 LTS | Server OS |
| **Web Server** | Nginx | Reverse proxy, static files, SSL |
| **PHP** | PHP 8.3+ FPM | Application runtime |
| **Database** | MySQL 8.x | Primary database |
| **Cache** | Redis | Cache, sessions, queue broker |
| **Process Manager** | Supervisor | Queue workers, scheduler |
| **SSL** | Certbot (Let's Encrypt) | Free HTTPS |
| **Node** | Node.js 20 LTS | Vite build (build-time only) |

---

## Nginx Configuration

```nginx
# /etc/nginx/sites-available/hotelv4

server {
    listen 80;
    server_name hotel.alma.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name hotel.alma.com;

    root /var/www/hotelv4/public;
    index index.php;

    # SSL (Certbot auto-manages these)
    ssl_certificate /etc/letsencrypt/live/hotel.alma.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/hotel.alma.com/privkey.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    # Max upload size (for profile photos, menu images)
    client_max_body_size 10M;

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml;

    # Static files cache
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Laravel app
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
    }

    # Deny hidden files
    location ~ /\.(?!well-known) {
        deny all;
    }
}
```

---

## Redis Configuration

Redis handles 3 things: cache, sessions, and queue broker.

### .env Configuration

```env
# Cache
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

# Sessions
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Queue
QUEUE_CONNECTION=redis

# Broadcasting (Pusher still used for real-time, Redis for queue only)
BROADCAST_CONNECTION=pusher
```

### Redis Server Setup

```bash
# Install
sudo apt install redis-server

# Configure — bind to localhost only
sudo nano /etc/redis/redis.conf
# Set: bind 127.0.0.1 ::1
# Set: supervised systemd
# Set: maxmemory 256mb
# Set: maxmemory-policy allkeys-lru

# Enable and start
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Verify
redis-cli ping
# → PONG
```

### Laravel Redis Package

```bash
composer require predis/predis
# Or use phpredis extension (faster):
sudo apt install php8.3-redis
```

---

## Supervisor Configuration

Supervisor keeps queue workers and the scheduler running. If they crash, Supervisor auto-restarts them.

### Queue Worker

```ini
# /etc/supervisor/conf.d/hotelv4-worker.conf

[program:hotelv4-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/hotelv4/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/hotelv4/storage/logs/worker.log
stopwaitsecs=3600
```

**`numprocs=2`** — runs 2 worker processes. Increase if queue gets backed up.

### Scheduler (Cron via Supervisor)

```ini
# /etc/supervisor/conf.d/hotelv4-scheduler.conf

[program:hotelv4-scheduler]
process_name=%(program_name)s
command=php /var/www/hotelv4/artisan schedule:work
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/hotelv4/storage/logs/scheduler.log
```

**`schedule:work`** — Laravel's built-in scheduler daemon (runs every minute without cron).

### Supervisor Commands

```bash
# Read new config
sudo supervisorctl reread

# Apply changes
sudo supervisorctl update

# Check status
sudo supervisorctl status

# Restart workers after deployment
sudo supervisorctl restart hotelv4-worker:*
```

---

## Cron Jobs / Scheduled Tasks

### Laravel Scheduler Setup

If using `schedule:work` via Supervisor (recommended), no crontab needed. But if you prefer cron:

```bash
# Traditional cron approach (alternative to schedule:work)
* * * * * cd /var/www/hotelv4 && php artisan schedule:run >> /dev/null 2>&1
```

### Scheduled Tasks for HotelV4

```php
// app/Console/Kernel.php (or routes/console.php in Laravel 12)

use Illuminate\Support\Facades\Schedule;

// Cleanup expired kiosk requests (every minute)
Schedule::command('kiosk:cleanup')->everyMinute();

// Auto-close stale shifts (every 30 minutes)
Schedule::command('shifts:auto-close')->everyThirtyMinutes();

// Clear expired sessions (daily at 3 AM)
Schedule::command('session:gc')->dailyAt('03:00');

// Prune old activity logs (weekly, keep 90 days)
Schedule::command('activitylog:clean --days=90')->weekly();

// Cache optimization (daily at 4 AM)
Schedule::command('optimize:clear')->dailyAt('04:00');

// Database backup (daily at 2 AM — if using spatie/laravel-backup)
Schedule::command('backup:run --only-db')->dailyAt('02:00');
Schedule::command('backup:clean')->dailyAt('02:30');
```

### Custom Artisan Commands

```php
// app/Console/Commands/CleanupKioskRequests.php
// Deletes expired kiosk_requests based on branch_settings.kiosk_time_limit
// Runs every minute via scheduler

// app/Console/Commands/AutoCloseStaleShifts.php
// Closes shifts open longer than branch_settings.stale_shift_hours
// Runs every 30 minutes via scheduler
```

---

## Queue Jobs

These operations run in background queues (not blocking the web request):

| Job | Trigger | What It Does |
|-----|---------|-------------|
| `SendKioskNotification` | Kiosk check-in/out request | Pusher event to frontdesk |
| `ProcessActivityLog` | Any loggable action | Write to activity_log (if async logging) |
| `SendShiftCloseNotification` | Shift closed | Notify admin of shortage if difference < 0 |
| `GenerateStayCode` | Check-in confirmed | Generate unique QR code |
| `UpdateMenuStock` | Food/amenity order | Deduct stock, create stock log |

### Queue Configuration

```php
// config/queue.php
'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
    'queue' => env('REDIS_QUEUE', 'default'),
    'retry_after' => 90,
    'block_for' => null,
    'after_commit' => false,
],
```

### Priority Queues (Optional)

```bash
# High priority: kiosk notifications (real-time)
# Default: activity logs, stock updates
# Low: cleanup, backups

php artisan queue:work redis --queue=high,default,low
```

---

## Production .env

```env
APP_NAME="HOMI Hotel"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://hotel.alma.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotelv4
DB_USERNAME=hotelv4_user
DB_PASSWORD=strong_password_here

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Pusher (real-time kiosk notifications)
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
PUSHER_APP_CLUSTER=ap1

# Mail (for notifications — optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587

# File storage
FILESYSTEM_DISK=public

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=error
```

---

## Deployment Script

```bash
#!/bin/bash
# deploy.sh — run after git pull on production

set -e

echo "Deploying HotelV4..."

# Maintenance mode
php artisan down --retry=60

# Pull latest
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
php artisan filament:cache-components

# Restart queue workers
sudo supervisorctl restart hotelv4-worker:*

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Back online
php artisan up

echo "Deployment complete!"
```

---

## Server Directory Structure

```
/var/www/hotelv4/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← Nginx root
├── resources/
├── routes/
├── storage/
│   ├── app/
│   │   └── public/  ← Uploaded files (symlinked to public/storage)
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/  ← Not used (Redis handles sessions)
│   │   └── views/
│   └── logs/
│       ├── laravel.log
│       ├── worker.log    ← Queue worker logs
│       └── scheduler.log ← Scheduler logs
├── vendor/
├── .env              ← Production environment
└── deploy.sh         ← Deployment script
```

---

## Monitoring & Health

### Health Check Endpoint

```
GET /up → Laravel health check (configured in bootstrap/app.php)
```

### Log Monitoring

```bash
# Watch Laravel errors
tail -f /var/www/hotelv4/storage/logs/laravel.log

# Watch queue worker
tail -f /var/www/hotelv4/storage/logs/worker.log

# Watch scheduler
tail -f /var/www/hotelv4/storage/logs/scheduler.log
```

### Supervisor Status

```bash
sudo supervisorctl status
# hotelv4-worker:hotelv4-worker_00   RUNNING   pid 12345, uptime 2:30:00
# hotelv4-worker:hotelv4-worker_01   RUNNING   pid 12346, uptime 2:30:00
# hotelv4-scheduler                  RUNNING   pid 12347, uptime 2:30:00
```

### Redis Monitoring

```bash
redis-cli info memory
redis-cli info stats
redis-cli monitor  # Live command stream (dev only)
```

---

## Security Checklist

| Item | How |
|------|-----|
| HTTPS enforced | Nginx redirects HTTP → HTTPS |
| APP_DEBUG=false | .env in production |
| Strong DB password | Not default |
| Redis localhost only | bind 127.0.0.1 in redis.conf |
| Firewall | UFW: allow 22, 80, 443 only |
| File permissions | www-data owns storage/, 775 |
| .env not in git | .gitignore |
| Sanctum tokens | Kiosk devices only, revocable |
| Rate limiting | Login attempts limited |
| CORS | API restricted to kiosk domains |

---

## DigitalOcean Recommended Droplet

| Spec | Value |
|------|-------|
| **Plan** | Basic Shared CPU |
| **RAM** | 2 GB (start), scale to 4 GB |
| **vCPUs** | 1 (start), scale to 2 |
| **Storage** | 50 GB SSD |
| **Region** | Singapore (SGP1) — closest to Philippines |
| **OS** | Ubuntu 24.04 LTS |
| **Estimated cost** | ~$12-24/month |

Scale up when:
- Queue workers consistently behind
- Response times > 500ms
- Redis memory > 200MB
- CPU consistently > 80%
