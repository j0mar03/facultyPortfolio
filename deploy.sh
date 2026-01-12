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

echo -e "${YELLOW}📦 Installing/updating Composer dependencies...${NC}"
docker compose exec -T app composer install --no-dev --optimize-autoloader

echo -e "${YELLOW}📦 Installing/updating NPM dependencies...${NC}"
docker compose exec --user root -T app npm install

echo -e "${YELLOW}🏗️  Building frontend assets...${NC}"
docker compose exec --user root -T app npm run build

echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
docker compose exec -T app php artisan migrate --force

echo -e "${YELLOW}🧹 Clearing Laravel caches...${NC}"
docker compose exec -T app php artisan config:clear
docker compose exec -T app php artisan cache:clear
docker compose exec -T app php artisan route:clear
docker compose exec -T app php artisan view:clear

echo -e "${YELLOW}🔄 Restarting containers...${NC}"
docker compose restart app web

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo -e "${GREEN}🌐 Your app should now be updated at https://portfolio.itechportfolio.xyz${NC}"
