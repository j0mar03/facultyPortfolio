#!/bin/bash

# Configure MySQL to accept connections from Docker containers
# This script updates MySQL bind-address to allow Docker containers to connect

set -e

echo "🔧 Configuring MySQL for Docker access..."

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Check if running as root or with sudo
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}❌ Please run as root or with sudo${NC}"
    exit 1
fi

# Find MySQL configuration file
MYSQL_CONF=""
POSSIBLE_PATHS=(
    "/etc/mysql/mysql.conf.d/mysqld.cnf"
    "/etc/mysql/my.cnf"
    "/etc/my.cnf"
    "/etc/mysql/mariadb.conf.d/50-server.cnf"
    "/etc/mysql/conf.d/mysqld.cnf"
)

echo -e "${YELLOW}🔍 Looking for MySQL configuration file...${NC}"
for path in "${POSSIBLE_PATHS[@]}"; do
    if [ -f "$path" ]; then
        MYSQL_CONF="$path"
        echo -e "${GREEN}✅ Found MySQL config: $MYSQL_CONF${NC}"
        break
    fi
done

# If still not found, try to find it using mysqld --help
if [ -z "$MYSQL_CONF" ]; then
    echo -e "${YELLOW}🔍 Searching for MySQL config using mysqld...${NC}"
    MYSQL_CONF=$(mysqld --help --verbose 2>/dev/null | grep -A 1 "Default options" | grep "my.cnf" | awk '{print $NF}' | head -1)
    
    # If found via mysqld, check if it exists
    if [ -n "$MYSQL_CONF" ] && [ ! -f "$MYSQL_CONF" ]; then
        MYSQL_CONF=""
    fi
fi

# If still not found, check common include directories
if [ -z "$MYSQL_CONF" ]; then
    echo -e "${YELLOW}🔍 Checking MySQL include directories...${NC}"
    if [ -d "/etc/mysql/mysql.conf.d" ]; then
        MYSQL_CONF="/etc/mysql/mysql.conf.d/mysqld.cnf"
        # Create the file if directory exists but file doesn't
        touch "$MYSQL_CONF"
        echo -e "${YELLOW}📝 Created MySQL config file: $MYSQL_CONF${NC}"
    elif [ -d "/etc/mysql" ]; then
        MYSQL_CONF="/etc/mysql/my.cnf"
        touch "$MYSQL_CONF"
        echo -e "${YELLOW}📝 Created MySQL config file: $MYSQL_CONF${NC}"
    fi
fi

# Final check
if [ -z "$MYSQL_CONF" ] || [ ! -f "$MYSQL_CONF" ]; then
    echo -e "${RED}❌ Could not find MySQL configuration file.${NC}"
    echo -e "${YELLOW}   Please manually configure MySQL bind-address:${NC}"
    echo -e "${YELLOW}   1. Find your MySQL config file (usually in /etc/mysql/ or /etc/)${NC}"
    echo -e "${YELLOW}   2. Add or modify: bind-address = 0.0.0.0${NC}"
    echo -e "${YELLOW}   3. Restart MySQL: sudo systemctl restart mysql${NC}"
    exit 1
fi

# Backup configuration
if [ ! -f "${MYSQL_CONF}.backup" ]; then
    echo -e "${YELLOW}📦 Backing up MySQL configuration...${NC}"
    cp "$MYSQL_CONF" "${MYSQL_CONF}.backup"
    echo -e "${GREEN}✅ Backup created at ${MYSQL_CONF}.backup${NC}"
fi

# Check current bind-address (check in all config files that might be included)
CURRENT_BIND=$(grep -E "^bind-address" "$MYSQL_CONF" 2>/dev/null || echo "")

# Also check in included config files
if [ -d "/etc/mysql/mysql.conf.d" ]; then
    INCLUDED_BIND=$(grep -rhE "^bind-address" /etc/mysql/mysql.conf.d/ 2>/dev/null | head -1 || echo "")
    if [ -n "$INCLUDED_BIND" ]; then
        CURRENT_BIND="$INCLUDED_BIND"
    fi
fi

if echo "$CURRENT_BIND" | grep -q "0.0.0.0"; then
    echo -e "${GREEN}✅ MySQL is already configured to accept connections from all interfaces${NC}"
    exit 0
fi

# Update bind-address
echo -e "${YELLOW}📝 Updating MySQL bind-address...${NC}"

# Check if bind-address exists in this file
if grep -q "^bind-address" "$MYSQL_CONF"; then
    # Replace existing bind-address
    sed -i 's/^bind-address.*/bind-address = 0.0.0.0/' "$MYSQL_CONF"
    echo -e "${GREEN}✅ Updated existing bind-address in $MYSQL_CONF${NC}"
else
    # Check if it's in included config files (MariaDB/MySQL 8.0+)
    if [ -d "/etc/mysql/mysql.conf.d" ]; then
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
            echo "" >> "$MYSQL_CONF"
            echo "[mysqld]" >> "$MYSQL_CONF"
            echo "bind-address = 0.0.0.0" >> "$MYSQL_CONF"
            echo -e "${GREEN}✅ Added bind-address to $MYSQL_CONF${NC}"
        fi
    else
        # Add to main config file
        echo "" >> "$MYSQL_CONF"
        echo "[mysqld]" >> "$MYSQL_CONF"
        echo "bind-address = 0.0.0.0" >> "$MYSQL_CONF"
        echo -e "${GREEN}✅ Added bind-address to $MYSQL_CONF${NC}"
    fi
fi

echo -e "${GREEN}✅ Updated bind-address to 0.0.0.0${NC}"

# Restart MySQL
echo -e "${YELLOW}🔄 Restarting MySQL...${NC}"
systemctl restart mysql

# Wait for MySQL to start
sleep 3

# Verify MySQL is running
if systemctl is-active --quiet mysql; then
    echo -e "${GREEN}✅ MySQL restarted successfully${NC}"
else
    echo -e "${RED}❌ MySQL failed to restart. Check logs: sudo journalctl -u mysql${NC}"
    exit 1
fi

# Verify bind-address
echo -e "${YELLOW}🔍 Verifying configuration...${NC}"
if netstat -tlnp 2>/dev/null | grep -q ":3306.*0.0.0.0"; then
    echo -e "${GREEN}✅ MySQL is listening on all interfaces (0.0.0.0:3306)${NC}"
else
    echo -e "${YELLOW}⚠️  Could not verify bind-address. Please check manually:${NC}"
    echo -e "   sudo netstat -tlnp | grep mysql"
fi

echo ""
echo -e "${YELLOW}⚠️  Security Note:${NC}"
echo -e "   MySQL is now accessible from all interfaces."
echo -e "   Make sure to:"
echo -e "   1. Use strong passwords for database users"
echo -e "   2. Configure firewall to restrict access if needed"
echo -e "   3. Use MySQL user host restrictions (already configured)"
echo ""
echo -e "${GREEN}✅ MySQL is now ready to accept connections from Docker containers${NC}"
