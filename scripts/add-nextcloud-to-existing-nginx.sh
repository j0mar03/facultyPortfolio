#!/bin/bash

# Add Nextcloud to Existing Nginx Configuration
# This script adds Nextcloud as a new server block to your existing Nginx

set -e

echo "🔧 Adding Nextcloud to Existing Nginx Configuration"
echo "==================================================="
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

DOMAIN="opcr.itechportfolio.xyz"
NEXTCLOUD_PORT="8082"

# Step 1: Find existing Nginx config
echo -e "${BLUE}Step 1: Finding existing Nginx configuration...${NC}"

NGINX_CONFIG=""
POSSIBLE_CONFIGS=(
    "/etc/nginx/sites-available/faculty-portfolio"
    "/etc/nginx/sites-available/default"
    "/etc/nginx/nginx.conf"
    "/etc/nginx/conf.d/default.conf"
)

for config in "${POSSIBLE_CONFIGS[@]}"; do
    if [ -f "$config" ]; then
        # Check if it has server blocks
        if grep -q "server_name" "$config"; then
            NGINX_CONFIG="$config"
            echo -e "${GREEN}✅ Found Nginx config: $NGINX_CONFIG${NC}"
            break
        fi
    fi
done

# Also check sites-enabled
if [ -z "$NGINX_CONFIG" ]; then
    for site in /etc/nginx/sites-enabled/*; do
        if [ -f "$site" ] && [ ! -L "$site" ]; then
            REAL_CONFIG=$(readlink -f "$site" 2>/dev/null || echo "$site")
            if grep -q "server_name" "$REAL_CONFIG"; then
                NGINX_CONFIG="$REAL_CONFIG"
                echo -e "${GREEN}✅ Found Nginx config: $NGINX_CONFIG${NC}"
                break
            fi
        fi
    done
fi

if [ -z "$NGINX_CONFIG" ]; then
    echo -e "${RED}❌ Could not find existing Nginx configuration${NC}"
    echo -e "${YELLOW}   Please specify the path to your Nginx config file:${NC}"
    read -p "Nginx config file path: " NGINX_CONFIG
fi

if [ ! -f "$NGINX_CONFIG" ]; then
    echo -e "${RED}❌ Config file not found: $NGINX_CONFIG${NC}"
    exit 1
fi

# Step 2: Backup
echo ""
echo -e "${BLUE}Step 2: Backing up configuration...${NC}"
cp "$NGINX_CONFIG" "${NGINX_CONFIG}.backup.$(date +%Y%m%d_%H%M%S)"
echo -e "${GREEN}✅ Backup created${NC}"

# Step 3: Check if Nextcloud config already exists
echo ""
echo -e "${BLUE}Step 3: Checking for existing Nextcloud configuration...${NC}"

if grep -q "$DOMAIN" "$NGINX_CONFIG"; then
    echo -e "${YELLOW}⚠️  Nextcloud configuration already exists in $NGINX_CONFIG${NC}"
    read -p "Do you want to replace it? (yes/no): " REPLACE
    if [ "$RELACE" != "yes" ]; then
        echo -e "${YELLOW}❌ Aborted${NC}"
        exit 0
    fi
    # Remove existing Nextcloud server block
    sed -i "/server_name.*$DOMAIN/,/^}/d" "$NGINX_CONFIG"
    echo -e "${GREEN}✅ Removed existing Nextcloud configuration${NC}"
fi

# Step 4: Add Nextcloud server block
echo ""
echo -e "${BLUE}Step 4: Adding Nextcloud server block...${NC}"

# Create Nextcloud server block
NEXTCLOUD_BLOCK=$(cat <<EOF

# Nextcloud - opcr.itechportfolio.xyz
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN;

    # Let's Encrypt verification
    location /.well-known/acme-challenge/ {
        root /var/www/html;
    }

    # Increase upload size for Nextcloud
    client_max_body_size 512M;
    client_body_buffer_size 512M;

    # Proxy to Nextcloud container
    location / {
        proxy_pass http://127.0.0.1:$NEXTCLOUD_PORT;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        
        # Cloudflare specific headers
        proxy_set_header CF-Connecting-IP \$http_cf_connecting_ip;
        proxy_set_header CF-Ray \$http_cf_ray;
        proxy_set_header CF-Visitor \$http_cf_visitor;
        
        # Standard proxy headers
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_set_header X-Forwarded-Host \$host;
        proxy_set_header X-Forwarded-Ssl on;
        
        # WebSocket support for Nextcloud
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "upgrade";
        
        # Timeouts
        proxy_connect_timeout 360s;
        proxy_send_timeout 360s;
        proxy_read_timeout 360s;
        send_timeout 360s;
    }
}
EOF
)

# Append to config file
echo "$NEXTCLOUD_BLOCK" >> "$NGINX_CONFIG"
echo -e "${GREEN}✅ Added Nextcloud server block${NC}"

# Step 5: Test configuration
echo ""
echo -e "${BLUE}Step 5: Testing Nginx configuration...${NC}"

if nginx -t; then
    echo -e "${GREEN}✅ Nginx configuration is valid!${NC}"
else
    echo -e "${RED}❌ Nginx configuration test failed${NC}"
    echo -e "${YELLOW}   Restoring backup...${NC}"
    cp "${NGINX_CONFIG}.backup."* "$NGINX_CONFIG" 2>/dev/null || true
    exit 1
fi

# Step 6: Reload Nginx
echo ""
echo -e "${BLUE}Step 6: Reloading Nginx...${NC}"

if systemctl reload nginx; then
    echo -e "${GREEN}✅ Nginx reloaded successfully${NC}"
else
    echo -e "${RED}❌ Failed to reload Nginx${NC}"
    echo -e "${YELLOW}   Try: sudo systemctl restart nginx${NC}"
    exit 1
fi

# Step 7: Verify
echo ""
echo -e "${BLUE}Step 7: Verifying setup...${NC}"

# Check if Nextcloud container is running
if docker ps | grep -q "nextcloud"; then
    echo -e "${GREEN}✅ Nextcloud container is running${NC}"
else
    echo -e "${YELLOW}⚠️  Nextcloud container is not running${NC}"
    echo -e "${YELLOW}   Start it: cd /opt/services/nextcloud && docker compose up -d${NC}"
fi

# Test HTTP connection
HTTP_TEST=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:$NEXTCLOUD_PORT || echo "000")
if [ "$HTTP_TEST" = "200" ] || [ "$HTTP_TEST" = "302" ] || [ "$HTTP_TEST" = "301" ]; then
    echo -e "${GREEN}✅ Nextcloud is responding on port $NEXTCLOUD_PORT${NC}"
else
    echo -e "${YELLOW}⚠️  Could not verify Nextcloud connection (HTTP $HTTP_TEST)${NC}"
fi

echo ""
echo -e "${GREEN}✅ Nextcloud added to existing Nginx configuration!${NC}"
echo ""
echo -e "${BLUE}Summary:${NC}"
echo -e "  Config file: $NGINX_CONFIG"
echo -e "  Domain: $DOMAIN"
echo -e "  Nextcloud port: $NEXTCLOUD_PORT"
echo ""
echo -e "${YELLOW}Your Nginx now handles:${NC}"
echo -e "  - Faculty Portfolio (existing)"
echo -e "  - Nextcloud: $DOMAIN"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo -e "  1. Make sure Cloudflare SSL mode is set to 'Full'"
echo -e "  2. Test access: https://$DOMAIN"
echo -e "  3. Check logs: sudo tail -f /var/log/nginx/error.log"
