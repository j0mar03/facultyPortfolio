# Faculty Portfolio System - Deployment Guide

**Version:** 1.0
**Last Updated:** November 13, 2025
**Stack:** Laravel 12.37, Livewire 3.6, MySQL 8.0, Nginx, Docker

---

## Table of Contents

1. [Deployment Readiness Assessment](#deployment-readiness-assessment)
2. [Pre-Deployment Checklist](#pre-deployment-checklist)
3. [Deployment Options](#deployment-options)
4. [Security Hardening](#security-hardening)
5. [CI/CD Automation](#cicd-automation)
6. [Rollback Strategy](#rollback-strategy)
7. [Monitoring & Maintenance](#monitoring--maintenance)

---

## Deployment Readiness Assessment

### ✅ **READY Components**

#### Application Stack
- **PHP Version:** 8.3 ✅
- **Laravel Framework:** 12.37.0 ✅
- **Livewire:** 3.6.4 ✅
- **Database:** MySQL 8.0 ✅
- **Web Server:** Nginx 1.27 ✅
- **Frontend Build:** Vite 7.0.7 ✅

#### Database
- **Migrations:** 19 migrations present ✅
- **Seeders:** 8 seeders available ✅
  - CourseSeeder
  - UserSeeder
  - DCPETCurriculumSeeder
  - DECETCurriculumSeeder
  - ClassOfferingSeeder
  - DCPETChairSeeder
  - DemoSubjectSeeder

#### Security Features
- **Authentication:** Laravel Jetstream + Sanctum ✅
- **Email Verification:** Enabled ✅
- **Password Hashing:** Bcrypt ✅
- **CSRF Protection:** Enabled ✅
- **HTTPS:** Configured (self-signed cert for dev) ✅
- **No Hardcoded Credentials:** Verified ✅

#### File Structure
- **Environment Template:** .env.example present ✅
- **Gitignore:** Properly configured ✅
- **Docker Setup:** Complete ✅
- **Build Scripts:** Configured in composer.json ✅

---

### ⚠️ **RISKS & ISSUES TO ADDRESS**

#### Critical Issues

1. **SSL Certificate in Git**
   - **Risk:** Self-signed SSL key is untracked but directory structure exposed
   - **Action Required:** Add `docker/nginx/ssl/` to .gitignore
   - **Command:**
     ```bash
     echo "docker/nginx/ssl/*.key" >> .gitignore
     echo "docker/nginx/ssl/*.crt" >> .gitignore
     ```

2. **Debug Mode**
   - **Current:** `APP_DEBUG=true` in production would expose sensitive data
   - **Action Required:** Ensure production .env sets `APP_DEBUG=false`

3. **Database Credentials**
   - **Current:** Weak credentials (`faculty/faculty`) in .env
   - **Action Required:** Use strong passwords in production

4. **Session Security**
   - **Current:** SESSION_SECURE_COOKIE not set
   - **Action Required:** Set to `true` in production for HTTPS

5. **Missing Production Configs**
   - No `.env.production` template
   - No CORS configuration file (expected at config/cors.php but missing)
   - No logging configuration for production

---

### 📋 **MISSING Components**

1. **Environment Variables Not in .env.example:**
   - `SESSION_SECURE_COOKIE`
   - `FORCE_HTTPS`
   - Production mail settings
   - Backup configurations

2. **Documentation:**
   - Deployment runbook
   - Database backup procedures
   - Disaster recovery plan

3. **Monitoring:**
   - Application performance monitoring (APM)
   - Error tracking (Sentry, Bugsnag)
   - Uptime monitoring

4. **Testing:**
   - No test suite found in repository
   - No CI/CD pipeline configured

---

## Pre-Deployment Checklist

### 1. Environment Configuration

```bash
# Copy and configure production environment
cp .env.example .env.production

# Edit .env.production with production values
nano .env.production
```

**Critical Environment Variables:**

```env
# Application
APP_NAME="Faculty Portfolio System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Security
APP_KEY=base64:GenerateNewKeyHere
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# Database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=faculty_portfolio
DB_USERNAME=your_db_user
DB_PASSWORD=strong-random-password-here

# Mail (Production)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@pup.edu.ph
MAIL_PASSWORD=app-specific-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@pup.edu.ph
MAIL_FROM_NAME="${APP_NAME}"

# Cache & Sessions
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null

# File Storage
FILESYSTEM_DISK=s3  # or local for VPS
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=your-bucket-name
```

### 2. Security Hardening

```bash
# Generate new APP_KEY
php artisan key:generate --force

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper file permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs
chown -R www-data:www-data storage bootstrap/cache
```

### 3. Database Preparation

```bash
# Run migrations
php artisan migrate --force

# Run seeders (optional, for demo data)
php artisan db:seed --class=CourseSeeder
php artisan db:seed --class=UserSeeder

# Create admin user manually
php artisan tinker
User::create([
    'name' => 'Admin User',
    'email' => 'admin@pup.edu.ph',
    'password' => Hash::make('secure-password'),
    'role' => 'admin',
    'email_verified_at' => now(),
]);
```

### 4. Asset Compilation

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev
npm ci --production

# Build assets
npm run build

# Link storage
php artisan storage:link
```

---

## Deployment Options

### Option 1: Docker Deployment (Recommended)

**Best For:** Production-ready containerized deployment with easy scaling

#### Step 1: Create Production Dockerfile

Create `docker/php/Dockerfile.production`:

```dockerfile
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip rsync \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    supervisor cron \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip opcache \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP Production Configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Upload limits
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# OPcache configuration
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

# Copy application files
COPY --chown=www-data:www-data . /var/www/html

# Install dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Supervisor configuration for queue workers
COPY docker/supervisor/laravel-worker.conf /etc/supervisor/conf.d/

CMD ["php-fpm"]
```

#### Step 2: Create Production docker-compose.yml

Create `docker-compose.production.yml`:

```yaml
services:
  app:
    build:
      context: .
      dockerfile: docker/php/Dockerfile.production
    container_name: facultyportfolio-app-prod
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - storage-data:/var/www/html/storage
    environment:
      - TZ=Asia/Manila
    depends_on:
      - db
      - redis
    networks:
      - faculty-network

  web:
    build:
      context: .
      dockerfile: docker/nginx/Dockerfile
    container_name: facultyportfolio-web-prod
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    depends_on:
      - app
    volumes:
      - ./public:/var/www/html/public:ro
      - storage-data:/var/www/html/storage:ro
      - ./docker/nginx/ssl:/etc/nginx/ssl:ro
    networks:
      - faculty-network

  db:
    image: mysql:8.0
    container_name: facultyportfolio-db-prod
    restart: unless-stopped
    environment:
      - MYSQL_DATABASE=faculty_portfolio
      - MYSQL_USER=${DB_USERNAME}
      - MYSQL_PASSWORD=${DB_PASSWORD}
      - MYSQL_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
    command: --default-authentication-plugin=mysql_native_password
    volumes:
      - db-data:/var/lib/mysql
    networks:
      - faculty-network

  redis:
    image: redis:7-alpine
    container_name: facultyportfolio-redis-prod
    restart: unless-stopped
    networks:
      - faculty-network

  # Queue Worker
  queue:
    build:
      context: .
      dockerfile: docker/php/Dockerfile.production
    container_name: facultyportfolio-queue-prod
    restart: unless-stopped
    command: php artisan queue:work --tries=3 --timeout=90
    depends_on:
      - app
      - redis
    volumes:
      - storage-data:/var/www/html/storage
    networks:
      - faculty-network

  # Scheduler
  scheduler:
    build:
      context: .
      dockerfile: docker/php/Dockerfile.production
    container_name: facultyportfolio-scheduler-prod
    restart: unless-stopped
    command: bash -c "while true; do php artisan schedule:run --verbose --no-interaction; sleep 60; done"
    depends_on:
      - app
    volumes:
      - storage-data:/var/www/html/storage
    networks:
      - faculty-network

volumes:
  db-data:
    driver: local
  storage-data:
    driver: local

networks:
  faculty-network:
    driver: bridge
```

#### Step 3: Deploy with Docker

```bash
# Build images
docker-compose -f docker-compose.production.yml build

# Start services
docker-compose -f docker-compose.production.yml up -d

# Run migrations
docker-compose -f docker-compose.production.yml exec app php artisan migrate --force

# Cache configurations
docker-compose -f docker-compose.production.yml exec app php artisan config:cache
docker-compose -f docker-compose.production.yml exec app php artisan route:cache
docker-compose -f docker-compose.production.yml exec app php artisan view:cache
```

---

### Option 2: Cloud Platform Deployment

#### AWS (Elastic Beanstalk + RDS)

**Best For:** Scalable cloud deployment with managed services

**Prerequisites:**
- AWS Account
- AWS CLI installed
- EB CLI installed

**Steps:**

1. **Install EB CLI:**
```bash
pip install awsebcli
```

2. **Initialize Elastic Beanstalk:**
```bash
eb init faculty-portfolio \
  --platform "PHP 8.3 running on 64bit Amazon Linux 2023" \
  --region ap-southeast-1
```

3. **Create `.ebextensions/01_setup.config`:**
```yaml
option_settings:
  aws:elasticbeanstalk:container:php:phpini:
    document_root: /public
    memory_limit: 256M
    upload_max_filesize: 10M
    post_max_size: 10M
    max_execution_time: 300

  aws:elasticbeanstalk:application:environment:
    APP_ENV: production
    APP_DEBUG: false
    CACHE_DRIVER: redis
    SESSION_DRIVER: redis
    QUEUE_CONNECTION: sqs

container_commands:
  01_install_composer_dependencies:
    command: "composer install --optimize-autoloader --no-dev"
  02_run_migrations:
    command: "php artisan migrate --force"
    leader_only: true
  03_cache_config:
    command: "php artisan config:cache"
  04_cache_routes:
    command: "php artisan route:cache"
  05_cache_views:
    command: "php artisan view:cache"
  06_link_storage:
    command: "php artisan storage:link"
    leader_only: true
```

4. **Create RDS Database:**
```bash
# Create RDS MySQL instance
aws rds create-db-instance \
  --db-instance-identifier faculty-portfolio-db \
  --db-instance-class db.t3.micro \
  --engine mysql \
  --engine-version 8.0 \
  --master-username admin \
  --master-user-password YourSecurePassword \
  --allocated-storage 20 \
  --region ap-southeast-1
```

5. **Deploy Application:**
```bash
# Create environment
eb create faculty-portfolio-prod \
  --database \
  --database.engine mysql \
  --database.username admin

# Deploy
eb deploy

# Set environment variables
eb setenv \
  APP_KEY=base64:your-generated-key \
  DB_HOST=your-rds-endpoint \
  DB_DATABASE=faculty_portfolio \
  DB_USERNAME=admin \
  DB_PASSWORD=YourSecurePassword
```

**Monthly Cost Estimate:**
- EB Environment (t3.small): ~$15/month
- RDS MySQL (db.t3.micro): ~$15/month
- Data Transfer: ~$5/month
- **Total: ~$35/month**

---

#### Google Cloud Platform (App Engine + Cloud SQL)

**Best For:** Managed platform with automatic scaling

**Steps:**

1. **Create `app.yaml`:**
```yaml
runtime: php83

env_variables:
  APP_KEY: "base64:your-generated-key"
  APP_STORAGE: /tmp
  VIEW_COMPILED_PATH: /tmp/views
  SESSION_DRIVER: cookie
  CACHE_DRIVER: file
  LOG_CHANNEL: stderr

handlers:
  - url: /.*
    script: auto
    secure: always
    redirect_http_response_code: 301

automatic_scaling:
  target_cpu_utilization: 0.65
  min_instances: 1
  max_instances: 10
```

2. **Deploy:**
```bash
# Initialize GCP project
gcloud init

# Create Cloud SQL instance
gcloud sql instances create faculty-portfolio-db \
  --database-version=MYSQL_8_0 \
  --tier=db-f1-micro \
  --region=asia-southeast1

# Deploy
gcloud app deploy
```

**Monthly Cost Estimate:**
- App Engine (F1 instance): ~$25/month
- Cloud SQL (db-f1-micro): ~$10/month
- **Total: ~$35/month**

---

#### Azure (App Service + Azure Database for MySQL)

**Best For:** Enterprise integration with Microsoft services

**Steps:**

1. **Create resources:**
```bash
# Create resource group
az group create --name faculty-portfolio-rg --location southeastasia

# Create App Service Plan
az appservice plan create \
  --name faculty-portfolio-plan \
  --resource-group faculty-portfolio-rg \
  --sku B1 \
  --is-linux

# Create Web App
az webapp create \
  --name faculty-portfolio \
  --resource-group faculty-portfolio-rg \
  --plan faculty-portfolio-plan \
  --runtime "PHP:8.3"

# Create MySQL Database
az mysql flexible-server create \
  --name faculty-portfolio-db \
  --resource-group faculty-portfolio-rg \
  --location southeastasia \
  --admin-user adminuser \
  --admin-password YourSecurePassword \
  --sku-name Standard_B1ms \
  --tier Burstable \
  --version 8.0
```

2. **Configure and Deploy:**
```bash
# Set environment variables
az webapp config appsettings set \
  --name faculty-portfolio \
  --resource-group faculty-portfolio-rg \
  --settings \
    APP_KEY=base64:your-key \
    DB_HOST=faculty-portfolio-db.mysql.database.azure.com \
    DB_DATABASE=faculty_portfolio \
    DB_USERNAME=adminuser \
    DB_PASSWORD=YourSecurePassword

# Deploy using Git
git remote add azure https://faculty-portfolio.scm.azurewebsites.net/faculty-portfolio.git
git push azure main
```

**Monthly Cost Estimate:**
- App Service (B1): ~$13/month
- Azure MySQL (B1ms): ~$15/month
- **Total: ~$28/month**

---

### Option 3: Free Hosting Options

#### Railway.app (Recommended Free Tier)

**Best For:** Quick deployment with generous free tier

**Features:**
- $5 free credit per month
- PostgreSQL/MySQL databases included
- Automatic HTTPS
- Git-based deployment

**Steps:**

1. **Sign up at railway.app**
2. **Click "New Project" → "Deploy from GitHub"**
3. **Select your repository**
4. **Add MySQL database from marketplace**
5. **Set environment variables in Railway dashboard**
6. **Deploy automatically on git push**

**Free Tier Limits:**
- 500 execution hours/month
- $5 credit (covers most small apps)

---

#### Render.com (Free Tier)

**Best For:** Simple deployment with managed services

**Features:**
- Free tier for web services
- PostgreSQL database included
- Automatic SSL
- Docker support

**Create `render.yaml`:**
```yaml
services:
  - type: web
    name: faculty-portfolio
    env: php
    buildCommand: composer install && npm run build
    startCommand: php-fpm
    envVars:
      - key: APP_KEY
        generateValue: true
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false

databases:
  - name: faculty-portfolio-db
    databaseName: faculty_portfolio
    user: faculty
```

**Free Tier Limits:**
- Services spin down after 15 min inactivity
- 750 hours/month

---

#### Fly.io (Free Tier)

**Best For:** Global edge deployment

**Create `fly.toml`:**
```toml
app = "faculty-portfolio"
primary_region = "sin"

[build]
  dockerfile = "docker/php/Dockerfile.production"

[env]
  APP_ENV = "production"
  LOG_CHANNEL = "stderr"

[[services]]
  http_checks = []
  internal_port = 9000
  processes = ["app"]
  protocol = "tcp"

  [[services.ports]]
    port = 80
    handlers = ["http"]

  [[services.ports]]
    port = 443
    handlers = ["tls", "http"]
```

**Deploy:**
```bash
# Install flyctl
curl -L https://fly.io/install.sh | sh

# Launch app
fly launch

# Deploy
fly deploy
```

**Free Tier:**
- 3 shared-cpu-1x VMs
- 160GB bandwidth
- PostgreSQL database

---

### Option 4: VPS Deployment (DigitalOcean/Linode)

**Best For:** Full control, cost-effective for production

**Server Requirements:**
- OS: Ubuntu 22.04 LTS
- RAM: Minimum 2GB (Recommended 4GB)
- Storage: 20GB SSD
- CPU: 2 cores

#### Initial Server Setup

```bash
# 1. Update system
sudo apt update && sudo apt upgrade -y

# 2. Install required packages
sudo apt install -y nginx mysql-server php8.3-fpm php8.3-mysql php8.3-mbstring \
  php8.3-xml php8.3-bcmath php8.3-curl php8.3-gd php8.3-zip php8.3-redis \
  redis-server supervisor git unzip

# 3. Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 4. Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# 5. Secure MySQL
sudo mysql_secure_installation
```

#### Configure MySQL

```bash
sudo mysql -u root -p

CREATE DATABASE faculty_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'faculty_user'@'localhost' IDENTIFIED BY 'secure-password-here';
GRANT ALL PRIVILEGES ON faculty_portfolio.* TO 'faculty_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Deploy Application

```bash
# 1. Clone repository
cd /var/www
sudo git clone https://github.com/yourusername/facultyPortfolio.git
sudo chown -R www-data:www-data facultyPortfolio

# 2. Install dependencies
cd facultyPortfolio
composer install --optimize-autoloader --no-dev
npm ci --production
npm run build

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# Edit .env with production settings
nano .env

# 4. Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 5. Run migrations
php artisan migrate --force

# 6. Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Link storage
php artisan storage:link
```

#### Configure Nginx

Create `/etc/nginx/sites-available/faculty-portfolio`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.com;
    root /var/www/facultyPortfolio/public;

    # SSL Configuration (use Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    index index.php;

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
        fastcgi_param HTTPS on;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 64M;
}
```

**Enable site:**
```bash
sudo ln -s /etc/nginx/sites-available/faculty-portfolio /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### Install SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d your-domain.com

# Auto-renewal is configured automatically
```

#### Configure Queue Worker (Supervisor)

Create `/etc/supervisor/conf.d/faculty-portfolio-worker.conf`:

```ini
[program:faculty-portfolio-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/facultyPortfolio/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/facultyPortfolio/storage/logs/worker.log
stopwaitsecs=3600
```

**Start supervisor:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start faculty-portfolio-worker:*
```

#### Configure Cron for Laravel Scheduler

```bash
sudo crontab -e -u www-data

# Add this line:
* * * * * cd /var/www/facultyPortfolio && php artisan schedule:run >> /dev/null 2>&1
```

**VPS Cost:**
- DigitalOcean Droplet (2GB): $12/month
- Linode (2GB): $12/month
- Vultr (2GB): $12/month

---

## Security Hardening

### 1. Environment Configuration

**Create `.env.production.example`:**

```env
APP_NAME="Faculty Portfolio System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

# Force HTTPS
FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=faculty_portfolio
DB_USERNAME=
DB_PASSWORD=

# Security
BCRYPT_ROUNDS=12

# Logging (production)
LOG_CHANNEL=daily
LOG_LEVEL=error
LOG_DAYS=14

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 2. Update .gitignore

Add to `.gitignore`:

```gitignore
# SSL Certificates
docker/nginx/ssl/*.key
docker/nginx/ssl/*.crt
docker/nginx/ssl/*.pem

# Production Environment
.env.production

# Backups
*.sql
*.sql.gz
backup/
```

### 3. Security Headers Middleware

Create `app/Http/Middleware/SecurityHeaders.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
```

Register in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
})
```

### 4. Rate Limiting

Update `app/Providers/AppServiceProvider.php`:

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

public function boot(): void
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });

    RateLimiter::for('uploads', function (Request $request) {
        return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
    });
}
```

### 5. Database Backup Script

Create `scripts/backup-database.sh`:

```bash
#!/bin/bash

# Configuration
BACKUP_DIR="/var/backups/faculty-portfolio"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="faculty_portfolio"
DB_USER="faculty_user"
DB_PASSWORD="your-password"
RETENTION_DAYS=30

# Create backup directory
mkdir -p $BACKUP_DIR

# Dump database
mysqldump -u $DB_USER -p$DB_PASSWORD $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Remove old backups
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +$RETENTION_DAYS -delete

echo "Backup completed: $BACKUP_DIR/db_backup_$DATE.sql.gz"
```

**Schedule in cron:**
```bash
0 2 * * * /var/www/facultyPortfolio/scripts/backup-database.sh >> /var/log/faculty-backup.log 2>&1
```

---

## CI/CD Automation

### Option 1: GitHub Actions

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [main]
  workflow_dispatch:

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: mbstring, xml, bcmath, pdo_mysql

      - name: Install Composer dependencies
        run: composer install --prefer-dist --no-progress

      - name: Run tests
        run: php artisan test

  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    steps:
      - uses: actions/checkout@v4

      - name: Deploy to Server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/facultyPortfolio
            git pull origin main
            composer install --optimize-autoloader --no-dev
            npm ci --production
            npm run build
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            sudo supervisorctl restart faculty-portfolio-worker:*
            echo "Deployment completed at $(date)"

      - name: Notify Deployment
        if: always()
        uses: 8398a7/action-slack@v3
        with:
          status: ${{ job.status }}
          text: 'Deployment ${{ job.status }}'
        env:
          SLACK_WEBHOOK_URL: ${{ secrets.SLACK_WEBHOOK }}
```

**Setup GitHub Secrets:**
- `HOST`: Your server IP/domain
- `USERNAME`: SSH username
- `SSH_PRIVATE_KEY`: Private SSH key
- `SLACK_WEBHOOK`: (Optional) Slack notification webhook

---

### Option 2: GitLab CI/CD

Create `.gitlab-ci.yml`:

```yaml
stages:
  - test
  - build
  - deploy

variables:
  MYSQL_DATABASE: faculty_portfolio_test
  MYSQL_ROOT_PASSWORD: secret

test:
  stage: test
  image: php:8.3
  services:
    - mysql:8.0
  before_script:
    - apt-get update && apt-get install -y git zip unzip libzip-dev
    - docker-php-ext-install pdo pdo_mysql zip
    - curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
  script:
    - cp .env.example .env
    - composer install
    - php artisan key:generate
    - php artisan test

build:
  stage: build
  image: node:20
  script:
    - npm ci
    - npm run build
  artifacts:
    paths:
      - public/build

deploy_production:
  stage: deploy
  only:
    - main
  before_script:
    - 'command -v ssh-agent >/dev/null || ( apt-get update -y && apt-get install openssh-client -y )'
    - eval $(ssh-agent -s)
    - echo "$SSH_PRIVATE_KEY" | tr -d '\r' | ssh-add -
    - mkdir -p ~/.ssh
    - chmod 700 ~/.ssh
  script:
    - ssh -o StrictHostKeyChecking=no $SSH_USER@$SSH_HOST "
        cd /var/www/facultyPortfolio &&
        git pull origin main &&
        composer install --optimize-autoloader --no-dev &&
        php artisan migrate --force &&
        php artisan config:cache &&
        php artisan route:cache &&
        php artisan view:cache &&
        sudo supervisorctl restart faculty-portfolio-worker:*
      "
```

---

### Option 3: Jenkins Pipeline

Create `Jenkinsfile`:

```groovy
pipeline {
    agent any

    environment {
        SSH_CREDENTIALS = credentials('faculty-portfolio-ssh')
        SERVER_HOST = 'your-server.com'
    }

    stages {
        stage('Test') {
            steps {
                sh 'composer install'
                sh 'cp .env.example .env'
                sh 'php artisan key:generate'
                sh 'php artisan test'
            }
        }

        stage('Build Assets') {
            steps {
                sh 'npm ci'
                sh 'npm run build'
            }
        }

        stage('Deploy') {
            when {
                branch 'main'
            }
            steps {
                sshagent(['faculty-portfolio-ssh']) {
                    sh """
                        ssh -o StrictHostKeyChecking=no deploy@${SERVER_HOST} '
                            cd /var/www/facultyPortfolio &&
                            git pull origin main &&
                            composer install --optimize-autoloader --no-dev &&
                            npm ci --production &&
                            npm run build &&
                            php artisan migrate --force &&
                            php artisan config:cache &&
                            php artisan route:cache &&
                            php artisan view:cache &&
                            sudo supervisorctl restart faculty-portfolio-worker:*
                        '
                    """
                }
            }
        }
    }

    post {
        success {
            slackSend color: 'good', message: "Deployment successful: ${env.JOB_NAME} #${env.BUILD_NUMBER}"
        }
        failure {
            slackSend color: 'danger', message: "Deployment failed: ${env.JOB_NAME} #${env.BUILD_NUMBER}"
        }
    }
}
```

---

## Rollback Strategy

### 1. Git-based Rollback

**Create rollback script** `scripts/rollback.sh`:

```bash
#!/bin/bash

# Get the previous commit hash
PREVIOUS_COMMIT=$(git log --oneline -2 | tail -1 | cut -d' ' -f1)

echo "Rolling back to commit: $PREVIOUS_COMMIT"

# Checkout previous commit
git checkout $PREVIOUS_COMMIT

# Install dependencies
composer install --optimize-autoloader --no-dev
npm ci --production
npm run build

# Run migrations (rollback if needed)
read -p "Rollback migrations? (y/n): " ROLLBACK_DB
if [ "$ROLLBACK_DB" == "y" ]; then
    php artisan migrate:rollback --step=1
fi

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart workers
sudo supervisorctl restart faculty-portfolio-worker:*

echo "Rollback completed!"
```

### 2. Blue-Green Deployment

**Setup two environments:**

```bash
# Blue (current production)
/var/www/faculty-blue

# Green (new deployment)
/var/www/faculty-green

# Symlink points to active
/var/www/facultyPortfolio -> /var/www/faculty-blue
```

**Deployment script:**

```bash
#!/bin/bash

ACTIVE=$(readlink /var/www/facultyPortfolio)
if [[ $ACTIVE == *"blue"* ]]; then
    TARGET="faculty-green"
    OLD="faculty-blue"
else
    TARGET="faculty-blue"
    OLD="faculty-green"
fi

# Deploy to inactive environment
cd /var/www/$TARGET
git pull origin main
composer install --optimize-autoloader --no-dev
npm ci --production && npm run build
php artisan migrate --force
php artisan config:cache

# Switch symlink (zero downtime)
ln -sfn /var/www/$TARGET /var/www/facultyPortfolio

# Reload services
sudo systemctl reload php8.3-fpm
sudo systemctl reload nginx

echo "Switched to $TARGET. Old version still available at $OLD for rollback."
```

**Rollback:**
```bash
ln -sfn /var/www/$OLD /var/www/facultyPortfolio
sudo systemctl reload nginx
```

### 3. Database Rollback

**Before each migration:**
```bash
# Backup database
php artisan db:backup

# Run migration
php artisan migrate --force

# If issues occur:
php artisan migrate:rollback --step=1

# Restore from backup if needed
mysql -u user -p faculty_portfolio < backup.sql
```

---

## Monitoring & Maintenance

### 1. Application Monitoring

**Install Laravel Telescope (Development/Staging only):**
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

**Production Error Tracking (Sentry):**

```bash
composer require sentry/sentry-laravel
php artisan sentry:publish --dsn=your-sentry-dsn
```

### 2. Server Monitoring

**Install monitoring tools:**

```bash
# Install Netdata (system monitoring)
bash <(curl -Ss https://my-netdata.io/kickstart.sh)

# Access at http://your-server:19999
```

### 3. Health Check Endpoint

Add to `routes/web.php`:

```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'cache' => Cache::has('health_check') ? 'working' : 'not working',
        'timestamp' => now()->toIso8601String(),
    ]);
});
```

### 4. Automated Backups

**Configure automated backups with `spatie/laravel-backup`:**

```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

**Configure `config/backup.php` and schedule:**

```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('backup:clean')->daily()->at('01:00');
    $schedule->command('backup:run')->daily()->at('02:00');
}
```

### 5. Performance Optimization

**Enable OPcache in production:**

```ini
; /etc/php/8.3/fpm/conf.d/opcache.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=1
```

**Laravel optimizations:**
```bash
# Production optimization
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## Post-Deployment Checklist

- [ ] SSL certificate installed and valid
- [ ] Environment variables set correctly
- [ ] Database migrations run successfully
- [ ] Asset build completed
- [ ] Storage directory writable
- [ ] Queue workers running
- [ ] Cron jobs configured
- [ ] Backups scheduled and tested
- [ ] Error logging configured
- [ ] Monitoring enabled
- [ ] Security headers configured
- [ ] Rate limiting enabled
- [ ] Debug mode disabled
- [ ] HTTPS redirect working
- [ ] Email sending functional
- [ ] File uploads working
- [ ] Health check endpoint responding

---

## Support & Troubleshooting

### Common Issues

**Issue: 500 Internal Server Error**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check Nginx error logs
sudo tail -f /var/log/nginx/error.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.3-fpm.log
```

**Issue: Permission denied on storage**
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**Issue: Queue not processing**
```bash
# Check supervisor status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart faculty-portfolio-worker:*

# Check queue jobs
php artisan queue:work --tries=3 --verbose
```

**Issue: Database connection refused**
```bash
# Check MySQL service
sudo systemctl status mysql

# Test connection
mysql -u faculty_user -p faculty_portfolio

# Check .env credentials
cat .env | grep DB_
```

---

## Maintenance Commands

```bash
# Clear all caches
php artisan optimize:clear

# Recache everything
php artisan optimize

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear old sessions
php artisan session:gc

# Database maintenance
php artisan db:show
php artisan db:table users

# View application info
php artisan about
```

---

## Conclusion

This deployment guide provides multiple deployment strategies based on your requirements:

- **Docker:** For containerized, scalable deployments
- **Cloud Platforms:** For managed, auto-scaling infrastructure
- **Free Hosting:** For development/testing or small-scale production
- **VPS:** For full control and cost-effectiveness

Choose the option that best fits your:
- Budget
- Technical expertise
- Scalability needs
- Maintenance capacity

For production deployment, recommended stack:
- **Small Scale (<100 users):** Railway/Render free tier or VPS
- **Medium Scale (100-1000 users):** VPS with proper monitoring
- **Large Scale (>1000 users):** Cloud platforms (AWS/GCP/Azure) with auto-scaling

---

**Document Version:** 1.0
**Last Updated:** November 13, 2025
**Maintained By:** Faculty Portfolio Development Team
