#!/bin/bash

# Fix Docker Network Configuration
# This script detects the actual Faculty Portfolio network and updates docker-compose files

set -e

echo "🔧 Fixing Docker Network Configuration"
echo "======================================"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Check if Faculty Portfolio MySQL container is running
if ! docker ps | grep -q "facultyportfolio-db"; then
    echo -e "${RED}❌ Faculty Portfolio MySQL container is not running!${NC}"
    echo -e "${YELLOW}   Please start Faculty Portfolio first:${NC}"
    echo -e "${YELLOW}   cd ~/facultyPortfolio && docker compose up -d${NC}"
    exit 1
fi

# Detect the actual Docker network name
MYSQL_CONTAINER="facultyportfolio-db"
MYSQL_NETWORK=$(docker inspect "$MYSQL_CONTAINER" --format '{{range $key, $value := .NetworkSettings.Networks}}{{$key}}{{end}}' | head -1)

if [ -z "$MYSQL_NETWORK" ]; then
    echo -e "${RED}❌ Could not detect Docker network${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Detected MySQL network: $MYSQL_NETWORK${NC}"

# Get script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

# Update Nextcloud docker-compose.yml
NEXTCLOUD_COMPOSE="$PROJECT_DIR/docker-compose.nextcloud.yml"
if [ -f "$NEXTCLOUD_COMPOSE" ]; then
    echo -e "${BLUE}📝 Updating Nextcloud docker-compose.yml...${NC}"
    
    # Replace network name if it exists, or add it
    if grep -q "facultyportfolio_default" "$NEXTCLOUD_COMPOSE"; then
        sed -i "s/facultyportfolio_default/$MYSQL_NETWORK/g" "$NEXTCLOUD_COMPOSE"
        echo -e "${GREEN}✅ Updated Nextcloud network to: $MYSQL_NETWORK${NC}"
    fi
fi

# Update Snipe-IT docker-compose.yml
SNIPEIT_COMPOSE="$PROJECT_DIR/docker-compose.snipeit.yml"
if [ -f "$SNIPEIT_COMPOSE" ]; then
    echo -e "${BLUE}📝 Updating Snipe-IT docker-compose.yml...${NC}"
    
    # Replace network name if it exists, or add it
    if grep -q "facultyportfolio_default" "$SNIPEIT_COMPOSE"; then
        sed -i "s/facultyportfolio_default/$MYSQL_NETWORK/g" "$SNIPEIT_COMPOSE"
        echo -e "${GREEN}✅ Updated Snipe-IT network to: $MYSQL_NETWORK${NC}"
    fi
fi

echo ""
echo -e "${GREEN}✅ Network configuration updated!${NC}"
echo -e "${YELLOW}   Network name: $MYSQL_NETWORK${NC}"
echo ""
echo -e "${BLUE}To verify, check docker-compose files:${NC}"
echo -e "   grep -A 2 'networks:' $NEXTCLOUD_COMPOSE"
echo -e "   grep -A 2 'networks:' $SNIPEIT_COMPOSE"
