#!/bin/bash

# Snipe-IT Setup Script
# This script sets up Snipe-IT on the VPS using the existing MySQL server
# and organizes it in a separate directory structure

set -e

echo "🚀 Setting up Snipe-IT..."

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

# Services directory
SERVICES_DIR="/opt/services"
SNIPEIT_DIR="$SERVICES_DIR/snipeit"

# Check if running as root or with sudo
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}❌ Please run with sudo${NC}"
    exit 1
fi

# Create services directory structure if it doesn't exist
if [ ! -d "$SERVICES_DIR" ]; then
    echo -e "${YELLOW}📁 Creating services directory structure...${NC}"
    bash "$SCRIPT_DIR/setup-services-structure.sh"
fi

# Create Snipe-IT directory
mkdir -p "$SNIPEIT_DIR"
cd "$SNIPEIT_DIR"

# Check if docker-compose.snipeit.yml exists in project, copy it
if [ -f "$PROJECT_DIR/docker-compose.snipeit.yml" ]; then
    cp "$PROJECT_DIR/docker-compose.snipeit.yml" "$SNIPEIT_DIR/docker-compose.yml"
    echo -e "${GREEN}✅ Copied docker-compose.yml to Snipe-IT directory${NC}"
else
    echo -e "${RED}❌ docker-compose.snipeit.yml not found in project!${NC}"
    exit 1
fi

# Check if MySQL setup script has been run
echo -e "${YELLOW}📋 Checking MySQL setup...${NC}"
if ! mysql -u root -e "USE snipeit;" 2>/dev/null; then
    echo -e "${YELLOW}⚠️  Snipe-IT database not found. Running database setup...${NC}"
    bash "$SCRIPT_DIR/setup-nextcloud-db.sh"
fi

# Prompt for configuration
echo ""
echo -e "${YELLOW}📝 Snipe-IT Configuration${NC}"

read -p "Snipe-IT subdomain [asset.itechportfolio.xyz]: " SNIPEIT_HOST
SNIPEIT_HOST=${SNIPEIT_HOST:-asset.itechportfolio.xyz}

read -p "Snipe-IT protocol [https]: " SNIPEIT_PROTOCOL
SNIPEIT_PROTOCOL=${SNIPEIT_PROTOCOL:-https}

read -sp "Snipe-IT database password: " SNIPEIT_DB_PASSWORD
echo ""

read -sp "Redis password [optional, press Enter to skip]: " REDIS_PASSWORD
echo ""

read -p "Mail driver [smtp]: " MAIL_DRIVER
MAIL_DRIVER=${MAIL_DRIVER:-smtp}

read -p "Mail host [optional]: " MAIL_HOST

read -p "Mail port [587]: " MAIL_PORT
MAIL_PORT=${MAIL_PORT:-587}

read -p "Mail username [optional]: " MAIL_USERNAME

read -sp "Mail password [optional]: " MAIL_PASSWORD
echo ""

# Generate APP_KEY
APP_KEY=$(openssl rand -base64 32)

# Create .env file for Snipe-IT
cat > "$SNIPEIT_DIR/.env" <<EOF
# Snipe-IT Configuration
APP_ENV=production
APP_DEBUG=false
APP_KEY=${APP_KEY}
APP_URL=${SNIPEIT_PROTOCOL}://${SNIPEIT_HOST}

# Database Configuration (using host MySQL server)
DB_CONNECTION=mysql
DB_HOST=host.docker.internal
DB_PORT=3306
DB_DATABASE=snipeit
DB_USERNAME=snipeit_user
DB_PASSWORD=${SNIPEIT_DB_PASSWORD}
DB_PREFIX=

# Mail Configuration
MAIL_DRIVER=${MAIL_DRIVER}
MAIL_HOST=${MAIL_HOST}
MAIL_PORT=${MAIL_PORT}
MAIL_USERNAME=${MAIL_USERNAME}
MAIL_PASSWORD=${MAIL_PASSWORD}
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDR=noreply@itechportfolio.xyz
MAIL_FROM_NAME=Snipe-IT

# Cache/Session Configuration
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=${REDIS_PASSWORD}
SESSION_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
EOF

chmod 600 "$SNIPEIT_DIR/.env"
echo -e "${GREEN}✅ Created .env file${NC}"

# Load environment variables
export $(cat "$SNIPEIT_DIR/.env" | grep -v '^#' | xargs)

# Start Snipe-IT containers
echo -e "${YELLOW}🐳 Starting Snipe-IT containers...${NC}"
cd "$SNIPEIT_DIR"
docker compose --env-file .env up -d

# Wait for Snipe-IT to be ready
echo -e "${YELLOW}⏳ Waiting for Snipe-IT to be ready...${NC}"
sleep 15

# Run Snipe-IT setup
echo -e "${YELLOW}🔧 Running Snipe-IT setup...${NC}"
docker compose exec snipeit php artisan migrate --force || true
docker compose exec snipeit php artisan db:seed --class=DatabaseSeeder || true

# Check if Snipe-IT is running
if docker ps | grep -q snipeit; then
    echo -e "${GREEN}✅ Snipe-IT is running!${NC}"
    echo ""
    echo -e "${GREEN}📋 Snipe-IT Information:${NC}"
    echo -e "  - Directory: $SNIPEIT_DIR"
    echo -e "  - URL: ${SNIPEIT_PROTOCOL}://${SNIPEIT_HOST}"
    echo -e "  - Database: snipeit (on host MySQL)"
    echo ""
    echo -e "${YELLOW}📝 Next Steps:${NC}"
    echo -e "  1. Configure Nginx reverse proxy (see scripts/nginx/snipeit.conf)"
    echo -e "  2. Set up SSL certificate for ${SNIPEIT_HOST}"
    echo -e "  3. Access Snipe-IT at ${SNIPEIT_PROTOCOL}://${SNIPEIT_HOST}"
    echo -e "  4. Create admin user on first login"
else
    echo -e "${RED}❌ Failed to start Snipe-IT. Check logs with:${NC}"
    echo -e "   cd $SNIPEIT_DIR && docker compose logs"
    exit 1
fi
