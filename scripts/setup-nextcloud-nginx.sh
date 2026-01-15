#!/bin/bash

# Setup Nginx Reverse Proxy for Nextcloud
# This script configures Nginx and SSL for opcr.itechportfolio.xyz

set -e

echo "🌐 Setting up Nginx Reverse Proxy for Nextcloud"
echo "=============================================="
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

# Domain configuration
DOMAIN="opcr.itechportfolio.xyz"
NEXTCLOUD_PORT="8082"

# Step 1: Check DNS Configuration
echo -e "${BLUE}Step 1: Checking DNS Configuration...${NC}"
echo ""
echo -e "${YELLOW}⚠️  IMPORTANT: Before continuing, make sure DNS is configured!${NC}"
echo ""
echo -e "${YELLOW}DNS Configuration Required:${NC}"
echo -e "   Domain: $DOMAIN"
echo -e "   Type: A Record"
echo -e "   Name: opcr"
echo -e "   Value: Your VPS IP address"
echo -e "   TTL: Auto (or 3600)"
echo ""
echo -e "${YELLOW}Where to configure:${NC}"
echo -e "   - Cloudflare: DNS → Records → Add A record"
echo -e "   - Namecheap: Advanced DNS → Add A record"
echo ""

# Get VPS IP
VPS_IP=$(curl -s ifconfig.me || curl -s ipinfo.io/ip || echo "unknown")
echo -e "${GREEN}Your VPS IP appears to be: $VPS_IP${NC}"
echo ""

read -p "Have you configured the DNS A record for $DOMAIN? (yes/no): " DNS_CONFIGURED

if [ "$DNS_CONFIGURED" != "yes" ]; then
    echo ""
    echo -e "${YELLOW}📝 DNS Configuration Instructions:${NC}"
    echo ""
    echo -e "${BLUE}For Cloudflare:${NC}"
    echo "   1. Log in to Cloudflare"
    echo "   2. Select your domain (itechportfolio.xyz)"
    echo "   3. Go to DNS → Records"
    echo "   4. Click 'Add record'"
    echo "   5. Type: A"
    echo "   6. Name: opcr"
    echo "   7. IPv4 address: $VPS_IP"
    echo "   8. Proxy status: DNS only (gray cloud) or Proxied (orange cloud)"
    echo "   9. TTL: Auto"
    echo "   10. Click Save"
    echo ""
    echo -e "${BLUE}For Namecheap:${NC}"
    echo "   1. Log in to Namecheap"
    echo "   2. Go to Domain List → Manage (itechportfolio.xyz)"
    echo "   3. Go to Advanced DNS tab"
    echo "   4. Under Host Records, click 'Add New Record'"
    echo "   5. Type: A Record"
    echo "   6. Host: opcr"
    echo "   7. Value: $VPS_IP"
    echo "   8. TTL: Automatic (or 3600)"
    echo "   9. Click Save"
    echo ""
    echo -e "${YELLOW}After configuring DNS, wait 5-10 minutes for propagation, then run this script again.${NC}"
    exit 0
fi

# Step 2: Verify DNS is working
echo ""
echo -e "${BLUE}Step 2: Verifying DNS...${NC}"

DNS_IP=$(dig +short $DOMAIN @8.8.8.8 | tail -1 || echo "")
if [ -z "$DNS_IP" ]; then
    DNS_IP=$(nslookup $DOMAIN 8.8.8.8 2>/dev/null | grep -A 1 "Name:" | tail -1 | awk '{print $2}' || echo "")
fi

if [ -n "$DNS_IP" ] && [ "$DNS_IP" != "" ]; then
    echo -e "${GREEN}✅ DNS is configured: $DOMAIN → $DNS_IP${NC}"
    if [ "$DNS_IP" != "$VPS_IP" ]; then
        echo -e "${YELLOW}⚠️  DNS IP ($DNS_IP) doesn't match VPS IP ($VPS_IP)${NC}"
        echo -e "${YELLOW}   This might be okay if you're using Cloudflare proxy${NC}"
    fi
else
    echo -e "${RED}❌ DNS not resolving yet. Please wait a few minutes and try again.${NC}"
    echo -e "${YELLOW}   You can check DNS propagation at: https://dnschecker.org/#A/$DOMAIN${NC}"
    read -p "Continue anyway? (yes/no): " CONTINUE
    if [ "$CONTINUE" != "yes" ]; then
        exit 0
    fi
fi

# Step 3: Check if Nginx is installed
echo ""
echo -e "${BLUE}Step 3: Checking Nginx...${NC}"

if ! command -v nginx &> /dev/null; then
    echo -e "${YELLOW}⚠️  Nginx is not installed. Installing...${NC}"
    apt update
    apt install -y nginx
fi

echo -e "${GREEN}✅ Nginx is installed${NC}"

# Step 4: Copy Nginx configuration
echo ""
echo -e "${BLUE}Step 4: Configuring Nginx...${NC}"

NGINX_CONFIG="/etc/nginx/sites-available/nextcloud"
NGINX_CONFIG_SOURCE="$PROJECT_DIR/scripts/nginx/nextcloud.conf"

if [ -f "$NGINX_CONFIG_SOURCE" ]; then
    # Update domain in config file
    sed "s/opcr.itechportfolio.xyz/$DOMAIN/g" "$NGINX_CONFIG_SOURCE" > "$NGINX_CONFIG"
    echo -e "${GREEN}✅ Created Nginx configuration: $NGINX_CONFIG${NC}"
else
    echo -e "${RED}❌ Nginx config template not found: $NGINX_CONFIG_SOURCE${NC}"
    exit 1
fi

# Step 5: Enable site
echo ""
echo -e "${BLUE}Step 5: Enabling Nginx site...${NC}"

ln -sf "$NGINX_CONFIG" /etc/nginx/sites-enabled/nextcloud
echo -e "${GREEN}✅ Enabled Nextcloud site${NC}"

# Remove default site if it exists and conflicts
if [ -f "/etc/nginx/sites-enabled/default" ]; then
    echo -e "${YELLOW}⚠️  Default Nginx site found. Disabling to avoid conflicts...${NC}"
    rm -f /etc/nginx/sites-enabled/default
fi

# Step 6: Test Nginx configuration
echo ""
echo -e "${BLUE}Step 6: Testing Nginx configuration...${NC}"

if nginx -t; then
    echo -e "${GREEN}✅ Nginx configuration is valid${NC}"
else
    echo -e "${RED}❌ Nginx configuration test failed!${NC}"
    exit 1
fi

# Step 7: Reload Nginx
echo ""
echo -e "${BLUE}Step 7: Reloading Nginx...${NC}"

systemctl reload nginx
echo -e "${GREEN}✅ Nginx reloaded${NC}"

# Step 8: Check if Nextcloud container is running
echo ""
echo -e "${BLUE}Step 8: Checking Nextcloud container...${NC}"

if docker ps | grep -q "nextcloud"; then
    echo -e "${GREEN}✅ Nextcloud container is running${NC}"
else
    echo -e "${RED}❌ Nextcloud container is not running!${NC}"
    echo -e "${YELLOW}   Starting Nextcloud...${NC}"
    cd /opt/services/nextcloud
    docker compose up -d
    sleep 5
fi

# Step 9: Test HTTP connection
echo ""
echo -e "${BLUE}Step 9: Testing HTTP connection...${NC}"

HTTP_TEST=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:$NEXTCLOUD_PORT || echo "000")
if [ "$HTTP_TEST" = "200" ] || [ "$HTTP_TEST" = "302" ] || [ "$HTTP_TEST" = "301" ]; then
    echo -e "${GREEN}✅ Nextcloud is responding on port $NEXTCLOUD_PORT${NC}"
else
    echo -e "${YELLOW}⚠️  Nextcloud might not be responding (HTTP $HTTP_TEST)${NC}"
    echo -e "${YELLOW}   Check logs: docker logs nextcloud${NC}"
fi

# Step 10: Cloudflare SSL Configuration
echo ""
echo -e "${BLUE}Step 10: Cloudflare SSL Configuration...${NC}"

echo ""
read -p "Is your domain using Cloudflare proxy (orange cloud)? (yes/no): " USE_CLOUDFLARE

if [ "$USE_CLOUDFLARE" = "yes" ]; then
    echo -e "${GREEN}✅ Using Cloudflare proxy configuration${NC}"
    
    # Use Cloudflare-specific config
    NGINX_CONFIG_SOURCE="$PROJECT_DIR/scripts/nginx/nextcloud-cloudflare.conf"
    if [ -f "$NGINX_CONFIG_SOURCE" ]; then
        sed "s/opcr.itechportfolio.xyz/$DOMAIN/g" "$NGINX_CONFIG_SOURCE" > "$NGINX_CONFIG"
        echo -e "${GREEN}✅ Updated to Cloudflare proxy configuration${NC}"
        
        # Reload Nginx
        systemctl reload nginx
    fi
    
    echo ""
    echo -e "${YELLOW}📝 Cloudflare SSL Settings:${NC}"
    echo -e "   Go to Cloudflare Dashboard → SSL/TLS"
    echo -e "   Set encryption mode to: ${GREEN}Full${NC} or ${GREEN}Full (strict)${NC}"
    echo -e "   This ensures Cloudflare → Your Server uses HTTPS"
    echo ""
    echo -e "${YELLOW}   Note: You don't need Let's Encrypt on your server${NC}"
    echo -e "${YELLOW}   Cloudflare handles SSL between users and Cloudflare${NC}"
    echo -e "${YELLOW}   Your server only needs HTTP (port 80)${NC}"
else
    echo -e "${YELLOW}📝 Setting up Let's Encrypt SSL...${NC}"
    
    # Check if Certbot is installed
    if ! command -v certbot &> /dev/null; then
        echo -e "${YELLOW}📦 Installing Certbot...${NC}"
        apt update
        apt install -y certbot python3-certbot-nginx
    fi
    
    read -p "Do you want to set up SSL certificate with Let's Encrypt now? (yes/no): " SETUP_SSL
    
    if [ "$SETUP_SSL" = "yes" ]; then
        echo ""
        read -p "Enter email for Let's Encrypt notifications: " EMAIL
        
        echo -e "${YELLOW}🔐 Obtaining SSL certificate...${NC}"
        
        # Use the regular config with SSL
        certbot --nginx -d "$DOMAIN" --non-interactive --agree-tos --email "$EMAIL" --redirect || {
            echo -e "${RED}❌ Failed to obtain SSL certificate${NC}"
            echo -e "${YELLOW}   Make sure DNS is pointing to this server${NC}"
            echo -e "${YELLOW}   Check firewall allows ports 80 and 443${NC}"
        }
        
        systemctl reload nginx
    fi
fi

# Final summary
echo ""
echo -e "${GREEN}✅ Nginx Reverse Proxy Setup Complete!${NC}"
echo ""
echo -e "${BLUE}Summary:${NC}"
echo -e "  Domain: $DOMAIN"
echo -e "  Nginx Config: $NGINX_CONFIG"
echo -e "  Nextcloud Port: $NEXTCLOUD_PORT"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
if [ "$SETUP_SSL" != "yes" ]; then
    echo -e "  1. Set up SSL certificate: sudo certbot --nginx -d $DOMAIN"
fi
echo -e "  2. Access Nextcloud at: http://$DOMAIN (or https:// if SSL is configured)"
echo -e "  3. Log in with admin credentials"
echo -e "  4. Install Calendar app from Apps menu"
echo ""
echo -e "${YELLOW}Troubleshooting:${NC}"
echo -e "  - Check Nginx logs: sudo tail -f /var/log/nginx/error.log"
echo -e "  - Check Nextcloud logs: docker logs nextcloud"
echo -e "  - Test DNS: dig $DOMAIN"
echo -e "  - Test HTTP: curl -I http://$DOMAIN"
