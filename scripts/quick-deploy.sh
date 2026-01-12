#!/bin/bash

# Faculty Portfolio System - Quick Deployment Script
# Usage: ./scripts/quick-deploy.sh [environment]
# Example: ./scripts/quick-deploy.sh production

set -e  # Exit on error

ENVIRONMENT=${1:-production}
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

echo "========================================="
echo "Faculty Portfolio System Deployment"
echo "Environment: $ENVIRONMENT"
echo "Timestamp: $TIMESTAMP"
echo "========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Functions
info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
    exit 1
}

# Check if running as correct user
if [ "$EUID" -eq 0 ]; then
    error "Do not run this script as root!"
fi

# Check environment file exists
if [ ! -f ".env.$ENVIRONMENT" ]; then
    warning ".env.$ENVIRONMENT not found. Using .env"
    if [ ! -f ".env" ]; then
        error ".env file not found!"
    fi
else
    info "Using .env.$ENVIRONMENT"
    cp ".env.$ENVIRONMENT" .env
fi

# Maintenance mode
info "Putting application in maintenance mode..."
php artisan down || warning "Could not enable maintenance mode"

# Backup database
if [ "$ENVIRONMENT" == "production" ]; then
    info "Creating database backup..."
    mkdir -p storage/backups
    php artisan backup:run || warning "Backup failed"
fi

# Pull latest code
info "Pulling latest code from repository..."
git pull origin main || error "Git pull failed"

# Install/update dependencies
info "Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction || error "Composer install failed"

info "Installing NPM dependencies..."
npm ci --production || error "NPM install failed"

# Build assets
info "Building frontend assets..."
npm run build || error "Asset build failed"

# Run database migrations
info "Running database migrations..."
php artisan migrate --force || error "Migration failed"

# Clear and cache configurations
info "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

info "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link storage (if not already linked)
info "Linking storage directory..."
php artisan storage:link || warning "Storage link already exists"

# Restart queue workers
if command -v supervisorctl &> /dev/null; then
    info "Restarting queue workers..."
    sudo supervisorctl restart faculty-portfolio-worker:* || warning "Could not restart workers"
fi

# Clear application cache
info "Clearing application cache..."
php artisan cache:clear

# Bring application back up
info "Bringing application back online..."
php artisan up

# Run health check
info "Running health check..."
if [ "$ENVIRONMENT" == "production" ]; then
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" https://localhost/health || echo "000")
else
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8081/health || echo "000")
fi

if [ "$HTTP_CODE" == "200" ]; then
    info "Health check passed ✓"
else
    warning "Health check returned: $HTTP_CODE"
fi

# Deployment summary
echo ""
echo "========================================="
echo -e "${GREEN}Deployment Completed Successfully!${NC}"
echo "========================================="
echo "Environment: $ENVIRONMENT"
echo "Deployed at: $(date)"
echo "Git commit: $(git rev-parse --short HEAD)"
echo "Git branch: $(git rev-parse --abbrev-ref HEAD)"
echo ""

# Show recent logs
info "Recent logs (last 10 lines):"
tail -n 10 storage/logs/laravel.log || warning "Could not read logs"

echo ""
info "Deployment script completed!"
