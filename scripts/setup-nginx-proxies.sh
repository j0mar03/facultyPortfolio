#!/bin/bash

# Setup Nginx Reverse Proxies for All Services
# This script configures Nginx for Nextcloud and Snipe-IT subdomains

set -e

echo "🌐 Setting up Nginx reverse proxies..."

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

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
NGINX_CONFIGS="$PROJECT_DIR/scripts/nginx"

# Check if Nginx is installed
if ! command -v nginx &> /dev/null; then
    echo -e "${RED}❌ Nginx is not installed. Please install it first.${NC}"
    exit 1
fi

# Copy Nginx configurations
echo -e "${YELLOW}📝 Copying Nginx configurations...${NC}"

# Nextcloud configuration
if [ -f "$NGINX_CONFIGS/nextcloud.conf" ]; then
    cp "$NGINX_CONFIGS/nextcloud.conf" /etc/nginx/sites-available/nextcloud
    echo -e "${GREEN}✅ Copied Nextcloud Nginx configuration${NC}"
else
    echo -e "${YELLOW}⚠️  Nextcloud Nginx config not found${NC}"
fi

# Snipe-IT configuration
if [ -f "$NGINX_CONFIGS/snipeit.conf" ]; then
    cp "$NGINX_CONFIGS/snipeit.conf" /etc/nginx/sites-available/snipeit
    echo -e "${GREEN}✅ Copied Snipe-IT Nginx configuration${NC}"
else
    echo -e "${YELLOW}⚠️  Snipe-IT Nginx config not found${NC}"
fi

# Enable sites
echo -e "${YELLOW}🔗 Enabling Nginx sites...${NC}"

if [ -f "/etc/nginx/sites-available/nextcloud" ]; then
    ln -sf /etc/nginx/sites-available/nextcloud /etc/nginx/sites-enabled/nextcloud
    echo -e "${GREEN}✅ Enabled Nextcloud site${NC}"
fi

if [ -f "/etc/nginx/sites-available/snipeit" ]; then
    ln -sf /etc/nginx/sites-available/snipeit /etc/nginx/sites-enabled/snipeit
    echo -e "${GREEN}✅ Enabled Snipe-IT site${NC}"
fi

# Test Nginx configuration
echo -e "${YELLOW}🧪 Testing Nginx configuration...${NC}"
if nginx -t; then
    echo -e "${GREEN}✅ Nginx configuration is valid${NC}"
    
    # Reload Nginx
    echo -e "${YELLOW}🔄 Reloading Nginx...${NC}"
    systemctl reload nginx
    echo -e "${GREEN}✅ Nginx reloaded${NC}"
else
    echo -e "${RED}❌ Nginx configuration test failed. Please fix errors.${NC}"
    exit 1
fi

# SSL Certificate setup
echo ""
echo -e "${YELLOW}📜 SSL Certificate Setup${NC}"
echo -e "   Nextcloud: opcr.itechportfolio.xyz"
echo -e "   Snipe-IT: asset.itechportfolio.xyz"
echo ""
read -p "Do you want to set up SSL certificates with Let's Encrypt now? (y/n): " SETUP_SSL

if [ "$SETUP_SSL" = "y" ]; then
    # Check if certbot is installed
    if ! command -v certbot &> /dev/null; then
        echo -e "${YELLOW}📦 Installing Certbot...${NC}"
        apt update
        apt install -y certbot python3-certbot-nginx
    fi
    
    # Get SSL certificates
    echo -e "${YELLOW}🔐 Obtaining SSL certificates...${NC}"
    
    if [ -f "/etc/nginx/sites-available/nextcloud" ]; then
        certbot --nginx -d opcr.itechportfolio.xyz --non-interactive --agree-tos --email admin@itechportfolio.xyz || true
    fi
    
    if [ -f "/etc/nginx/sites-available/snipeit" ]; then
        certbot --nginx -d asset.itechportfolio.xyz --non-interactive --agree-tos --email admin@itechportfolio.xyz || true
    fi
    
    echo -e "${GREEN}✅ SSL certificates configured${NC}"
    echo -e "${YELLOW}💡 Certbot will auto-renew certificates${NC}"
fi

echo ""
echo -e "${GREEN}✅ Nginx reverse proxies configured!${NC}"
echo ""
echo -e "${GREEN}📋 Service URLs:${NC}"
echo -e "  - Nextcloud: https://opcr.itechportfolio.xyz"
echo -e "  - Snipe-IT: https://asset.itechportfolio.xyz"
echo ""
echo -e "${YELLOW}💡 Make sure DNS records point to your VPS IP:${NC}"
echo -e "  - opcr.itechportfolio.xyz -> Your VPS IP"
echo -e "  - asset.itechportfolio.xyz -> Your VPS IP"
