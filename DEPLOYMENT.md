# Production Deployment Guide: SmartPOS SaaS

## 1. Server Environment Prerequisites
- **Operating System**: Ubuntu 22.04 LTS or 24.04 LTS (or equivalent Debian/RHEL).
- **Web Server**: Nginx (recommended) or Apache 2.4+.
- **PHP**: PHP 8.3 or 8.4 with FPM and extensions:
  `php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-intl php8.3-gd php8.3-redis`
- **Database**: MySQL 8.0+ or MariaDB 10.6+.
- **Caching & Queues**: Redis 7.x.
- **Node.js**: Node 20+ & NPM (for asset builds).
- **Composer**: Composer 2.7+.

---

## 2. Nginx Server Block Configuration
Create `/etc/nginx/sites-available/pos-saas.conf`:

```nginx
server {
    listen 80;
    server_name pos.yourdomain.com *.pos.yourdomain.com;
    root /var/www/pos_saas/public;

    index index.php index.html;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 50M;
}
```

Enable the configuration and reload Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/pos-saas.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 3. SSL Configuration (Let's Encrypt)
```bash
sudo certbot --nginx -d pos.yourdomain.com
```

---

## 4. Application Installation Steps
```bash
# 1. Clone or copy code
cd /var/www/pos_saas

# 2. Set ownership and file permissions
sudo chown -R www-data:www-data /var/www/pos_saas
sudo chmod -R 775 /var/www/pos_saas/storage /var/www/pos_saas/bootstrap/cache

# 3. Install production PHP packages
composer install --no-dev --optimize-autoloader

# 4. Configure environment
cp .env.example .env
nano .env # Set APP_ENV=production, DB credentials, APP_URL

# 5. Generate application key
php artisan key:generate

# 6. Run database migrations and seeders
php artisan migrate --force
php artisan db:seed --force

# 7. Create storage symlink
php artisan storage:link

# 8. Cache configuration and routes for speed
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5. Cron Job Setup (Laravel Scheduler)
Edit the server crontab for www-data:
```bash
sudo crontab -u www-data -e
```
Add the following line:
```bash
* * * * * cd /var/www/pos_saas && php artisan schedule:run >> /dev/null 2>&1
```

---

## 6. Background Queue Worker (Supervisor)
Create `/etc/supervisor/conf.d/pos-saas-worker.conf`:
```ini
[program:pos-saas-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pos_saas/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/pos_saas/storage/logs/worker.log
stopwaitsecs=3600
```
Update and start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pos-saas-worker:*
```
