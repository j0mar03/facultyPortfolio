#!/bin/bash

# Simple Host-Level Nginx Setup for Docker Services
# This sets up Nginx on the host to proxy to Docker containers

set -e

echo "🌐 Setting up Host-Level Nginx for Docker Services"
echo "=================================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}❌ Please run with sudo${NC}"
    exit 1
fi

# Find what ports are actually being used
echo -e "${BLUE}Step 1: Checking Docker container ports...${NC}"

FACULTY_PORT=$(docker ps --format "{{.Ports}}" | grep -oP '\d+(?=->80/tcp)' | head -1 || echo "8081")
NEXTCLOUD_PORT="8082"

echo -e "${GREEN}✅ Faculty Portfolio port: $FACULTY_PORT${NC}"
echo -e "${GREEN}✅ Nextcloud port: $NEXTCLOUD_PORT${NC}"

# Check if something is using port 80
if netstat -tlnp 2>/dev/null | grep -q ":80 " || ss -tlnp 2>/dev/null | grep -q ":80 "; then
    echo -e "${YELLOW}⚠️  Port 80 is in use${NC}"
    echo -e "${YELLOW}   We need to stop the container using port 80 first${NC}"
    echo ""
    echo -e "${BLUE}Containers using port 80:${NC}"
    docker ps --format "table {{.Names}}\t{{.Ports}}" | grep ":80"
    echo ""
    read -p "Do you want to change Faculty Portfolio to use port 8080 instead? (yes/no): " CHANGE_PORT
    
    if [ "$CHANGE_PORT" = "yes" ]; then
        echo -e "${YELLOW}   You'll need to update docker-compose.yml to use port 8080:80${NC}"
        echo -e "${YELLOW}   Then restart: cd ~/facultyPortfolio && docker compose up -d${NC}"
        FACULTY_PORT="8080"
    else
        echo -e "${RED}❌ Cannot proceed while port 80 is in use${NC}"
        exit 1
    fi
fi

# Install Nginx if needed
if ! command -v nginx &> /dev/null; then
    echo -e "${YELLOW}📦 Installing Nginx...${NC}"
    apt update
    apt install -y nginx
fi

# Create Nginx config
echo ""
echo -e "${BLUE}Step 2: Creating Nginx configuration...${NC}"

NGINX_CONFIG="/etc/nginx/sites-available/multi-service"

cat > "$NGINX_CONFIG" <<EOF
# Faculty Portfolio
server {
    listen 80;
    server_name portfolio.itechportfolio.xyz;

    location / {
        proxy_pass http://127.0.0.1:$FACULTY_PORT;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "upgrade";
    }
}

# Nextcloud
server {
    listen 80;
    server_name opcr.itechportfolio.xyz;

    client_max_body_size 512M;
    client_body_buffer_size 512M;

    location / {
        proxy_pass http://127.0.0.1:$NEXTCLOUD_PORT;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header CF-Connecting-IP \$http_cf_connecting_ip;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_set_header X-Forwarded-Host \$host;
        
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "upgrade";
        
        proxy_connect_timeout 360s;
        proxy_send_timeout 360s;
        proxy_read_timeout 360s;
    }
}
EOF

echo -e "${GREEN}✅ Created Nginx config: $NGINX_CONFIG${NC}"

# Enable site
echo ""
echo -e "${BLUE}Step 3: Enabling site...${NC}"
ln -sf "$NGINX_CONFIG" /etc/nginx/sites-enabled/multi-service
rm -f /etc/nginx/sites-enabled/default 2>/dev/null || true
echo -e "${GREEN}✅ Enabled multi-service site${NC}"

# Test config
echo ""
echo -e "${BLUE}Step 4: Testing configuration...${NC}"
if nginx -t; then
    echo -e "${GREEN}✅ Configuration is valid${NC}"
else
    echo -e "${RED}❌ Configuration test failed${NC}"
    exit 1
fi

# Start/reload Nginx
echo ""
echo -e "${BLUE}Step 5: Starting Nginx...${NC}"
if systemctl is-active --quiet nginx; then
    systemctl reload nginx
    echo -e "${GREEN}✅ Nginx reloaded${NC}"
else
    systemctl start nginx
    systemctl enable nginx
    echo -e "${GREEN}✅ Nginx started and enabled${NC}"
fi

echo ""
echo -e "${GREEN}✅ Host-level Nginx configured!${NC}"
echo ""
echo -e "${BLUE}Summary:${NC}"
echo -e "  Faculty Portfolio: portfolio.itechportfolio.xyz → localhost:$FACULTY_PORT"
echo -e "  Nextcloud: opcr.itechportfolio.xyz → localhost:$NEXTCLOUD_PORT"
echo ""
echo -e "${YELLOW}Important:${NC}"
echo -e "  Make sure Faculty Portfolio Docker container is NOT using port 80"
echo -e "  Update docker-compose.yml: Change '80:80' to '$FACULTY_PORT:80'"
echo -e "  Then restart: cd ~/facultyPortfolio && docker compose up -d"
