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

# Check for docker
if ! command_exists docker; then
    echo -e "${RED}Error: docker is not installed${NC}"
    exit 1
fi

# Check for docker compose (v2) or docker-compose (v1)
DOCKER_COMPOSE_CMD=""
if docker compose version >/dev/null 2>&1; then
    DOCKER_COMPOSE_CMD="docker compose"
    echo -e "${GREEN}Found Docker Compose v2 (docker compose)${NC}"
elif command_exists docker-compose; then
    DOCKER_COMPOSE_CMD="docker-compose"
    echo -e "${GREEN}Found Docker Compose v1 (docker-compose)${NC}"
else
    echo -e "${RED}Error: docker compose or docker-compose is not installed${NC}"
    echo "Please install Docker Compose v2 (recommended) or v1"
    exit 1
fi

# Check for nginx
if ! command_exists nginx; then
    echo -e "${RED}Error: nginx is not installed${NC}"
    exit 1
fi

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
    echo "Please start it with: $DOCKER_COMPOSE_CMD up -d db"
    exit 1
fi
echo -e "${GREEN}Database is running${NC}"
echo ""

# Setup nginx configuration for site.itechportfolio.xyz (BookStack ONLY)
echo -e "${YELLOW}Setting up nginx configuration for BookStack...${NC}"

# Check if nginx is in Docker or on host
NGINX_IN_DOCKER=false
DOCKER_NGINX_CONF=""
DOCKER_NGINX_CONTAINER=""

if docker ps | grep -q "facultyportfolio-web"; then
    NGINX_IN_DOCKER=true
    DOCKER_NGINX_CONTAINER="facultyportfolio-web"
    # Find nginx config in container
    DOCKER_NGINX_CONF=$(docker exec "$DOCKER_NGINX_CONTAINER" find /etc/nginx -name "*.conf" -type f 2>/dev/null | grep -E "(default|nginx\.conf)" | head -1 || echo "/etc/nginx/conf.d/default.conf")
    echo -e "${GREEN}✓ Found nginx in Docker container: $DOCKER_NGINX_CONTAINER${NC}"
    echo -e "${GREEN}✓ Nginx config path: $DOCKER_NGINX_CONF${NC}"
fi

if [ "$NGINX_IN_DOCKER" = true ]; then
    # Add BookStack config to Docker nginx
    echo -e "${YELLOW}Adding BookStack configuration to Docker nginx...${NC}"
    
    # Backup current config
    docker cp "$DOCKER_NGINX_CONTAINER:$DOCKER_NGINX_CONF" /tmp/nginx-config-backup-$(date +%Y%m%d_%H%M%S).conf
    echo -e "${GREEN}✓ Backup created${NC}"
    
    # Copy config from container
    docker cp "$DOCKER_NGINX_CONTAINER:$DOCKER_NGINX_CONF" /tmp/nginx-current.conf
    
    # Check if BookStack config already exists
    if grep -q "site.itechportfolio.xyz" /tmp/nginx-current.conf; then
        echo -e "${YELLOW}BookStack configuration already exists in Docker nginx (skipping)${NC}"
    else
        # Add BookStack server block
        BOOKSTACK_BLOCK=$(cat <<'EOF'

# BookStack - site.itechportfolio.xyz
server {
    listen 80;
    listen [::]:80;
    server_name site.itechportfolio.xyz;

    # Cloudflare headers
    set_real_ip_from 0.0.0.0/0;
    real_ip_header CF-Connecting-IP;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Increase upload size for BookStack
    client_max_body_size 100M;
    client_body_buffer_size 100M;

    # Proxy to BookStack container (on host)
    location / {
        proxy_pass http://host.docker.internal:8084;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        
        # Cloudflare specific headers
        proxy_set_header CF-Connecting-IP $http_cf_connecting_ip;
        proxy_set_header CF-Ray $http_cf_ray;
        proxy_set_header CF-Visitor $http_cf_visitor;
        
        # Standard proxy headers
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto https;
        proxy_set_header X-Forwarded-Host $host;
        proxy_set_header X-Forwarded-Ssl on;
        proxy_set_header X-Forwarded-Port 443;
        
        # Timeouts
        proxy_connect_timeout 300s;
        proxy_send_timeout 300s;
        proxy_read_timeout 300s;
    }
}
EOF
)
        
        # Append to config
        echo "$BOOKSTACK_BLOCK" >> /tmp/nginx-current.conf
        
        # Copy config to container first
        docker cp /tmp/nginx-current.conf "$DOCKER_NGINX_CONTAINER:$DOCKER_NGINX_CONF"
        
        # Test config
        echo -e "${YELLOW}Testing nginx configuration...${NC}"
        if docker exec "$DOCKER_NGINX_CONTAINER" nginx -t; then
            echo -e "${GREEN}✓ Nginx configuration is valid${NC}"
            
            # Reload nginx in container
            echo -e "${YELLOW}Reloading nginx in Docker container...${NC}"
            if docker exec "$DOCKER_NGINX_CONTAINER" nginx -s reload 2>/dev/null; then
                echo -e "${GREEN}✓ Nginx reloaded in Docker container${NC}"
            else
                echo -e "${YELLOW}Reload failed, restarting container...${NC}"
                docker restart "$DOCKER_NGINX_CONTAINER"
                sleep 2
                echo -e "${GREEN}✓ Container restarted${NC}"
            fi
        else
            echo -e "${RED}Nginx configuration test failed. Restoring backup...${NC}"
            BACKUP_FILE=$(ls -t /tmp/nginx-config-backup-*.conf | head -1)
            if [ -f "$BACKUP_FILE" ]; then
                docker cp "$BACKUP_FILE" "$DOCKER_NGINX_CONTAINER:$DOCKER_NGINX_CONF"
                echo -e "${GREEN}✓ Backup restored${NC}"
            fi
            exit 1
        fi
        
        # Also update the source file for persistence
        DOCKER_NGINX_SOURCE="docker/nginx/default.conf"
        if [ -f "$DOCKER_NGINX_SOURCE" ]; then
            if ! grep -q "site.itechportfolio.xyz" "$DOCKER_NGINX_SOURCE"; then
                echo "$BOOKSTACK_BLOCK" >> "$DOCKER_NGINX_SOURCE"
                echo -e "${GREEN}✓ Updated source nginx config: $DOCKER_NGINX_SOURCE${NC}"
                echo -e "${YELLOW}Note: Rebuild container to persist changes: docker-compose build web${NC}"
            fi
        fi
    fi
else
    # Host nginx setup (original code)
    BOOKSTACK_NGINX_CONF="/etc/nginx/sites-available/bookstack"
    BOOKSTACK_NGINX_ENABLED="/etc/nginx/sites-enabled/bookstack"

    if [ ! -f "$BOOKSTACK_NGINX_CONF" ]; then
        cp scripts/nginx/bookstack.conf "$BOOKSTACK_NGINX_CONF"
        ln -sf "$BOOKSTACK_NGINX_CONF" "$BOOKSTACK_NGINX_ENABLED"
        echo -e "${GREEN}BookStack nginx configuration created${NC}"
    else
        echo -e "${YELLOW}BookStack nginx configuration already exists (skipping)${NC}"
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

    # Start or reload nginx
    echo -e "${YELLOW}Starting/Reloading nginx...${NC}"
    if systemctl is-active --quiet nginx; then
        systemctl reload nginx
        echo -e "${GREEN}Nginx reloaded${NC}"
    else
        systemctl start nginx
        systemctl enable nginx
        echo -e "${GREEN}Nginx started and enabled${NC}"
    fi
fi

# Note: Portfolio configuration is NOT touched - it's already working!
echo -e "${GREEN}✓ Portfolio configuration left untouched (already working)${NC}"
echo ""

# Note about Cloudflare SSL
echo -e "${YELLOW}Note: SSL/HTTPS is handled by Cloudflare${NC}"
echo -e "${GREEN}No SSL certificates needed on the server (Cloudflare Flexible mode)${NC}"
echo -e "${YELLOW}Make sure your domains are proxied through Cloudflare (orange cloud)${NC}"
echo ""

# Start BookStack
echo -e "${YELLOW}Starting BookStack containers...${NC}"
$DOCKER_COMPOSE_CMD -f docker-compose.bookstack.yml up -d
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
echo "BookStack Service:"
echo "  - URL:  https://site.itechportfolio.xyz"
echo ""
echo -e "${GREEN}Note: Your existing portfolio at portfolio.itechportfolio.xyz is unchanged${NC}"
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
echo "  $DOCKER_COMPOSE_CMD -f docker-compose.bookstack.yml logs -f"
echo ""
echo "To stop BookStack:"
echo "  $DOCKER_COMPOSE_CMD -f docker-compose.bookstack.yml down"
echo ""
