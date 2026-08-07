# Quickad (Laravel 11) — Production Deployment Guide

## Requirements
- PHP 8.2+ (tested on 8.5)
- MySQL 8+ / MariaDB 10.5+
- Composer 2.x
- Node 18+ (only if you rebuild assets)
- Web server: **nginx** (recommended) or Apache

## 1. Server prep
```bash
sudo apt install -y php php-mysql php-mbstring php-xml php-gd php-curl php-fpm nginx
```

## 2. Deploy code
```bash
git clone <your-repo> /var/www/quickad
cd /var/www/quickad/laravel-quickad
cp .env.example .env
# edit .env with production DB creds, APP_URL, etc.
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
# Import schema on fresh DB
php artisan migrate --force
```

## 3. Import legacy seed data (optional — for a fresh install)
```bash
sed 's/<<prefix>>/ad_/g' ../install/database/data.sql | mysql -u root -p quickad
```

## 4. Permissions
```bash
sudo chown -R www-data:www-data /var/www/quickad
sudo chmod -R 775 /var/www/quickad/laravel-quickad/storage
sudo chmod -R 775 /var/www/quickad/laravel-quickad/bootstrap/cache
sudo chmod -R 775 /var/www/quickad/laravel-quickad/public/storage/products
```

## 5. nginx site (`/etc/nginx/sites-available/quickad`)
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/quickad/laravel-quickad/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.5-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known) {
        deny all;
    }

    client_max_body_size 20M;
}
```
```bash
sudo ln -s /etc/nginx/sites-available/quickad /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

## 6. Queue worker (for emails / image resizing)
```bash
sudo tee /etc/systemd/system/quickad-queue.service <<EOF
[Unit]
Description=Quickad queue worker
After=network.target
[Service]
User=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/quickad/laravel-quickad/artisan queue:work --sleep=3 --tries=3
[Install]
WantedBy=multi-user.target
EOF
sudo systemctl enable --now quickad-queue
```

## 7. Cron (expire ads, cleanup)
```bash
* * * * * cd /var/www/quickad/laravel-quickad && php artisan schedule:run >> /dev/null 2>&1
```

## 8. Post-deploy checks
- `curl -I https://your-domain.com/`          — 200
- `curl -I https://your-domain.com/admin`     — 302 → /admin/login
- `curl -I https://your-domain.com/api/v1/categories -H "Accept: application/json"` — 200

## Environment variables required
```
APP_NAME=Quickad
APP_ENV=production
APP_KEY=<generated>
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quickad
DB_USERNAME=quickad_user
DB_PASSWORD=<secret>
DB_PREFIX=ad_

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database

# SSLCommerz payment gateway (sole active gateway)
SSLCOMMERZ_MODE=live                              # sandbox for staging
SSLCOMMERZ_STORE_ID=<your_live_store_id>
SSLCOMMERZ_STORE_PASSWORD=<your_live_store_password>
SSLCOMMERZ_CURRENCY=BDT
SSLCOMMERZ_LOCALHOST=false
SSLCOMMERZ_VERIFY_HASH=true

# Where SSLCommerz redirects the buyer back after checkout (Next.js frontend)
FRONTEND_URL=https://your-frontend-domain.com
```

## SSLCommerz merchant setup

1. Sign up at https://developer.sslcommerz.com/ and request a live store.
2. Whitelist these callback URLs in the merchant panel:
   - `https://your-api-domain.com/api/v1/payments/sslcommerz/success`
   - `https://your-api-domain.com/api/v1/payments/sslcommerz/fail`
   - `https://your-api-domain.com/api/v1/payments/sslcommerz/cancel`
   - `https://your-api-domain.com/api/v1/payments/sslcommerz/ipn`
3. Copy the live `store_id` and `store_password` into the env vars above.
4. Toggle `SSLCOMMERZ_MODE=live` — the code auto-selects the correct API
   domain (`securepay.sslcommerz.com` vs `sandbox.sslcommerz.com`).

