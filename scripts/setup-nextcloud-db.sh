#!/bin/bash

# Setup MySQL databases and users for Nextcloud and Snipe-IT
# This script prepares the existing MySQL server to be used by Nextcloud and Snipe-IT

set -e

echo "🔧 Setting up MySQL databases for Nextcloud and Snipe-IT..."

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Check if MySQL is running
if ! systemctl is-active --quiet mysql; then
    echo -e "${RED}❌ MySQL is not running. Please start MySQL first.${NC}"
    exit 1
fi

# Prompt for MySQL root password
read -sp "Enter MySQL root password: " MYSQL_ROOT_PASSWORD
echo ""

# Prompt for Nextcloud database password
read -sp "Enter password for Nextcloud database user: " NEXTCLOUD_DB_PASSWORD
echo ""

# Prompt for Snipe-IT database password
read -sp "Enter password for Snipe-IT database user: " SNIPEIT_DB_PASSWORD
echo ""

# Create MySQL script
MYSQL_SCRIPT=$(cat <<EOF
-- Create Nextcloud database
CREATE DATABASE IF NOT EXISTS nextcloud CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Create Nextcloud user
CREATE USER IF NOT EXISTS 'nextcloud_user'@'localhost' IDENTIFIED BY '${NEXTCLOUD_DB_PASSWORD}';
CREATE USER IF NOT EXISTS 'nextcloud_user'@'%' IDENTIFIED BY '${NEXTCLOUD_DB_PASSWORD}';

-- Grant privileges for Nextcloud
GRANT ALL PRIVILEGES ON nextcloud.* TO 'nextcloud_user'@'localhost';
GRANT ALL PRIVILEGES ON nextcloud.* TO 'nextcloud_user'@'%';

-- Create Snipe-IT database
CREATE DATABASE IF NOT EXISTS snipeit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create Snipe-IT user
CREATE USER IF NOT EXISTS 'snipeit_user'@'localhost' IDENTIFIED BY '${SNIPEIT_DB_PASSWORD}';
CREATE USER IF NOT EXISTS 'snipeit_user'@'%' IDENTIFIED BY '${SNIPEIT_DB_PASSWORD}';

-- Grant privileges for Snipe-IT
GRANT ALL PRIVILEGES ON snipeit.* TO 'snipeit_user'@'localhost';
GRANT ALL PRIVILEGES ON snipeit.* TO 'snipeit_user'@'%';

-- Flush privileges
FLUSH PRIVILEGES;

-- Show created databases
SHOW DATABASES LIKE 'nextcloud';
SHOW DATABASES LIKE 'snipeit';
EOF
)

# Execute MySQL script
echo -e "${YELLOW}📝 Creating databases and users...${NC}"
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<EOF
${MYSQL_SCRIPT}
EOF

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Databases and users created successfully!${NC}"
    echo ""
    echo -e "${GREEN}📋 Summary:${NC}"
    echo -e "  - Nextcloud database: nextcloud"
    echo -e "  - Nextcloud user: nextcloud_user"
    echo -e "  - Snipe-IT database: snipeit"
    echo -e "  - Snipe-IT user: snipeit_user"
    echo ""
    echo -e "${YELLOW}⚠️  Important:${NC}"
    echo -e "  1. Make sure MySQL is configured to accept connections from Docker containers"
    echo -e "  2. Update /etc/mysql/mysql.conf.d/mysqld.cnf and set bind-address = 0.0.0.0"
    echo -e "  3. Restart MySQL: sudo systemctl restart mysql"
    echo -e "  4. Save these passwords securely!"
else
    echo -e "${RED}❌ Failed to create databases. Please check MySQL root password.${NC}"
    exit 1
fi
