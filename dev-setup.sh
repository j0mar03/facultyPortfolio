#!/bin/bash

# Local Development Setup Script
# Run this after pulling changes or when setting up locally

set -e

echo "🔧 Setting up local development environment..."

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

cd "$(dirname "$0")"

echo -e "${YELLOW}📦 Installing Composer dependencies...${NC}"
docker compose exec -T app composer install

echo -e "${YELLOW}📦 Installing NPM dependencies...${NC}"
docker compose exec -T app npm install

echo -e "${YELLOW}🏗️  Building frontend assets (dev mode)...${NC}"
docker compose exec -T app npm run build

echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
docker compose exec -T app php artisan migrate

echo -e "${YELLOW}🧹 Clearing Laravel caches...${NC}"
docker compose exec -T app php artisan config:clear
docker compose exec -T app php artisan cache:clear

echo -e "${GREEN}✅ Local development environment ready!${NC}"
echo -e "${GREEN}🌐 Access your app at http://localhost:8081${NC}"
