#!/bin/bash

# Fix Nginx Configuration for Cloudflare
# Removes SSL certificate references since Cloudflare handles HTTPS

set -e

echo "🔧 Fixing Nginx Configuration for Cloudflare"
echo "============================================="
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

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
DOMAIN="opcr.itechportfolio.xyz"
NGINX_CONFIG="/etc/nginx/sites-available/nextcloud"

# Check if config exists
if [ ! -f "$NGINX_CONFIG" ]; then
    echo -e "${RED}❌ Nginx config not found: $NGINX_CONFIG${NC}"
    exit 1
fi

echo -e "${BLUE}Step 1: Backing up current config...${NC}"
cp "$NGINX_CONFIG" "${NGINX_CONFIG}.backup.$(date +%Y%m%d_%H%M%S)"
echo -e "${GREEN}✅ Backup created${NC}"

echo ""
echo -e "${BLUE}Step 2: Creating Cloudflare-compatible config...${NC}"

# Create new config without SSL certificate references
cat > "$NGINX_CONFIG" <<'NGINX_EOF'
# Nginx configuration for Nextcloud with Cloudflare Proxy
# Domain: opcr.itechportfolio.xyz
# Cloudflare handles HTTPS, server only needs HTTP

# HTTP server (Cloudflare proxies HTTPS to this)
server {
    listen 80;
    listen [::]:80;
    server_name opcr.itechportfolio.xyz;

    # Let's Encrypt verification (for future use if needed)
    location /.well-known/acme-challenge/ {
        root /var/www/html;
    }

    # Increase upload size for Nextcloud
    client_max_body_size 512M;
    client_body_buffer_size 512M;

    # Proxy to Nextcloud container
    location / {
        proxy_pass http://127.0.0.1:8082;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        
        # Cloudflare specific headers
        proxy_set_header CF-Connecting-IP $http_cf_connecting_ip;
        proxy_set_header CF-Ray $http_cf_ray;
        proxy_set_header CF-Visitor $http_cf_visitor;
        
        # Standard proxy headers
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $host;
        proxy_set_header X-Forwarded-Ssl on;
        
        # WebSocket support for Nextcloud
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        
        # Timeouts
        proxy_connect_timeout 360s;
        proxy_send_timeout 360s;
        proxy_read_timeout 360s;
        send_timeout 360s;
    }
}
NGINX_EOF

# Update domain name
sed -i "s/opcr.itechportfolio.xyz/$DOMAIN/g" "$NGINX_CONFIG"

echo -e "${GREEN}✅ Created Cloudflare-compatible config${NC}"

echo ""
echo -e "${BLUE}Step 3: Testing Nginx configuration...${NC}"

if nginx -t; then
    echo -e "${GREEN}✅ Nginx configuration is valid!${NC}"
else
    echo -e "${RED}❌ Nginx configuration test failed${NC}"
    echo -e "${YELLOW}   Restoring backup...${NC}"
    cp "${NGINX_CONFIG}.backup."* "$NGINX_CONFIG" 2>/dev/null || true
    exit 1
fi

echo ""
echo -e "${BLUE}Step 4: Starting/Reloading Nginx...${NC}"

# Check if Nginx is running
if systemctl is-active --quiet nginx; then
    echo -e "${YELLOW}   Nginx is running, reloading...${NC}"
    if systemctl reload nginx; then
        echo -e "${GREEN}✅ Nginx reloaded successfully${NC}"
    else
        echo -e "${RED}❌ Failed to reload Nginx${NC}"
        exit 1
    fi
else
    echo -e "${YELLOW}   Nginx is not running, starting...${NC}"
    if systemctl start nginx; then
        echo -e "${GREEN}✅ Nginx started successfully${NC}"
        
        # Enable to start on boot
        systemctl enable nginx
        echo -e "${GREEN}✅ Nginx enabled to start on boot${NC}"
    else
        echo -e "${RED}❌ Failed to start Nginx${NC}"
        echo -e "${YELLOW}   Checking status...${NC}"
        systemctl status nginx
        exit 1
    fi
fi

echo ""
echo -e "${GREEN}✅ Nginx configuration fixed!${NC}"
echo ""
echo -e "${BLUE}Summary:${NC}"
echo -e "  ✅ Removed SSL certificate references"
echo -e "  ✅ Using HTTP only (Cloudflare handles HTTPS)"
echo -e "  ✅ Configured Cloudflare headers"
echo -e "  ✅ Proxy to Nextcloud on port 8082"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo -e "  1. Make sure Cloudflare SSL mode is set to 'Full'"
echo -e "  2. Test access: https://$DOMAIN"
echo -e "  3. Check Nextcloud is running: docker ps | grep nextcloud"
echo ""
echo -e "${YELLOW}Troubleshooting:${NC}"
echo -e "  - Check Nginx logs: sudo tail -f /var/log/nginx/error.log"
echo -e "  - Check Nextcloud logs: docker logs nextcloud"
echo -e "  - Test HTTP: curl -I http://localhost:8082"
