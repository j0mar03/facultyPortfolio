#!/bin/bash

# Quick script to start Nginx

set -e

echo "🚀 Starting Nginx..."

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

# Check if Nginx is installed
if ! command -v nginx &> /dev/null; then
    echo -e "${YELLOW}⚠️  Nginx is not installed. Installing...${NC}"
    apt update
    apt install -y nginx
fi

# Check current status
if systemctl is-active --quiet nginx; then
    echo -e "${GREEN}✅ Nginx is already running${NC}"
    systemctl status nginx --no-pager -l
else
    echo -e "${YELLOW}📝 Starting Nginx...${NC}"
    
    # Test configuration first
    if nginx -t; then
        echo -e "${GREEN}✅ Configuration is valid${NC}"
        
        # Start Nginx
        if systemctl start nginx; then
            echo -e "${GREEN}✅ Nginx started successfully${NC}"
            
            # Enable on boot
            systemctl enable nginx
            echo -e "${GREEN}✅ Nginx enabled to start on boot${NC}"
            
            # Show status
            echo ""
            systemctl status nginx --no-pager -l
        else
            echo -e "${RED}❌ Failed to start Nginx${NC}"
            echo -e "${YELLOW}   Checking logs...${NC}"
            journalctl -u nginx -n 20 --no-pager
            exit 1
        fi
    else
        echo -e "${RED}❌ Nginx configuration test failed${NC}"
        echo -e "${YELLOW}   Fix configuration errors first${NC}"
        exit 1
    fi
fi

echo ""
echo -e "${GREEN}✅ Nginx is running!${NC}"
echo ""
echo -e "${YELLOW}Useful commands:${NC}"
echo -e "  Check status: sudo systemctl status nginx"
echo -e "  View logs: sudo tail -f /var/log/nginx/error.log"
echo -e "  Test config: sudo nginx -t"
echo -e "  Reload: sudo systemctl reload nginx"
