#!/bin/bash

# Add Nextcloud to Docker Nginx Configuration
# Since Faculty Portfolio uses Docker, we'll add Nextcloud to the same setup

set -e

echo "🐳 Adding Nextcloud to Docker Nginx Configuration"
echo "================================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
FACULTY_DIR="$HOME/facultyPortfolio"

if [ ! -d "$FACULTY_DIR" ]; then
    FACULTY_DIR="/root/facultyPortfolio"
fi

if [ ! -d "$FACULTY_DIR" ]; then
    echo -e "${RED}❌ Could not find Faculty Portfolio directory${NC}"
    read -p "Enter Faculty Portfolio directory path: " FACULTY_DIR
fi

cd "$FACULTY_DIR"

# Check if docker-compose.yml exists
if [ ! -f "docker-compose.yml" ]; then
    echo -e "${RED}❌ docker-compose.yml not found in $FACULTY_DIR${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Found Faculty Portfolio at: $FACULTY_DIR${NC}"

# Check if there's an nginx container
if docker ps | grep -q "facultyportfolio-web"; then
    echo -e "${GREEN}✅ Found Faculty Portfolio web container${NC}"
    
    # Find nginx config in the container
    NGINX_CONF_PATH=$(docker exec facultyportfolio-web find /etc/nginx -name "*.conf" -type f 2>/dev/null | grep -E "(default|nginx)" | head -1 || echo "")
    
    if [ -n "$NGINX_CONF_PATH" ]; then
        echo -e "${BLUE}Found Nginx config in container: $NGINX_CONF_PATH${NC}"
        
        # Copy config from container
        docker cp facultyportfolio-web:$NGINX_CONF_PATH /tmp/nginx-config-backup.conf
        
        # Add Nextcloud server block
        echo ""
        echo -e "${BLUE}Adding Nextcloud configuration...${NC}"
        
        NEXTCLOUD_BLOCK=$(cat <<'EOF'

    # Nextcloud - opcr.itechportfolio.xyz
    server {
        listen 80;
        server_name opcr.itechportfolio.xyz;

        client_max_body_size 512M;
        client_body_buffer_size 512M;

        location / {
            proxy_pass http://host.docker.internal:8082;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header CF-Connecting-IP $http_cf_connecting_ip;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
            proxy_set_header X-Forwarded-Host $host;
            
            proxy_http_version 1.1;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection "upgrade";
            
            proxy_connect_timeout 360s;
            proxy_send_timeout 360s;
            proxy_read_timeout 360s;
        }
    }
EOF
)
        
        # Append to config
        echo "$NEXTCLOUD_BLOCK" >> /tmp/nginx-config-backup.conf
        
        # Copy back to container
        docker cp /tmp/nginx-config-backup.conf facultyportfolio-web:$NGINX_CONF_PATH
        
        # Reload nginx in container
        docker exec facultyportfolio-web nginx -s reload || docker exec facultyportfolio-web nginx -t
        
        echo -e "${GREEN}✅ Added Nextcloud to Docker Nginx config${NC}"
    else
        echo -e "${YELLOW}⚠️  Could not find Nginx config in container${NC}"
        echo -e "${YELLOW}   Using alternative method...${NC}"
    fi
fi

# Alternative: Check if we can access host from container
echo ""
echo -e "${BLUE}Checking Docker network setup...${NC}"

# Check if host.docker.internal is available
if docker exec facultyportfolio-web ping -c 1 host.docker.internal 2>/dev/null; then
    echo -e "${GREEN}✅ Container can access host.docker.internal${NC}"
else
    echo -e "${YELLOW}⚠️  Container cannot access host.docker.internal${NC}"
    echo -e "${YELLOW}   Need to add extra_hosts to docker-compose.yml${NC}"
fi

echo ""
echo -e "${BLUE}Alternative Solution: Use Host-Level Nginx${NC}"
echo -e "${YELLOW}Since Docker is using port 80, we can:${NC}"
echo ""
echo -e "  1. Change Faculty Portfolio to use a different port (e.g., 8080)"
echo -e "  2. Install host-level Nginx on port 80"
echo -e "  3. Nginx proxies to:"
echo -e "     - Faculty Portfolio: localhost:8080"
echo -e "     - Nextcloud: localhost:8082"
echo ""
read -p "Do you want to set up host-level Nginx? (yes/no): " SETUP_HOST_NGINX

if [ "$SETUP_HOST_NGINX" = "yes" ]; then
    echo ""
    echo -e "${BLUE}Setting up host-level Nginx...${NC}"
    
    # Check what port Faculty Portfolio is actually using
    FACULTY_PORT=$(docker ps --format "table {{.Ports}}" | grep facultyportfolio-web | grep -oP '\d+(?=->80)' | head -1 || echo "8081")
    
    echo -e "${YELLOW}Faculty Portfolio appears to be on port: $FACULTY_PORT${NC}"
    
    # Create host-level nginx config
    cat > /tmp/nginx-multi-service.conf <<EOF
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
    }
}

# Nextcloud
server {
    listen 80;
    server_name opcr.itechportfolio.xyz;

    client_max_body_size 512M;
    client_body_buffer_size 512M;

    location / {
        proxy_pass http://127.0.0.1:8082;
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
    
    echo -e "${GREEN}✅ Created host-level Nginx config${NC}"
    echo -e "${YELLOW}   Config saved to: /tmp/nginx-multi-service.conf${NC}"
    echo ""
    echo -e "${YELLOW}Next steps:${NC}"
    echo -e "  1. Stop Docker containers using port 80"
    echo -e "  2. Install host-level Nginx: sudo apt install nginx"
    echo -e "  3. Copy config: sudo cp /tmp/nginx-multi-service.conf /etc/nginx/sites-available/multi-service"
    echo -e "  4. Enable: sudo ln -s /etc/nginx/sites-available/multi-service /etc/nginx/sites-enabled/"
    echo -e "  5. Test: sudo nginx -t"
    echo -e "  6. Start: sudo systemctl start nginx"
fi

echo ""
echo -e "${GREEN}✅ Setup complete!${NC}"
