#!/bin/bash

# Nextcloud Setup Script
# This script sets up Nextcloud on the VPS using the existing MySQL server
# and organizes it in a separate directory structure

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

# Services directory
SERVICES_DIR="/opt/services"
NEXTCLOUD_DIR="$SERVICES_DIR/nextcloud"

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

# Create Nextcloud directory
mkdir -p "$NEXTCLOUD_DIR"
cd "$NEXTCLOUD_DIR"

# Check if docker-compose.nextcloud.yml exists in project, copy it
if [ -f "$PROJECT_DIR/docker-compose.nextcloud.yml" ]; then
    cp "$PROJECT_DIR/docker-compose.nextcloud.yml" "$NEXTCLOUD_DIR/docker-compose.yml"
    echo -e "${GREEN}✅ Copied docker-compose.yml to Nextcloud directory${NC}"
else
    echo -e "${RED}❌ docker-compose.nextcloud.yml not found in project!${NC}"
    exit 1
fi

# Check if MySQL setup script has been run (Docker MySQL)
echo -e "${YELLOW}📋 Checking Docker MySQL setup...${NC}"

# Check if Faculty Portfolio MySQL container is running
if ! docker ps | grep -q "facultyportfolio-db"; then
    echo -e "${RED}❌ Faculty Portfolio MySQL container is not running!${NC}"
    echo -e "${YELLOW}   Please start Faculty Portfolio first:${NC}"
    echo -e "${YELLOW}   cd ~/facultyPortfolio && docker compose up -d${NC}"
    exit 1
fi

# Check if databases exist in Docker MySQL
MYSQL_CONTAINER="facultyportfolio-db"
DB_EXISTS=$(docker exec "$MYSQL_CONTAINER" mysql -uroot -proot -e "SHOW DATABASES LIKE 'nextcloud';" 2>/dev/null | grep -q "nextcloud" && echo "yes" || echo "no")

if [ "$DB_EXISTS" != "yes" ]; then
    echo -e "${YELLOW}⚠️  Nextcloud database not found. Running Docker MySQL setup...${NC}"
    bash "$SCRIPT_DIR/setup-docker-mysql.sh"
    
    # Verify database was created
    sleep 2
    DB_EXISTS=$(docker exec "$MYSQL_CONTAINER" mysql -uroot -proot -e "SHOW DATABASES LIKE 'nextcloud';" 2>/dev/null | grep -q "nextcloud" && echo "yes" || echo "no")
    if [ "$DB_EXISTS" != "yes" ]; then
        echo -e "${RED}❌ Failed to create Nextcloud database. Please run setup-docker-mysql.sh manually.${NC}"
        exit 1
    fi
fi

echo -e "${GREEN}✅ Nextcloud database exists${NC}"

# Prompt for configuration
echo ""
echo -e "${YELLOW}📝 Nextcloud Configuration${NC}"
read -p "Nextcloud admin username [admin]: " NEXTCLOUD_ADMIN_USER
NEXTCLOUD_ADMIN_USER=${NEXTCLOUD_ADMIN_USER:-admin}

read -sp "Nextcloud admin password: " NEXTCLOUD_ADMIN_PASSWORD
echo ""

read -p "Nextcloud subdomain [opcr.itechportfolio.xyz]: " NEXTCLOUD_HOST
NEXTCLOUD_HOST=${NEXTCLOUD_HOST:-opcr.itechportfolio.xyz}

read -p "Nextcloud protocol [https]: " NEXTCLOUD_PROTOCOL
NEXTCLOUD_PROTOCOL=${NEXTCLOUD_PROTOCOL:-https}

read -sp "Nextcloud database password: " NEXTCLOUD_DB_PASSWORD
echo ""

# Docker MySQL root password is 'root' by default
MYSQL_ROOT_PASSWORD="root"
echo -e "${YELLOW}   Using Docker MySQL root password: root${NC}"

read -sp "Redis password [optional, press Enter to skip]: " REDIS_PASSWORD
echo ""

# Create .env file for Nextcloud
cat > "$NEXTCLOUD_DIR/.env" <<EOF
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

chmod 600 "$NEXTCLOUD_DIR/.env"
echo -e "${GREEN}✅ Created .env file${NC}"

# Load environment variables
export $(cat "$NEXTCLOUD_DIR/.env" | grep -v '^#' | xargs)

# Check Docker network configuration
echo -e "${YELLOW}🔍 Checking Docker network configuration...${NC}"

# Detect the actual Docker network name
MYSQL_CONTAINER="facultyportfolio-db"
MYSQL_NETWORK=$(docker inspect "$MYSQL_CONTAINER" --format '{{range $key, $value := .NetworkSettings.Networks}}{{$key}}{{end}}' | head -1)

if [ -z "$MYSQL_NETWORK" ]; then
    echo -e "${RED}❌ Could not detect Docker network${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Detected MySQL network: $MYSQL_NETWORK${NC}"

# Fix network configuration in docker-compose files
echo -e "${YELLOW}🔧 Updating docker-compose.yml network configuration...${NC}"
bash "$SCRIPT_DIR/fix-docker-networks.sh"

# Start Nextcloud containers
echo -e "${YELLOW}🐳 Starting Nextcloud containers...${NC}"
cd "$NEXTCLOUD_DIR"
docker compose --env-file .env up -d

# Wait for Nextcloud to be ready
echo -e "${YELLOW}⏳ Waiting for Nextcloud to be ready...${NC}"
sleep 10

# Check if Nextcloud is running
if docker ps | grep -q nextcloud; then
    echo -e "${GREEN}✅ Nextcloud is running!${NC}"
    echo ""
    echo -e "${GREEN}📋 Nextcloud Information:${NC}"
    echo -e "  - Directory: $NEXTCLOUD_DIR"
    echo -e "  - URL: ${NEXTCLOUD_PROTOCOL}://${NEXTCLOUD_HOST}"
    echo -e "  - Admin User: ${NEXTCLOUD_ADMIN_USER}"
    echo -e "  - Database: nextcloud (on Docker MySQL: facultyportfolio-db)"
    echo ""
    echo -e "${YELLOW}📝 Next Steps:${NC}"
    echo -e "  1. Configure Nginx reverse proxy (see scripts/nginx/nextcloud.conf)"
    echo -e "  2. Set up SSL certificate for ${NEXTCLOUD_HOST}"
    echo -e "  3. Access Nextcloud at ${NEXTCLOUD_PROTOCOL}://${NEXTCLOUD_HOST}"
    echo -e "  4. Log in with admin credentials"
    echo -e "  5. Install Calendar app from Apps menu"
else
    echo -e "${RED}❌ Failed to start Nextcloud. Check logs with:${NC}"
    echo -e "   cd $NEXTCLOUD_DIR && docker compose logs"
    exit 1
fi
