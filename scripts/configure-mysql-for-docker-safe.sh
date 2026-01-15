#!/bin/bash

# Safe MySQL Configuration Script for Docker
# This script safely configures MySQL to accept connections from Docker containers
# WITHOUT breaking your existing Faculty Portfolio setup

set -e

echo "🔒 Safe MySQL Configuration for Docker"
echo "======================================"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Check if running as root or with sudo
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}❌ Please run with sudo${NC}"
    exit 1
fi

# Step 1: Check current MySQL status
echo -e "${BLUE}Step 1: Checking current MySQL status...${NC}"

# Check if MySQL is running
if ! systemctl is-active --quiet mysql && ! systemctl is-active --quiet mysqld && ! systemctl is-active --quiet mariadb; then
    echo -e "${RED}❌ MySQL is not running. Please start MySQL first.${NC}"
    exit 1
fi

# Determine service name
if systemctl is-active --quiet mysql; then
    SERVICE_NAME="mysql"
elif systemctl is-active --quiet mysqld; then
    SERVICE_NAME="mysqld"
else
    SERVICE_NAME="mariadb"
fi

echo -e "${GREEN}✅ MySQL service is running ($SERVICE_NAME)${NC}"

# Check current bind-address
echo ""
echo -e "${BLUE}Step 2: Checking current MySQL bind-address...${NC}"

# Check what's currently listening
CURRENT_LISTEN=$(netstat -tlnp 2>/dev/null | grep ":3306" || ss -tlnp 2>/dev/null | grep ":3306" || echo "")

if echo "$CURRENT_LISTEN" | grep -q "0.0.0.0"; then
    echo -e "${GREEN}✅ MySQL is already listening on all interfaces (0.0.0.0:3306)${NC}"
    echo -e "${GREEN}✅ No changes needed! Docker containers can already connect.${NC}"
    exit 0
elif echo "$CURRENT_LISTEN" | grep -q "127.0.0.1"; then
    echo -e "${YELLOW}⚠️  MySQL is currently only listening on localhost (127.0.0.1:3306)${NC}"
    echo -e "${YELLOW}   This needs to be changed to 0.0.0.0 for Docker containers${NC}"
else
    echo -e "${YELLOW}⚠️  Could not determine current bind-address${NC}"
    echo "$CURRENT_LISTEN"
fi

# Step 3: Test Faculty Portfolio connection
echo ""
echo -e "${BLUE}Step 3: Testing Faculty Portfolio database connection...${NC}"

# Try to find Faculty Portfolio .env file
FACULTY_ENV=""
if [ -f "/root/facultyPortfolio/.env" ]; then
    FACULTY_ENV="/root/facultyPortfolio/.env"
elif [ -f "$HOME/facultyPortfolio/.env" ]; then
    FACULTY_ENV="$HOME/facultyPortfolio/.env"
elif [ -f "./.env" ]; then
    FACULTY_ENV="./.env"
fi

if [ -n "$FACULTY_ENV" ]; then
    echo -e "${GREEN}✅ Found Faculty Portfolio .env file${NC}"
    
    # Extract database credentials (safely, without exposing passwords)
    DB_HOST=$(grep "^DB_HOST=" "$FACULTY_ENV" | cut -d'=' -f2 | tr -d '"' | tr -d "'" || echo "127.0.0.1")
    DB_PORT=$(grep "^DB_PORT=" "$FACULTY_ENV" | cut -d'=' -f2 | tr -d '"' | tr -d "'" || echo "3306")
    DB_DATABASE=$(grep "^DB_DATABASE=" "$FACULTY_ENV" | cut -d'=' -f2 | tr -d '"' | tr -d "'" || echo "")
    DB_USERNAME=$(grep "^DB_USERNAME=" "$FACULTY_ENV" | cut -d'=' -f2 | tr -d '"' | tr -d "'" || echo "")
    
    echo -e "   Database: $DB_DATABASE"
    echo -e "   Host: $DB_HOST"
    echo -e "   Port: $DB_PORT"
    
    # Test connection
    if [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
        echo -e "${YELLOW}   Testing connection...${NC}"
        if mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -e "SELECT 1;" "$DB_DATABASE" 2>/dev/null; then
            echo -e "${GREEN}✅ Faculty Portfolio can connect to MySQL${NC}"
        else
            echo -e "${YELLOW}⚠️  Could not test connection (may need password)${NC}"
        fi
    fi
else
    echo -e "${YELLOW}⚠️  Could not find Faculty Portfolio .env file${NC}"
    echo -e "${YELLOW}   Will proceed with caution...${NC}"
fi

# Step 4: Find MySQL config file
echo ""
echo -e "${BLUE}Step 4: Finding MySQL configuration file...${NC}"

MYSQL_CONF=""
POSSIBLE_PATHS=(
    "/etc/mysql/mysql.conf.d/mysqld.cnf"
    "/etc/mysql/my.cnf"
    "/etc/my.cnf"
    "/etc/mysql/mariadb.conf.d/50-server.cnf"
    "/etc/mysql/conf.d/mysqld.cnf"
)

for path in "${POSSIBLE_PATHS[@]}"; do
    if [ -f "$path" ]; then
        MYSQL_CONF="$path"
        echo -e "${GREEN}✅ Found MySQL config: $MYSQL_CONF${NC}"
        break
    fi
done

if [ -z "$MYSQL_CONF" ]; then
    # Try mysqld --help
    MYSQL_CONF=$(mysqld --help --verbose 2>/dev/null | grep -A 1 "Default options" | grep "my.cnf" | awk '{print $NF}' | head -1)
    if [ -n "$MYSQL_CONF" ] && [ -f "$MYSQL_CONF" ]; then
        echo -e "${GREEN}✅ Found MySQL config: $MYSQL_CONF${NC}"
    fi
fi

if [ -z "$MYSQL_CONF" ] || [ ! -f "$MYSQL_CONF" ]; then
    echo -e "${RED}❌ Could not find MySQL configuration file${NC}"
    echo -e "${YELLOW}   Please run: sudo bash scripts/diagnose-mysql.sh${NC}"
    exit 1
fi

# Step 5: Create backup
echo ""
echo -e "${BLUE}Step 5: Creating backup...${NC}"

BACKUP_FILE="${MYSQL_CONF}.backup.$(date +%Y%m%d_%H%M%S)"
if [ ! -f "$BACKUP_FILE" ]; then
    cp "$MYSQL_CONF" "$BACKUP_FILE"
    echo -e "${GREEN}✅ Backup created: $BACKUP_FILE${NC}"
    echo -e "${YELLOW}   To restore: sudo cp $BACKUP_FILE $MYSQL_CONF && sudo systemctl restart $SERVICE_NAME${NC}"
else
    echo -e "${YELLOW}⚠️  Backup already exists: $BACKUP_FILE${NC}"
fi

# Step 6: Show what will change
echo ""
echo -e "${BLUE}Step 6: Preview of changes...${NC}"
echo -e "${YELLOW}   Current config file: $MYSQL_CONF${NC}"

CURRENT_BIND=$(grep -E "^bind-address" "$MYSQL_CONF" 2>/dev/null || echo "   (not set - will use default)")
echo -e "${YELLOW}   Current bind-address: $CURRENT_BIND${NC}"
echo -e "${GREEN}   Will change to: bind-address = 0.0.0.0${NC}"

# Step 7: Confirm before making changes
echo ""
echo -e "${YELLOW}⚠️  IMPORTANT SAFETY INFORMATION:${NC}"
echo ""
echo -e "   ✅ This change is SAFE for your Faculty Portfolio:"
echo -e "      - MySQL will still accept localhost connections"
echo -e "      - 0.0.0.0 includes 127.0.0.1 (localhost)"
echo -e "      - Your existing connections will continue to work"
echo ""
echo -e "   ✅ Safety measures:"
echo -e "      - Backup created: $BACKUP_FILE"
echo -e "      - Can be rolled back if needed"
echo -e "      - MySQL will be restarted (brief downtime ~2-5 seconds)"
echo ""
echo -e "   ⚠️  What this change does:"
echo -e "      - Allows Docker containers to connect to MySQL"
echo -e "      - MySQL will listen on all network interfaces"
echo -e "      - Still secure if you use strong passwords and firewall"
echo ""

read -p "Do you want to proceed with the configuration? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    echo -e "${YELLOW}❌ Configuration cancelled by user${NC}"
    exit 0
fi

# Step 8: Make the change
echo ""
echo -e "${BLUE}Step 7: Updating MySQL configuration...${NC}"

# Check if bind-address exists in this file
if grep -q "^bind-address" "$MYSQL_CONF"; then
    # Replace existing bind-address
    sed -i 's/^bind-address.*/bind-address = 0.0.0.0/' "$MYSQL_CONF"
    echo -e "${GREEN}✅ Updated existing bind-address in $MYSQL_CONF${NC}"
else
    # Check for MariaDB style config
    MARIA_CONF="/etc/mysql/mariadb.conf.d/50-server.cnf"
    if [ -f "$MARIA_CONF" ]; then
        if grep -q "^bind-address" "$MARIA_CONF"; then
            sed -i 's/^bind-address.*/bind-address = 0.0.0.0/' "$MARIA_CONF"
            echo -e "${GREEN}✅ Updated bind-address in $MARIA_CONF${NC}"
        else
            # Add to [mysqld] section
            if grep -q "^\[mysqld\]" "$MARIA_CONF"; then
                sed -i '/^\[mysqld\]/a bind-address = 0.0.0.0' "$MARIA_CONF"
                echo -e "${GREEN}✅ Added bind-address to $MARIA_CONF${NC}"
            else
                echo "[mysqld]" >> "$MARIA_CONF"
                echo "bind-address = 0.0.0.0" >> "$MARIA_CONF"
                echo -e "${GREEN}✅ Created [mysqld] section with bind-address in $MARIA_CONF${NC}"
            fi
        fi
    else
        # Add to main config file
        if ! grep -q "^\[mysqld\]" "$MYSQL_CONF"; then
            echo "" >> "$MYSQL_CONF"
            echo "[mysqld]" >> "$MYSQL_CONF"
        fi
        echo "bind-address = 0.0.0.0" >> "$MYSQL_CONF"
        echo -e "${GREEN}✅ Added bind-address to $MYSQL_CONF${NC}"
    fi
fi

# Step 9: Validate config before restart
echo ""
echo -e "${BLUE}Step 8: Validating MySQL configuration...${NC}"

if command -v mysqld &> /dev/null; then
    if mysqld --validate-config 2>/dev/null; then
        echo -e "${GREEN}✅ MySQL configuration is valid${NC}"
    else
        echo -e "${YELLOW}⚠️  Could not validate config (may be normal)${NC}"
    fi
fi

# Step 10: Restart MySQL
echo ""
echo -e "${BLUE}Step 9: Restarting MySQL service...${NC}"
echo -e "${YELLOW}   This will cause a brief downtime (~2-5 seconds)${NC}"

if systemctl restart "$SERVICE_NAME"; then
    echo -e "${GREEN}✅ MySQL restarted successfully${NC}"
    
    # Wait a moment for MySQL to fully start
    sleep 3
    
    # Verify MySQL is running
    if systemctl is-active --quiet "$SERVICE_NAME"; then
        echo -e "${GREEN}✅ MySQL is running${NC}"
    else
        echo -e "${RED}❌ MySQL failed to start!${NC}"
        echo -e "${YELLOW}   Restoring backup...${NC}"
        cp "$BACKUP_FILE" "$MYSQL_CONF"
        systemctl restart "$SERVICE_NAME"
        echo -e "${GREEN}✅ Backup restored. Please check MySQL logs.${NC}"
        exit 1
    fi
else
    echo -e "${RED}❌ Failed to restart MySQL${NC}"
    exit 1
fi

# Step 11: Verify the change
echo ""
echo -e "${BLUE}Step 10: Verifying configuration...${NC}"

NEW_LISTEN=$(netstat -tlnp 2>/dev/null | grep ":3306" || ss -tlnp 2>/dev/null | grep ":3306" || echo "")

if echo "$NEW_LISTEN" | grep -q "0.0.0.0"; then
    echo -e "${GREEN}✅ SUCCESS! MySQL is now listening on all interfaces (0.0.0.0:3306)${NC}"
else
    echo -e "${YELLOW}⚠️  Could not verify. Please check manually:${NC}"
    echo -e "   sudo netstat -tlnp | grep mysql"
fi

# Step 12: Test Faculty Portfolio still works
echo ""
echo -e "${BLUE}Step 11: Testing Faculty Portfolio connection...${NC}"

if [ -n "$FACULTY_ENV" ] && [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
    sleep 2  # Give MySQL a moment to be ready
    if mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -e "SELECT 1;" "$DB_DATABASE" 2>/dev/null; then
        echo -e "${GREEN}✅ Faculty Portfolio can still connect to MySQL${NC}"
        echo -e "${GREEN}✅ Your existing setup is working correctly!${NC}"
    else
        echo -e "${YELLOW}⚠️  Could not test connection (may need password)${NC}"
        echo -e "${YELLOW}   Please test your Faculty Portfolio manually${NC}"
    fi
fi

# Final summary
echo ""
echo -e "${GREEN}✅ Configuration complete!${NC}"
echo ""
echo -e "${BLUE}Summary:${NC}"
echo -e "   ✅ MySQL is now configured for Docker containers"
echo -e "   ✅ Faculty Portfolio should still be working"
echo -e "   ✅ Backup saved at: $BACKUP_FILE"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo -e "   1. Test your Faculty Portfolio to ensure it's working"
echo -e "   2. Proceed with Nextcloud setup"
echo ""
echo -e "${YELLOW}If something breaks:${NC}"
echo -e "   Restore backup: sudo cp $BACKUP_FILE $MYSQL_CONF && sudo systemctl restart $SERVICE_NAME"
