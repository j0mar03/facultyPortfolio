#!/bin/bash

# Find Existing Nginx Configuration
# Helps identify where your Faculty Portfolio Nginx config is

set -e

echo "🔍 Finding Nginx Configuration Files"
echo "====================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}Checking common Nginx configuration locations...${NC}"
echo ""

# Check sites-available
if [ -d "/etc/nginx/sites-available" ]; then
    echo -e "${GREEN}📁 /etc/nginx/sites-available/${NC}"
    for file in /etc/nginx/sites-available/*; do
        if [ -f "$file" ] && ! [ -L "$file" ]; then
            echo -e "   ${YELLOW}$(basename $file)${NC}"
            if grep -q "server_name" "$file"; then
                echo -e "      → Contains server blocks"
                grep "server_name" "$file" | head -3 | sed 's/^/         /'
            fi
        fi
    done
    echo ""
fi

# Check sites-enabled
if [ -d "/etc/nginx/sites-enabled" ]; then
    echo -e "${GREEN}📁 /etc/nginx/sites-enabled/${NC}"
    for file in /etc/nginx/sites-enabled/*; do
        if [ -f "$file" ] || [ -L "$file" ]; then
            REAL_FILE=$(readlink -f "$file" 2>/dev/null || echo "$file")
            echo -e "   ${YELLOW}$(basename $file)${NC}"
            if [ -f "$REAL_FILE" ] && grep -q "server_name" "$REAL_FILE"; then
                echo -e "      → Contains server blocks"
                grep "server_name" "$REAL_FILE" | head -3 | sed 's/^/         /'
            fi
        fi
    done
    echo ""
fi

# Check conf.d
if [ -d "/etc/nginx/conf.d" ]; then
    echo -e "${GREEN}📁 /etc/nginx/conf.d/${NC}"
    for file in /etc/nginx/conf.d/*; do
        if [ -f "$file" ]; then
            echo -e "   ${YELLOW}$(basename $file)${NC}"
            if grep -q "server_name" "$file"; then
                echo -e "      → Contains server blocks"
                grep "server_name" "$file" | head -3 | sed 's/^/         /'
            fi
        fi
    done
    echo ""
fi

# Check main nginx.conf
if [ -f "/etc/nginx/nginx.conf" ]; then
    echo -e "${GREEN}📄 /etc/nginx/nginx.conf${NC}"
    if grep -q "server_name" /etc/nginx/nginx.conf; then
        echo -e "   ${YELLOW}→ Contains server blocks${NC}"
        grep "server_name" /etc/nginx/nginx.conf | head -3 | sed 's/^/      /'
    else
        echo -e "   → Main config file (usually includes other files)"
    fi
    echo ""
fi

# Check what's listening on port 80
echo -e "${BLUE}Checking what's using port 80...${NC}"
if command -v netstat &> /dev/null; then
    PORT_80=$(netstat -tlnp 2>/dev/null | grep ":80 " || echo "")
elif command -v ss &> /dev/null; then
    PORT_80=$(ss -tlnp 2>/dev/null | grep ":80 " || echo "")
fi

if [ -n "$PORT_80" ]; then
    echo -e "${GREEN}✅ Port 80 is in use:${NC}"
    echo "$PORT_80" | sed 's/^/   /'
else
    echo -e "${YELLOW}⚠️  Nothing found listening on port 80${NC}"
fi

echo ""
echo -e "${BLUE}Recommendation:${NC}"
echo -e "   Run: sudo bash scripts/add-nextcloud-to-existing-nginx.sh"
echo -e "   It will automatically find and update your Nginx config"
