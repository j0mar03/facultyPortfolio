#!/bin/bash

# Setup Docker MySQL for Nextcloud and Snipe-IT
# This script uses the existing Faculty Portfolio Docker MySQL container
# No need to configure host MySQL - uses Docker networking!

set -e

echo "🐳 Setting up Docker MySQL for Nextcloud and Snipe-IT"
echo "======================================================"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Check if Faculty Portfolio MySQL container is running
echo -e "${BLUE}Step 1: Checking Faculty Portfolio MySQL container...${NC}"

if ! docker ps | grep -q "facultyportfolio-db"; then
    echo -e "${RED}❌ Faculty Portfolio MySQL container is not running!${NC}"
    echo -e "${YELLOW}   Please start Faculty Portfolio first:${NC}"
    echo -e "${YELLOW}   cd ~/facultyPortfolio && docker compose up -d${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Faculty Portfolio MySQL container is running${NC}"

# Get MySQL container name
MYSQL_CONTAINER="facultyportfolio-db"

# Get MySQL root password from Faculty Portfolio docker-compose
FACULTY_DIR="${HOME}/facultyPortfolio"
if [ ! -d "$FACULTY_DIR" ]; then
    FACULTY_DIR="/root/facultyPortfolio"
fi

if [ ! -d "$FACULTY_DIR" ]; then
    echo -e "${YELLOW}⚠️  Could not find Faculty Portfolio directory${NC}"
    read -p "Enter Faculty Portfolio directory path: " FACULTY_DIR
fi

# Check for docker-compose.yml
if [ ! -f "$FACULTY_DIR/docker-compose.yml" ]; then
    echo -e "${RED}❌ Could not find docker-compose.yml in $FACULTY_DIR${NC}"
    exit 1
fi

# Extract MySQL root password (default is 'root' based on docker-compose.yml)
MYSQL_ROOT_PASSWORD="root"
if grep -q "MYSQL_ROOT_PASSWORD" "$FACULTY_DIR/docker-compose.yml"; then
    # Try to get from .env if exists
    if [ -f "$FACULTY_DIR/.env" ]; then
        ENV_ROOT_PASS=$(grep "MYSQL_ROOT_PASSWORD" "$FACULTY_DIR/.env" | cut -d'=' -f2 | tr -d '"' | tr -d "'" || echo "")
        if [ -n "$ENV_ROOT_PASS" ]; then
            MYSQL_ROOT_PASSWORD="$ENV_ROOT_PASS"
        fi
    fi
fi

echo -e "${GREEN}✅ Found MySQL container: $MYSQL_CONTAINER${NC}"

# Detect the actual Docker network name
MYSQL_NETWORK=$(docker inspect "$MYSQL_CONTAINER" --format '{{range $key, $value := .NetworkSettings.Networks}}{{$key}}{{end}}' | head -1)
if [ -z "$MYSQL_NETWORK" ]; then
    # Fallback to common naming convention
    MYSQL_NETWORK="facultyportfolio_default"
fi

echo -e "${GREEN}✅ MySQL network: $MYSQL_NETWORK${NC}"

# Prompt for database passwords
echo ""
echo -e "${BLUE}Step 2: Database Configuration${NC}"
read -sp "Enter password for Nextcloud database user: " NEXTCLOUD_DB_PASSWORD
echo ""
read -sp "Enter password for Snipe-IT database user: " SNIPEIT_DB_PASSWORD
echo ""

# Create databases and users
echo ""
echo -e "${BLUE}Step 3: Creating databases and users...${NC}"

# Create Nextcloud database and user
docker exec -i "$MYSQL_CONTAINER" mysql -uroot -p"$MYSQL_ROOT_PASSWORD" <<EOF
CREATE DATABASE IF NOT EXISTS nextcloud CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER IF NOT EXISTS 'nextcloud_user'@'%' IDENTIFIED BY '${NEXTCLOUD_DB_PASSWORD}';
GRANT ALL PRIVILEGES ON nextcloud.* TO 'nextcloud_user'@'%';

CREATE DATABASE IF NOT EXISTS snipeit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'snipeit_user'@'%' IDENTIFIED BY '${SNIPEIT_DB_PASSWORD}';
GRANT ALL PRIVILEGES ON snipeit.* TO 'snipeit_user'@'%';

FLUSH PRIVILEGES;

-- Show created databases
SHOW DATABASES LIKE 'nextcloud';
SHOW DATABASES LIKE 'snipeit';
EOF

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Databases and users created successfully!${NC}"
else
    echo -e "${RED}❌ Failed to create databases. Check MySQL root password.${NC}"
    echo -e "${YELLOW}   Default root password is 'root' (from docker-compose.yml)${NC}"
    exit 1
fi

# Save passwords to a secure file for reference
PASSWORDS_FILE="/opt/services/.db-passwords.txt"
mkdir -p /opt/services
cat > "$PASSWORDS_FILE" <<EOF
# Database Passwords (Keep Secure!)
# Created: $(date)

Nextcloud:
  Database: nextcloud
  User: nextcloud_user
  Password: ${NEXTCLOUD_DB_PASSWORD}

Snipe-IT:
  Database: snipeit
  User: snipeit_user
  Password: ${SNIPEIT_DB_PASSWORD}

MySQL Root (Docker):
  Container: ${MYSQL_CONTAINER}
  Password: ${MYSQL_ROOT_PASSWORD}
EOF

chmod 600 "$PASSWORDS_FILE"
echo -e "${GREEN}✅ Passwords saved to: $PASSWORDS_FILE${NC}"

echo ""
echo -e "${GREEN}✅ Docker MySQL setup complete!${NC}"
echo ""
echo -e "${BLUE}Summary:${NC}"
echo -e "  ✅ Using Faculty Portfolio MySQL container: $MYSQL_CONTAINER"
echo -e "  ✅ Nextcloud database: nextcloud"
echo -e "  ✅ Snipe-IT database: snipeit"
echo -e "  ✅ No host MySQL configuration needed!"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo -e "  1. Nextcloud docker-compose.yml already configured to use: MYSQL_HOST=facultyportfolio-db"
echo -e "  2. Snipe-IT docker-compose.yml already configured to use: DB_HOST=facultyportfolio-db"
echo -e "  3. Network name detected: $MYSQL_NETWORK"
echo -e "  4. Make sure docker-compose files reference network: $MYSQL_NETWORK"
