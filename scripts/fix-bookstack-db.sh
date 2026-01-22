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
USER_EXISTS=$(echo "$USER_EXISTS" | tr -d '\n' | head -1)

if [ "${USER_EXISTS:-0}" -eq "0" ]; then
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
CONNECTION_TEST=$(docker exec facultyportfolio-db mysql -u bookstack_user -p'BookstackDB2024!Secure' -e "USE bookstack; SELECT 1;" 2>&1)
if echo "$CONNECTION_TEST" | grep -q "ERROR\|Access denied"; then
    echo -e "${RED}✗ Database connection failed${NC}"
    echo "   Error: $(echo "$CONNECTION_TEST" | grep -i error | head -1)"
    echo "   Trying to fix permissions..."
    
    docker exec facultyportfolio-db mysql -u root -proot <<EOF 2>/dev/null
DROP USER IF EXISTS 'bookstack_user'@'%';
CREATE USER 'bookstack_user'@'%' IDENTIFIED BY 'BookstackDB2024!Secure';
GRANT ALL PRIVILEGES ON bookstack.* TO 'bookstack_user'@'%';
FLUSH PRIVILEGES;
EOF
    
    echo -e "${GREEN}✓ User recreated with permissions${NC}"
    
    # Test again
    if docker exec facultyportfolio-db mysql -u bookstack_user -p'BookstackDB2024!Secure' -e "USE bookstack; SELECT 1;" 2>&1 | grep -q "ERROR\|Access denied"; then
        echo -e "${RED}✗ Still failing. Check password and database name.${NC}"
    else
        echo -e "${GREEN}✓ Database connection successful after fix${NC}"
    fi
else
    echo -e "${GREEN}✓ Database connection successful${NC}"
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
