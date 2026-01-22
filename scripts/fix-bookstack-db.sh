#!/bin/bash

# Fix BookStack database connection issues

echo "==================================="
echo "BookStack Database Fix"
echo "==================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check if database is running
if ! docker ps | grep -q "facultyportfolio-db"; then
    echo -e "${RED}Error: Database is not running${NC}"
    exit 1
fi

echo -e "${YELLOW}Checking database user...${NC}"

# Check if user exists
USER_EXISTS=$(docker exec facultyportfolio-db mysql -u root -proot -e "SELECT User FROM mysql.user WHERE User='bookstack_user';" 2>/dev/null | grep -c "bookstack_user" || echo "0")

if [ "$USER_EXISTS" -eq "0" ]; then
    echo -e "${YELLOW}User doesn't exist, creating...${NC}"
    
    docker exec facultyportfolio-db mysql -u root -proot <<EOF
CREATE DATABASE IF NOT EXISTS bookstack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'bookstack_user'@'%' IDENTIFIED BY 'BookstackDB2024!Secure';
GRANT ALL PRIVILEGES ON bookstack.* TO 'bookstack_user'@'%';
FLUSH PRIVILEGES;
EOF
    
    echo -e "${GREEN}✓ User created${NC}"
else
    echo -e "${GREEN}✓ User exists${NC}"
fi

# Verify user can connect
echo -e "${YELLOW}Testing database connection...${NC}"
if docker exec facultyportfolio-db mysql -u bookstack_user -p'BookstackDB2024!Secure' -e "USE bookstack; SELECT 1;" 2>/dev/null; then
    echo -e "${GREEN}✓ Database connection successful${NC}"
else
    echo -e "${RED}✗ Database connection failed${NC}"
    echo "   Trying to fix permissions..."
    
    docker exec facultyportfolio-db mysql -u root -proot <<EOF
GRANT ALL PRIVILEGES ON bookstack.* TO 'bookstack_user'@'%';
FLUSH PRIVILEGES;
EOF
    
    echo -e "${GREEN}✓ Permissions updated${NC}"
fi

echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "  1. Remove BookStack config volume (if it has old config):"
echo "     docker volume rm bookstack-config"
echo ""
echo "  2. Restart BookStack:"
echo "     docker-compose -f docker-compose.bookstack.yml down"
echo "     docker-compose -f docker-compose.bookstack.yml up -d"
echo ""
