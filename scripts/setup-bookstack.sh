#!/bin/bash

# BookStack Setup Script
# This script sets up BookStack with nginx reverse proxy
# Configured for Cloudflare Flexible mode (Cloudflare handles HTTPS)

set -e

echo "==================================="
echo "BookStack Setup Script"
echo "==================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Please run as root (use sudo)${NC}"
    exit 1
fi

# Function to check if a command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Check for required commands
echo -e "${YELLOW}Checking requirements...${NC}"
for cmd in docker docker-compose nginx certbot; do
    if ! command_exists $cmd; then
        echo -e "${RED}Error: $cmd is not installed${NC}"
        exit 1
    fi
done
echo -e "${GREEN}All requirements met${NC}"
echo ""

# Check if docker network exists
echo -e "${YELLOW}Checking Docker network...${NC}"
if ! docker network inspect facultyportfolio_default >/dev/null 2>&1; then
    echo -e "${YELLOW}Creating facultyportfolio_default network...${NC}"
    docker network create facultyportfolio_default
fi
echo -e "${GREEN}Docker network ready${NC}"
echo ""

# Check if main database is running
echo -e "${YELLOW}Checking if MySQL database is running...${NC}"
if ! docker ps | grep -q "facultyportfolio-db"; then
    echo -e "${RED}Error: Main database (facultyportfolio-db) is not running${NC}"
    echo "Please start it with: docker-compose up -d db"
    exit 1
fi
echo -e "${GREEN}Database is running${NC}"
echo ""

# Setup nginx configuration for site.itechportfolio.xyz (BookStack)
echo -e "${YELLOW}Setting up nginx configuration for BookStack...${NC}"
BOOKSTACK_NGINX_CONF="/etc/nginx/sites-available/bookstack"
BOOKSTACK_NGINX_ENABLED="/etc/nginx/sites-enabled/bookstack"

if [ ! -f "$BOOKSTACK_NGINX_CONF" ]; then
    cp scripts/nginx/bookstack.conf "$BOOKSTACK_NGINX_CONF"
    ln -sf "$BOOKSTACK_NGINX_CONF" "$BOOKSTACK_NGINX_ENABLED"
    echo -e "${GREEN}BookStack nginx configuration created${NC}"
else
    echo -e "${YELLOW}BookStack nginx configuration already exists${NC}"
fi

# Setup nginx configuration for portfolio.itechportfolio.xyz
echo -e "${YELLOW}Setting up nginx configuration for Portfolio...${NC}"
PORTFOLIO_NGINX_CONF="/etc/nginx/sites-available/portfolio"
PORTFOLIO_NGINX_ENABLED="/etc/nginx/sites-enabled/portfolio"

if [ ! -f "$PORTFOLIO_NGINX_CONF" ]; then
    cp scripts/nginx/portfolio.conf "$PORTFOLIO_NGINX_CONF"
    ln -sf "$PORTFOLIO_NGINX_CONF" "$PORTFOLIO_NGINX_ENABLED"
    echo -e "${GREEN}Portfolio nginx configuration created${NC}"
else
    echo -e "${YELLOW}Portfolio nginx configuration already exists${NC}"
fi

# Test nginx configuration
echo -e "${YELLOW}Testing nginx configuration...${NC}"
if nginx -t; then
    echo -e "${GREEN}Nginx configuration is valid${NC}"
else
    echo -e "${RED}Nginx configuration has errors. Please fix them before continuing.${NC}"
    exit 1
fi
echo ""

# Reload nginx
echo -e "${YELLOW}Reloading nginx...${NC}"
systemctl reload nginx
echo -e "${GREEN}Nginx reloaded${NC}"
echo ""

# Note about Cloudflare SSL
echo -e "${YELLOW}Note: SSL/HTTPS is handled by Cloudflare${NC}"
echo -e "${GREEN}No SSL certificates needed on the server (Cloudflare Flexible mode)${NC}"
echo -e "${YELLOW}Make sure your domains are proxied through Cloudflare (orange cloud)${NC}"
echo ""

# Start BookStack
echo -e "${YELLOW}Starting BookStack containers...${NC}"
docker-compose -f docker-compose.bookstack.yml up -d
echo -e "${GREEN}BookStack started successfully${NC}"
echo ""

# Wait for BookStack to be ready
echo -e "${YELLOW}Waiting for BookStack to initialize (this may take a minute)...${NC}"
sleep 15

# Show status
echo ""
echo -e "${GREEN}==================================="
echo "BookStack Setup Complete!"
echo "===================================${NC}"
echo ""
echo "Services:"
echo "  - Portfolio:  https://portfolio.itechportfolio.xyz"
echo "  - BookStack:  https://site.itechportfolio.xyz"
echo ""
echo -e "${YELLOW}Cloudflare Configuration:${NC}"
echo "  - Make sure domains are proxied (orange cloud) in Cloudflare"
echo "  - SSL/TLS mode: Flexible (Cloudflare handles HTTPS)"
echo "  - Server only needs HTTP (port 80)"
echo ""
echo "BookStack Default Credentials:"
echo "  Email:    admin@admin.com"
echo "  Password: password"
echo ""
echo -e "${YELLOW}IMPORTANT: Change the default password immediately after first login!${NC}"
echo ""
echo "To view logs:"
echo "  docker-compose -f docker-compose.bookstack.yml logs -f"
echo ""
echo "To stop BookStack:"
echo "  docker-compose -f docker-compose.bookstack.yml down"
echo ""
