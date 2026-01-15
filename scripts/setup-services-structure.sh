#!/bin/bash

# Setup Services Directory Structure
# This script creates organized folders for all services on the VPS

set -e

echo "📁 Setting up services directory structure..."

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Check if running as root or with sudo
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}❌ Please run with sudo${NC}"
    exit 1
fi

# Base directory for services
SERVICES_DIR="/opt/services"

echo -e "${YELLOW}📂 Creating services directory structure...${NC}"

# Create base directory
mkdir -p "$SERVICES_DIR"
chmod 755 "$SERVICES_DIR"

# Create directories for each service
mkdir -p "$SERVICES_DIR/nextcloud"
mkdir -p "$SERVICES_DIR/snipeit"
mkdir -p "$SERVICES_DIR/nginx-configs"

# Set ownership (adjust user as needed)
# Assuming www-data or your user
if id "www-data" &>/dev/null; then
    chown -R www-data:www-data "$SERVICES_DIR"
else
    # Use current user if www-data doesn't exist
    CURRENT_USER=${SUDO_USER:-$USER}
    chown -R "$CURRENT_USER:$CURRENT_USER" "$SERVICES_DIR"
fi

echo -e "${GREEN}✅ Created directory structure:${NC}"
echo -e "  📁 $SERVICES_DIR/"
echo -e "    ├── nextcloud/     (Nextcloud files)"
echo -e "    ├── snipeit/       (Snipe-IT files)"
echo -e "    └── nginx-configs/ (Nginx configurations)"

echo ""
echo -e "${YELLOW}📝 Directory Structure:${NC}"
tree -L 2 "$SERVICES_DIR" 2>/dev/null || ls -la "$SERVICES_DIR"

echo ""
echo -e "${GREEN}✅ Services directory structure created!${NC}"
echo -e "${YELLOW}💡 Note: Faculty Portfolio can stay in its current location${NC}"
echo -e "${YELLOW}   or you can move it to $SERVICES_DIR/faculty-portfolio/ if desired${NC}"
