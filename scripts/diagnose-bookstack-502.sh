#!/bin/bash

# Diagnostic script for BookStack 502 Bad Gateway error

echo "==================================="
echo "BookStack 502 Error Diagnostics"
echo "==================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check 1: Is BookStack container running?
echo -e "${YELLOW}[1] Checking if BookStack container is running...${NC}"
if docker ps | grep -q "bookstack"; then
    echo -e "${GREEN}✓ BookStack container is running${NC}"
    docker ps | grep bookstack
else
    echo -e "${RED}✗ BookStack container is NOT running${NC}"
    echo "   Start it with: docker-compose -f docker-compose.bookstack.yml up -d"
    exit 1
fi
echo ""

# Check 2: Is BookStack container healthy?
echo -e "${YELLOW}[2] Checking BookStack container health...${NC}"
BOOKSTACK_STATUS=$(docker inspect bookstack --format '{{.State.Status}}' 2>/dev/null)
if [ "$BOOKSTACK_STATUS" = "running" ]; then
    echo -e "${GREEN}✓ Container status: $BOOKSTACK_STATUS${NC}"
else
    echo -e "${RED}✗ Container status: $BOOKSTACK_STATUS${NC}"
    echo "   Check logs: docker logs bookstack"
fi
echo ""

# Check 3: Can we access BookStack directly on port 8084?
echo -e "${YELLOW}[3] Testing direct access to BookStack on port 8084...${NC}"
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8084 | grep -q "200\|302\|301"; then
    echo -e "${GREEN}✓ BookStack is accessible on localhost:8084${NC}"
else
    echo -e "${RED}✗ BookStack is NOT accessible on localhost:8084${NC}"
    echo "   Response code: $(curl -s -o /dev/null -w "%{http_code}" http://localhost:8084)"
    echo "   Check if port 8084 is exposed: docker ps | grep bookstack"
fi
echo ""

# Check 4: Is nginx container running?
echo -e "${YELLOW}[4] Checking nginx container...${NC}"
if docker ps | grep -q "facultyportfolio-web"; then
    echo -e "${GREEN}✓ Nginx container is running${NC}"
    NGINX_CONTAINER="facultyportfolio-web"
else
    echo -e "${RED}✗ Nginx container is NOT running${NC}"
    exit 1
fi
echo ""

# Check 5: Is nginx configured for BookStack?
echo -e "${YELLOW}[5] Checking nginx configuration for BookStack...${NC}"
NGINX_CONF=$(docker exec "$NGINX_CONTAINER" find /etc/nginx -name "*.conf" -type f 2>/dev/null | grep -E "(default|nginx\.conf)" | head -1 || echo "/etc/nginx/conf.d/default.conf")

if docker exec "$NGINX_CONTAINER" grep -q "site.itechportfolio.xyz" "$NGINX_CONF" 2>/dev/null; then
    echo -e "${GREEN}✓ BookStack config found in nginx${NC}"
    echo "   Config file: $NGINX_CONF"
    
    # Show the proxy_pass target
    PROXY_TARGET=$(docker exec "$NGINX_CONTAINER" grep -A 5 "site.itechportfolio.xyz" "$NGINX_CONF" 2>/dev/null | grep "proxy_pass" | awk '{print $2}' | tr -d ';')
    echo "   Proxy target: $PROXY_TARGET"
else
    echo -e "${RED}✗ BookStack config NOT found in nginx${NC}"
    echo "   Run setup script: sudo ./scripts/setup-bookstack.sh"
fi
echo ""

# Check 6: Can nginx container reach BookStack?
echo -e "${YELLOW}[6] Testing connectivity from nginx container to BookStack...${NC}"

# Check if they're on the same network
NGINX_NETWORKS=$(docker inspect "$NGINX_CONTAINER" --format '{{range $key, $value := .NetworkSettings.Networks}}{{$key}} {{end}}' 2>/dev/null)
BOOKSTACK_NETWORKS=$(docker inspect bookstack --format '{{range $key, $value := .NetworkSettings.Networks}}{{$key}} {{end}}' 2>/dev/null)

SHARED_NETWORK=""
for nginx_net in $NGINX_NETWORKS; do
    for bookstack_net in $BOOKSTACK_NETWORKS; do
        if [ "$nginx_net" = "$bookstack_net" ]; then
            SHARED_NETWORK="$nginx_net"
            break 2
        fi
    done
done

if [ -n "$SHARED_NETWORK" ]; then
    echo -e "${GREEN}✓ Both containers are on network: $SHARED_NETWORK${NC}"
    
    # Test if nginx can reach bookstack by name
    if docker exec "$NGINX_CONTAINER" ping -c 1 bookstack 2>/dev/null | grep -q "1 packets transmitted"; then
        echo -e "${GREEN}✓ Nginx can ping BookStack container by name${NC}"
        
        # Test HTTP connection
        if docker exec "$NGINX_CONTAINER" wget -q --spider http://bookstack:80 2>/dev/null || docker exec "$NGINX_CONTAINER" curl -s -o /dev/null http://bookstack:80 2>/dev/null; then
            echo -e "${GREEN}✓ Nginx can reach BookStack on http://bookstack:80${NC}"
        else
            echo -e "${RED}✗ Nginx cannot reach BookStack on http://bookstack:80${NC}"
            echo "   Check BookStack logs: docker logs bookstack"
        fi
    else
        echo -e "${RED}✗ Nginx cannot ping BookStack container${NC}"
    fi
else
    echo -e "${YELLOW}⚠ Containers are NOT on the same network${NC}"
    echo "   Nginx networks: $NGINX_NETWORKS"
    echo "   BookStack networks: $BOOKSTACK_NETWORKS"
    echo ""
    echo "   Connecting nginx to facultyportfolio_default network..."
    docker network connect facultyportfolio_default "$NGINX_CONTAINER" 2>/dev/null && {
        echo -e "${GREEN}✓ Connected nginx to facultyportfolio_default${NC}"
        echo "   Restart nginx: docker restart $NGINX_CONTAINER"
    } || echo -e "${YELLOW}Could not connect (may already be connected)${NC}"
fi
echo ""

# Check 7: Check BookStack logs for errors
echo -e "${YELLOW}[7] Checking BookStack logs (last 20 lines)...${NC}"
docker logs bookstack --tail 20 2>&1 | tail -10
echo ""

# Check 8: Check nginx error logs
echo -e "${YELLOW}[8] Checking nginx error logs...${NC}"
docker exec "$NGINX_CONTAINER" tail -20 /var/log/nginx/error.log 2>/dev/null | grep -i "bookstack\|site.itechportfolio\|502" || echo "No relevant errors found"
echo ""

# Summary and recommendations
echo "==================================="
echo "Summary & Recommendations"
echo "==================================="
echo ""

if docker ps | grep -q "bookstack" && curl -s -o /dev/null -w "%{http_code}" http://localhost:8084 | grep -q "200\|302\|301"; then
    if [ -n "$SHARED_NETWORK" ]; then
        echo -e "${GREEN}✓ BookStack is running and accessible${NC}"
        echo -e "${GREEN}✓ Containers are on the same network${NC}"
        echo ""
        echo "If you still get 502, try:"
        echo "  1. Restart nginx: docker restart $NGINX_CONTAINER"
        echo "  2. Check nginx config: docker exec $NGINX_CONTAINER nginx -t"
        echo "  3. Verify proxy_pass target in nginx config matches: http://bookstack:80"
    else
        echo -e "${YELLOW}⚠ Network issue detected${NC}"
        echo "  Run: docker network connect facultyportfolio_default $NGINX_CONTAINER"
        echo "  Then: docker restart $NGINX_CONTAINER"
    fi
else
    echo -e "${RED}✗ BookStack is not accessible${NC}"
    echo "  Check: docker logs bookstack"
    echo "  Restart: docker-compose -f docker-compose.bookstack.yml restart"
fi

echo ""
