#!/bin/bash

# MySQL Configuration Diagnostic Script
# This script helps identify MySQL configuration file location and current settings

set -e

echo "🔍 MySQL Configuration Diagnostic"
echo "=================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Check if MySQL is running
echo -e "${BLUE}1. Checking MySQL service status...${NC}"
if systemctl is-active --quiet mysql || systemctl is-active --quiet mysqld || systemctl is-active --quiet mariadb; then
    echo -e "${GREEN}✅ MySQL/MariaDB service is running${NC}"
    
    # Determine service name
    if systemctl is-active --quiet mysql; then
        SERVICE_NAME="mysql"
    elif systemctl is-active --quiet mysqld; then
        SERVICE_NAME="mysqld"
    else
        SERVICE_NAME="mariadb"
    fi
    echo -e "   Service name: $SERVICE_NAME"
else
    echo -e "${RED}❌ MySQL/MariaDB service is not running${NC}"
    echo -e "${YELLOW}   Please start MySQL first: sudo systemctl start mysql${NC}"
fi

echo ""

# Find MySQL configuration files
echo -e "${BLUE}2. Searching for MySQL configuration files...${NC}"
CONFIG_FILES=$(find /etc -name "my.cnf" -o -name "mysqld.cnf" -o -name "*.cnf" 2>/dev/null | grep -E "(mysql|mariadb)" | head -10)

if [ -n "$CONFIG_FILES" ]; then
    echo -e "${GREEN}✅ Found configuration files:${NC}"
    echo "$CONFIG_FILES" | while read file; do
        echo -e "   ${YELLOW}$file${NC}"
        if [ -f "$file" ]; then
            BIND_ADDRESS=$(grep -E "^bind-address" "$file" 2>/dev/null || echo "   (not found)")
            if [ -n "$BIND_ADDRESS" ] && [ "$BIND_ADDRESS" != "   (not found)" ]; then
                echo -e "      → $BIND_ADDRESS"
            fi
        fi
    done
else
    echo -e "${YELLOW}⚠️  No configuration files found in /etc${NC}"
fi

echo ""

# Check MySQL version and default config
echo -e "${BLUE}3. Checking MySQL version and default config paths...${NC}"
if command -v mysqld &> /dev/null; then
    MYSQL_VERSION=$(mysqld --version 2>/dev/null | head -1 || echo "Unknown")
    echo -e "${GREEN}✅ MySQL version:${NC}"
    echo "   $MYSQL_VERSION"
    
    echo ""
    echo -e "${BLUE}4. Checking MySQL default configuration paths...${NC}"
    MYSQL_HELP=$(mysqld --help --verbose 2>/dev/null | grep -A 20 "Default options" || echo "")
    if [ -n "$MYSQL_HELP" ]; then
        echo -e "${GREEN}✅ MySQL default config paths:${NC}"
        echo "$MYSQL_HELP" | grep -E "(my\.cnf|Default options)" | head -5
    fi
else
    echo -e "${RED}❌ mysqld command not found${NC}"
fi

echo ""

# Check current bind-address setting
echo -e "${BLUE}5. Checking current bind-address setting...${NC}"
if command -v mysql &> /dev/null; then
    # Try to connect and check variable
    BIND_CHECK=$(mysql -u root -e "SHOW VARIABLES LIKE 'bind_address';" 2>/dev/null || echo "")
    if [ -n "$BIND_CHECK" ]; then
        echo -e "${GREEN}✅ Current bind_address variable:${NC}"
        echo "$BIND_CHECK"
    else
        echo -e "${YELLOW}⚠️  Could not check bind_address (may need root password)${NC}"
    fi
fi

# Check what's listening on port 3306
echo ""
echo -e "${BLUE}6. Checking what's listening on MySQL port (3306)...${NC}"
if command -v netstat &> /dev/null; then
    LISTENING=$(netstat -tlnp 2>/dev/null | grep ":3306" || echo "")
    if [ -n "$LISTENING" ]; then
        echo -e "${GREEN}✅ Port 3306 status:${NC}"
        echo "$LISTENING"
        
        if echo "$LISTENING" | grep -q "0.0.0.0"; then
            echo -e "${GREEN}   ✅ MySQL is listening on all interfaces (0.0.0.0)${NC}"
        elif echo "$LISTENING" | grep -q "127.0.0.1"; then
            echo -e "${YELLOW}   ⚠️  MySQL is only listening on localhost (127.0.0.1)${NC}"
            echo -e "${YELLOW}      Docker containers need it to listen on 0.0.0.0${NC}"
        fi
    else
        echo -e "${RED}❌ Nothing listening on port 3306${NC}"
    fi
elif command -v ss &> /dev/null; then
    LISTENING=$(ss -tlnp 2>/dev/null | grep ":3306" || echo "")
    if [ -n "$LISTENING" ]; then
        echo -e "${GREEN}✅ Port 3306 status:${NC}"
        echo "$LISTENING"
    fi
else
    echo -e "${YELLOW}⚠️  netstat/ss not available${NC}"
fi

echo ""
echo -e "${BLUE}7. Common MySQL config file locations:${NC}"
echo "   /etc/mysql/mysql.conf.d/mysqld.cnf"
echo "   /etc/mysql/my.cnf"
echo "   /etc/my.cnf"
echo "   /etc/mysql/mariadb.conf.d/50-server.cnf"
echo "   /etc/mysql/conf.d/mysqld.cnf"

echo ""
echo -e "${BLUE}8. Recommendations:${NC}"
if echo "$LISTENING" 2>/dev/null | grep -q "127.0.0.1"; then
    echo -e "${YELLOW}   ⚠️  MySQL needs to be configured to listen on 0.0.0.0${NC}"
    echo ""
    echo -e "${YELLOW}   To fix manually:${NC}"
    echo "   1. Find your MySQL config file (see list above)"
    echo "   2. Edit the file and find [mysqld] section"
    echo "   3. Add or change: bind-address = 0.0.0.0"
    echo "   4. Restart MySQL: sudo systemctl restart mysql"
    echo ""
    echo -e "${YELLOW}   Or run the automated script:${NC}"
    echo "   sudo bash scripts/configure-mysql-for-docker.sh"
else
    echo -e "${GREEN}   ✅ MySQL appears to be configured correctly${NC}"
fi

echo ""
echo -e "${GREEN}✅ Diagnostic complete!${NC}"
