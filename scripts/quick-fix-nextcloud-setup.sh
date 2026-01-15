#!/bin/bash

# Quick Fix for Nextcloud Setup with Docker MySQL
# Run this if setup-nextcloud.sh fails

set -e

echo "🔧 Quick Fix: Nextcloud Setup with Docker MySQL"
echo "=============================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Check if Faculty Portfolio MySQL container is running
echo -e "${BLUE}Step 1: Checking Faculty Portfolio MySQL...${NC}"

if ! docker ps | grep -q "facultyportfolio-db"; then
    echo -e "${RED}❌ Faculty Portfolio MySQL container is not running!${NC}"
    echo -e "${YELLOW}   Starting Faculty Portfolio...${NC}"
    cd ~/facultyPortfolio
    docker compose up -d db
    sleep 5
fi

if docker ps | grep -q "facultyportfolio-db"; then
    echo -e "${GREEN}✅ Faculty Portfolio MySQL is running${NC}"
else
    echo -e "${RED}❌ Failed to start MySQL container${NC}"
    exit 1
fi

# Step 2: Create databases
echo ""
echo -e "${BLUE}Step 2: Creating databases in Docker MySQL...${NC}"

MYSQL_CONTAINER="facultyportfolio-db"

# Check if databases already exist
NEXTCLOUD_EXISTS=$(docker exec "$MYSQL_CONTAINER" mysql -uroot -proot -e "SHOW DATABASES LIKE 'nextcloud';" 2>/dev/null | grep -q "nextcloud" && echo "yes" || echo "no")
SNIPEIT_EXISTS=$(docker exec "$MYSQL_CONTAINER" mysql -uroot -proot -e "SHOW DATABASES LIKE 'snipeit';" 2>/dev/null | grep -q "snipeit" && echo "yes" || echo "no")

if [ "$NEXTCLOUD_EXISTS" = "yes" ] && [ "$SNIPEIT_EXISTS" = "yes" ]; then
    echo -e "${GREEN}✅ Databases already exist${NC}"
else
    echo -e "${YELLOW}   Creating databases...${NC}"
    
    read -sp "Enter password for Nextcloud database user: " NEXTCLOUD_DB_PASSWORD
    echo ""
    read -sp "Enter password for Snipe-IT database user: " SNIPEIT_DB_PASSWORD
    echo ""
    
    docker exec -i "$MYSQL_CONTAINER" mysql -uroot -proot <<EOF
CREATE DATABASE IF NOT EXISTS nextcloud CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER IF NOT EXISTS 'nextcloud_user'@'%' IDENTIFIED BY '${NEXTCLOUD_DB_PASSWORD}';
GRANT ALL PRIVILEGES ON nextcloud.* TO 'nextcloud_user'@'%';

CREATE DATABASE IF NOT EXISTS snipeit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'snipeit_user'@'%' IDENTIFIED BY '${SNIPEIT_DB_PASSWORD}';
GRANT ALL PRIVILEGES ON snipeit.* TO 'snipeit_user'@'%';

FLUSH PRIVILEGES;
EOF
    
    echo -e "${GREEN}✅ Databases created${NC}"
fi

# Step 3: Fix Docker networks
echo ""
echo -e "${BLUE}Step 3: Fixing Docker network configuration...${NC}"

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
if [ -f "$SCRIPT_DIR/fix-docker-networks.sh" ]; then
    bash "$SCRIPT_DIR/fix-docker-networks.sh"
else
    echo -e "${YELLOW}⚠️  Network fix script not found, continuing...${NC}"
fi

echo ""
echo -e "${GREEN}✅ Quick fix complete!${NC}"
echo ""
echo -e "${YELLOW}Now you can run:${NC}"
echo -e "   sudo bash scripts/setup-nextcloud.sh"
