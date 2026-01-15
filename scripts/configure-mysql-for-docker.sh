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

MYSQL_CONF="/etc/mysql/mysql.conf.d/mysqld.cnf"

# Backup configuration
if [ ! -f "${MYSQL_CONF}.backup" ]; then
    echo -e "${YELLOW}📦 Backing up MySQL configuration...${NC}"
    cp "$MYSQL_CONF" "${MYSQL_CONF}.backup"
    echo -e "${GREEN}✅ Backup created at ${MYSQL_CONF}.backup${NC}"
fi

# Check current bind-address
CURRENT_BIND=$(grep -E "^bind-address" "$MYSQL_CONF" 2>/dev/null || echo "")

if echo "$CURRENT_BIND" | grep -q "0.0.0.0"; then
    echo -e "${GREEN}✅ MySQL is already configured to accept connections from all interfaces${NC}"
    exit 0
fi

# Update bind-address
echo -e "${YELLOW}📝 Updating MySQL bind-address...${NC}"

if grep -q "^bind-address" "$MYSQL_CONF"; then
    # Replace existing bind-address
    sed -i 's/^bind-address.*/bind-address = 0.0.0.0/' "$MYSQL_CONF"
else
    # Add bind-address if not present
    echo "bind-address = 0.0.0.0" >> "$MYSQL_CONF"
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
