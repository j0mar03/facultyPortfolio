#!/bin/bash

# Nextcloud Setup Script
# This script sets up Nextcloud on the VPS using the existing MySQL server

set -e

echo "🚀 Setting up Nextcloud Calendar..."

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_DIR"

# Check if docker-compose.nextcloud.yml exists
if [ ! -f "docker-compose.nextcloud.yml" ]; then
    echo -e "${RED}❌ docker-compose.nextcloud.yml not found!${NC}"
    exit 1
fi

# Check if MySQL setup script has been run
echo -e "${YELLOW}📋 Checking MySQL setup...${NC}"
if ! mysql -u root -e "USE nextcloud;" 2>/dev/null; then
    echo -e "${YELLOW}⚠️  Nextcloud database not found. Running database setup...${NC}"
    bash "$SCRIPT_DIR/setup-nextcloud-db.sh"
fi

# Prompt for configuration
echo ""
echo -e "${YELLOW}📝 Nextcloud Configuration${NC}"
read -p "Nextcloud admin username [admin]: " NEXTCLOUD_ADMIN_USER
NEXTCLOUD_ADMIN_USER=${NEXTCLOUD_ADMIN_USER:-admin}

read -sp "Nextcloud admin password: " NEXTCLOUD_ADMIN_PASSWORD
echo ""

read -p "Nextcloud domain/host [localhost]: " NEXTCLOUD_HOST
NEXTCLOUD_HOST=${NEXTCLOUD_HOST:-localhost}

read -p "Nextcloud protocol [http/https]: " NEXTCLOUD_PROTOCOL
NEXTCLOUD_PROTOCOL=${NEXTCLOUD_PROTOCOL:-http}

read -sp "Nextcloud database password: " NEXTCLOUD_DB_PASSWORD
echo ""

read -sp "MySQL root password: " MYSQL_ROOT_PASSWORD
echo ""

read -sp "Redis password [optional, press Enter to skip]: " REDIS_PASSWORD
echo ""

# Create .env file for Nextcloud if it doesn't exist
if [ ! -f ".env.nextcloud" ]; then
    cat > .env.nextcloud <<EOF
# Nextcloud Configuration
NEXTCLOUD_ADMIN_USER=${NEXTCLOUD_ADMIN_USER}
NEXTCLOUD_ADMIN_PASSWORD=${NEXTCLOUD_ADMIN_PASSWORD}
NEXTCLOUD_HOST=${NEXTCLOUD_HOST}
NEXTCLOUD_PROTOCOL=${NEXTCLOUD_PROTOCOL}
NEXTCLOUD_TRUSTED_DOMAINS=${NEXTCLOUD_HOST} localhost
NEXTCLOUD_DB_PASSWORD=${NEXTCLOUD_DB_PASSWORD}
MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
REDIS_PASSWORD=${REDIS_PASSWORD}
EOF
    echo -e "${GREEN}✅ Created .env.nextcloud file${NC}"
else
    echo -e "${YELLOW}⚠️  .env.nextcloud already exists. Updating values...${NC}"
    # Update values in .env.nextcloud
    sed -i "s/^NEXTCLOUD_ADMIN_USER=.*/NEXTCLOUD_ADMIN_USER=${NEXTCLOUD_ADMIN_USER}/" .env.nextcloud
    sed -i "s/^NEXTCLOUD_ADMIN_PASSWORD=.*/NEXTCLOUD_ADMIN_PASSWORD=${NEXTCLOUD_ADMIN_PASSWORD}/" .env.nextcloud
    sed -i "s/^NEXTCLOUD_HOST=.*/NEXTCLOUD_HOST=${NEXTCLOUD_HOST}/" .env.nextcloud
    sed -i "s/^NEXTCLOUD_PROTOCOL=.*/NEXTCLOUD_PROTOCOL=${NEXTCLOUD_PROTOCOL}/" .env.nextcloud
    sed -i "s/^NEXTCLOUD_DB_PASSWORD=.*/NEXTCLOUD_DB_PASSWORD=${NEXTCLOUD_DB_PASSWORD}/" .env.nextcloud
fi

# Load environment variables
export $(cat .env.nextcloud | grep -v '^#' | xargs)

# Check MySQL bind-address configuration
echo -e "${YELLOW}🔍 Checking MySQL configuration...${NC}"
MYSQL_BIND=$(grep -E "^bind-address" /etc/mysql/mysql.conf.d/mysqld.cnf 2>/dev/null || echo "bind-address = 127.0.0.1")
if echo "$MYSQL_BIND" | grep -q "127.0.0.1"; then
    echo -e "${YELLOW}⚠️  MySQL is currently bound to 127.0.0.1. Docker containers need access.${NC}"
    echo -e "${YELLOW}   To allow Docker access, update /etc/mysql/mysql.conf.d/mysqld.cnf:${NC}"
    echo -e "${YELLOW}   Change 'bind-address = 127.0.0.1' to 'bind-address = 0.0.0.0'${NC}"
    echo -e "${YELLOW}   Then restart MySQL: sudo systemctl restart mysql${NC}"
    read -p "Have you updated MySQL bind-address? (y/n): " MYSQL_UPDATED
    if [ "$MYSQL_UPDATED" != "y" ]; then
        echo -e "${RED}❌ Please update MySQL configuration first.${NC}"
        exit 1
    fi
fi

# Start Nextcloud containers
echo -e "${YELLOW}🐳 Starting Nextcloud containers...${NC}"
docker compose -f docker-compose.nextcloud.yml --env-file .env.nextcloud up -d

# Wait for Nextcloud to be ready
echo -e "${YELLOW}⏳ Waiting for Nextcloud to be ready...${NC}"
sleep 10

# Check if Nextcloud is running
if docker ps | grep -q nextcloud; then
    echo -e "${GREEN}✅ Nextcloud is running!${NC}"
    echo ""
    echo -e "${GREEN}📋 Nextcloud Information:${NC}"
    echo -e "  - URL: ${NEXTCLOUD_PROTOCOL}://${NEXTCLOUD_HOST}:8082"
    echo -e "  - Admin User: ${NEXTCLOUD_ADMIN_USER}"
    echo -e "  - Database: nextcloud (on host MySQL)"
    echo ""
    echo -e "${YELLOW}📝 Next Steps:${NC}"
    echo -e "  1. Access Nextcloud at ${NEXTCLOUD_PROTOCOL}://${NEXTCLOUD_HOST}:8082"
    echo -e "  2. Log in with admin credentials"
    echo -e "  3. Install Calendar app from Apps menu"
    echo -e "  4. Configure reverse proxy if needed (see NEXTCLOUD_SETUP.md)"
else
    echo -e "${RED}❌ Failed to start Nextcloud. Check logs with:${NC}"
    echo -e "   docker compose -f docker-compose.nextcloud.yml logs"
    exit 1
fi
