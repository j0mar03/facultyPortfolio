#!/bin/bash

# Faculty Portfolio Deployment Script
# Run this script on your VPS after pushing changes from local

set -e  # Exit on error

echo "🚀 Starting deployment..."

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo -e "${YELLOW}📥 Pulling latest changes from Git...${NC}"
git pull

if [ ! -f ".env" ]; then
    echo -e "${RED}❌ .env file not found. Create .env before deploying.${NC}"
    exit 1
fi

DB_PASSWORD_VALUE=$(grep -E '^DB_PASSWORD=' .env | tail -n1 | cut -d '=' -f2-)
DB_ROOT_PASSWORD_VALUE=$(grep -E '^DB_ROOT_PASSWORD=' .env | tail -n1 | cut -d '=' -f2-)
DB_DATABASE_VALUE=$(grep -E '^DB_DATABASE=' .env | tail -n1 | cut -d '=' -f2-)
if [[ "$DB_PASSWORD_VALUE" =~ ^(faculty|root|password|replace_with_strong_password|change-me)?$ ]] || [[ "$DB_ROOT_PASSWORD_VALUE" =~ ^(root|password|replace_with_strong_root_password|change-root-password)?$ ]]; then
    echo -e "${RED}❌ Weak DB credentials detected in .env. Set strong DB_PASSWORD and DB_ROOT_PASSWORD, then retry.${NC}"
    exit 1
fi

PORTFOLIO_DOMAIN_VALUE=$(grep -E '^PORTFOLIO_DOMAIN=' .env | tail -n1 | cut -d '=' -f2-)
CERT_MODE_VALUE=$(grep -E '^CERT_MODE=' .env | tail -n1 | cut -d '=' -f2-)
CERTBOT_EMAIL_VALUE=$(grep -E '^CERTBOT_EMAIL=' .env | tail -n1 | cut -d '=' -f2-)
SETUP_NGINX_FULL_STRICT_VALUE=$(grep -E '^SETUP_NGINX_FULL_STRICT=' .env | tail -n1 | cut -d '=' -f2-)
RUN_DB_MIGRATIONS_VALUE=$(grep -E '^RUN_DB_MIGRATIONS=' .env | tail -n1 | cut -d '=' -f2-)
BACKUP_BEFORE_MIGRATE_VALUE=$(grep -E '^BACKUP_BEFORE_MIGRATE=' .env | tail -n1 | cut -d '=' -f2-)
RESTART_APP_AFTER_DEPLOY_VALUE=$(grep -E '^RESTART_APP_AFTER_DEPLOY=' .env | tail -n1 | cut -d '=' -f2-)

PORTFOLIO_DOMAIN_VALUE="${PORTFOLIO_DOMAIN_VALUE:-portfolio.itechportfolio.xyz}"
CERT_MODE_VALUE="${CERT_MODE_VALUE:-letsencrypt}"
SETUP_NGINX_FULL_STRICT_VALUE="${SETUP_NGINX_FULL_STRICT_VALUE:-0}"
RUN_DB_MIGRATIONS_VALUE="${RUN_DB_MIGRATIONS_VALUE:-0}"
BACKUP_BEFORE_MIGRATE_VALUE="${BACKUP_BEFORE_MIGRATE_VALUE:-1}"
RESTART_APP_AFTER_DEPLOY_VALUE="${RESTART_APP_AFTER_DEPLOY_VALUE:-0}"
DB_DATABASE_VALUE="${DB_DATABASE_VALUE:-faculty_portfolio}"

echo -e "${YELLOW}🐳 Ensuring application containers are up-to-date...${NC}"
docker compose up -d --build app web

echo -e "${YELLOW}🐳 Ensuring database container is running (no restart)...${NC}"
docker compose up -d db

echo -e "${YELLOW}📦 Installing/updating Composer dependencies...${NC}"
docker compose exec -T app composer install --no-dev --optimize-autoloader

echo -e "${YELLOW}📦 Installing/updating NPM dependencies...${NC}"
docker compose exec --user root -T app npm install

echo -e "${YELLOW}🏗️  Building frontend assets...${NC}"
docker compose exec --user root -T app npm run build

if [ "$RUN_DB_MIGRATIONS_VALUE" = "1" ]; then
    if [ "$BACKUP_BEFORE_MIGRATE_VALUE" = "1" ]; then
        echo -e "${YELLOW}💾 Creating pre-migration backup...${NC}"
        mkdir -p backups
        BACKUP_FILE="backups/faculty_portfolio_$(date +%Y%m%d_%H%M%S).sql"
        docker compose exec -T db sh -c "exec mysqldump -uroot -p\"$DB_ROOT_PASSWORD_VALUE\" \"$DB_DATABASE_VALUE\"" > "$BACKUP_FILE"
        echo -e "${GREEN}✅ Backup created: $BACKUP_FILE${NC}"
    fi

    echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
    docker compose exec -T app php artisan migrate --force
else
    echo -e "${YELLOW}⏭️  Skipping migrations (RUN_DB_MIGRATIONS=0).${NC}"
fi

echo -e "${YELLOW}🧹 Clearing Laravel caches...${NC}"
docker compose exec -T app php artisan config:clear
docker compose exec -T app php artisan cache:clear
docker compose exec -T app php artisan route:clear
docker compose exec -T app php artisan view:clear

if [ "$RESTART_APP_AFTER_DEPLOY_VALUE" = "1" ]; then
    echo -e "${YELLOW}🔄 Restarting app/web containers...${NC}"
    docker compose restart app web
else
    echo -e "${YELLOW}⏭️  Skipping app/web restart (RESTART_APP_AFTER_DEPLOY=0).${NC}"
fi

if [ "$SETUP_NGINX_FULL_STRICT_VALUE" = "1" ]; then
    echo -e "${YELLOW}🔐 Configuring host Nginx for Cloudflare Full (strict)...${NC}"
    PORTFOLIO_DOMAIN="$PORTFOLIO_DOMAIN_VALUE" \
    CERT_MODE="$CERT_MODE_VALUE" \
    CERTBOT_EMAIL="$CERTBOT_EMAIL_VALUE" \
    bash scripts/setup-portfolio-full-strict.sh
fi

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo -e "${GREEN}🌐 Your app should now be updated at https://portfolio.itechportfolio.xyz${NC}"
echo -e "${YELLOW}Cloudflare final step:${NC} set SSL/TLS mode to Full (strict) in dashboard."
