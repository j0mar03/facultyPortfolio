# Faculty Portfolio - Complete Architecture Overview

Visual overview of the complete Faculty Portfolio ecosystem with all services.

## 🌐 Service Architecture

```
                         INTERNET (HTTPS)
                              │
                              ↓
                    ┌─────────────────┐
                    │  Cloudflare DNS │
                    └─────────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        │                     │                     │
        ↓                     ↓                     ↓
portfolio.itechportfolio.xyz  site.itechportfolio.xyz
opcr.itechportfolio.xyz       asset.itechportfolio.xyz
        │                     │                     │
        │                     │                     │
        └─────────────────────┼─────────────────────┘
                              ↓
                    ┌─────────────────┐
                    │  Nginx (Host)   │
                    │  Reverse Proxy  │
                    │   + SSL/TLS     │
                    └─────────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        │                     │                     │
        ↓                     ↓                     ↓
    Portfolio           BookStack              Nextcloud
    (Port 8081)         (Port 8084)           (Port 8082)
        │                     │                     │
        │                     └─────────────────────┤
        │                                           │
        └───────────────────┬───────────────────────┘
                            ↓
                   ┌────────────────┐
                   │  MySQL 8.0 DB  │
                   │  (Port 3306)   │
                   └────────────────┘
                            │
                ┌───────────┼───────────┐
                ↓           ↓           ↓
        faculty_portfolio  bookstack  nextcloud
            + snipeit
```

## 📊 Service Details

### Main Services

| Service | Domain | Port | Technology | Purpose |
|---------|--------|------|------------|---------|
| **Faculty Portfolio** | portfolio.itechportfolio.xyz | 8081 | Laravel + PHP 8.2 | Faculty portfolio management system |
| **BookStack** | site.itechportfolio.xyz | 8084 | PHP + BookStack | Documentation & knowledge base |
| **Nextcloud** | opcr.itechportfolio.xyz | 8082 | PHP + Nextcloud | OPCR file storage & collaboration |
| **Snipe-IT** | asset.itechportfolio.xyz | 8083 | PHP + Snipe-IT | IT asset management |

### Infrastructure

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Web Server** | Nginx | Reverse proxy + SSL termination |
| **Database** | MySQL 8.0 | Shared database server |
| **SSL/TLS** | Let's Encrypt | Free SSL certificates |
| **Containerization** | Docker + Docker Compose | Service isolation & management |
| **DNS** | Cloudflare | Domain management & CDN |

## 🗄️ Database Structure

```
facultyportfolio-db (MySQL 8.0 Container)
├── faculty_portfolio
│   ├── users
│   ├── portfolios
│   ├── courses
│   └── ... (Laravel tables)
│
├── bookstack
│   ├── users
│   ├── books
│   ├── chapters
│   ├── pages
│   └── ... (BookStack tables)
│
├── nextcloud
│   ├── oc_users
│   ├── oc_files
│   ├── oc_storages
│   └── ... (Nextcloud tables)
│
└── snipeit
    ├── users
    ├── assets
    ├── categories
    └── ... (Snipe-IT tables)
```

## 📁 Directory Structure

```
/home/jomar/dev/projects/facultyPortfolio/     # Main project
├── app/                                       # Laravel application
├── docker-compose.yml                         # Main portfolio
├── docker-compose.bookstack.yml               # BookStack
├── docker-compose.nextcloud.yml               # Nextcloud
├── docker-compose.snipeit.yml                 # Snipe-IT
│
├── scripts/
│   ├── setup-bookstack.sh                     # BookStack setup
│   ├── setup-nextcloud.sh                     # Nextcloud setup
│   ├── setup-snipeit.sh                       # Snipe-IT setup
│   └── nginx/
│       ├── portfolio.conf                     # Portfolio nginx
│       ├── bookstack.conf                     # BookStack nginx
│       ├── nextcloud.conf                     # Nextcloud nginx
│       └── snipeit.conf                       # Snipe-IT nginx
│
└── Documentation/
    ├── BOOKSTACK_QUICKSTART.md
    ├── BOOKSTACK_SETUP.md
    ├── SERVICES_OVERVIEW.md
    └── ... (more docs)

/opt/services/                                 # Optional external services
├── nextcloud/                                 # (Alternative location)
└── snipeit/                                   # (Alternative location)
```

## 🔄 Data Flow

### User Access Flow
```
1. User → https://site.itechportfolio.xyz
2. DNS → Resolves to server IP
3. Nginx → Receives request on port 443
4. SSL → Decrypts HTTPS
5. Proxy → Forwards to localhost:8084
6. BookStack → Processes request
7. MySQL → Fetches data
8. BookStack → Renders page
9. Nginx → Returns response
10. User ← Sees documentation
```

### Docker Network Flow
```
Host Network (br0)
    ├── Nginx (Host)
    │
    └── Docker Bridge (facultyportfolio_default)
        ├── facultyportfolio-web (8081)
        ├── facultyportfolio-app (Laravel)
        ├── facultyportfolio-db (3306)
        │
        └── bookstack-network
            └── bookstack (8084)
```

## 🔐 Security Layers

```
┌─────────────────────────────────────┐
│  Layer 1: Cloudflare                │  DDoS protection, WAF
├─────────────────────────────────────┤
│  Layer 2: SSL/TLS (Let's Encrypt)   │  Encrypted traffic
├─────────────────────────────────────┤
│  Layer 3: Nginx Reverse Proxy       │  Request filtering
├─────────────────────────────────────┤
│  Layer 4: Docker Network Isolation  │  Container isolation
├─────────────────────────────────────┤
│  Layer 5: Application Auth          │  User authentication
├─────────────────────────────────────┤
│  Layer 6: Database Access Control   │  Privilege separation
└─────────────────────────────────────┘
```

## 🚀 Startup Sequence

```bash
# 1. Start Database (shared by all)
docker-compose up -d db

# 2. Start Portfolio
docker-compose up -d

# 3. Start BookStack
docker-compose -f docker-compose.bookstack.yml up -d

# 4. Start Nextcloud
docker-compose -f docker-compose.nextcloud.yml up -d

# 5. Start Snipe-IT
docker-compose -f docker-compose.snipeit.yml up -d

# 6. Verify all services
docker ps
```

## 📈 Resource Usage

| Service | CPU | RAM | Disk | Notes |
|---------|-----|-----|------|-------|
| MySQL | ~5% | 400MB | 2GB | Shared database |
| Portfolio | ~3% | 200MB | 500MB | Laravel app |
| BookStack | ~2% | 150MB | 300MB | Documentation |
| Nextcloud | ~4% | 300MB | Varies | File storage |
| Snipe-IT | ~2% | 150MB | 200MB | Asset mgmt |
| Nginx | ~1% | 50MB | 10MB | Reverse proxy |
| **Total** | ~17% | ~1.25GB | ~3GB+ | Approximate |

## 🔌 Port Mapping

| Internal Port | External Port | Service | Protocol |
|---------------|---------------|---------|----------|
| 80 | 80 | Nginx HTTP | HTTP |
| 443 | 443 | Nginx HTTPS | HTTPS |
| 3306 | 3307 | MySQL | TCP |
| 5173 | 5173 | Vite (Dev) | HTTP |
| 8081 | 8081 | Portfolio | HTTP |
| 8082 | 8082 | Nextcloud | HTTP |
| 8083 | 8083 | Snipe-IT | HTTP |
| 8084 | 8084 | BookStack | HTTP |

**Note**: External ports 80 & 443 are the only ones that should be accessible from the internet. All others are localhost-only.

## 🛠️ Management Commands

### View All Services
```bash
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
```

### Check Service Health
```bash
# Portfolio
curl -I https://portfolio.itechportfolio.xyz

# BookStack
curl -I https://site.itechportfolio.xyz

# Nextcloud
curl -I https://opcr.itechportfolio.xyz

# Snipe-IT
curl -I https://asset.itechportfolio.xyz
```

### Monitor Logs
```bash
# All services in one terminal
docker logs -f facultyportfolio-web &
docker logs -f bookstack &
docker logs -f nextcloud &
docker logs -f snipeit

# Or use docker-compose
docker-compose logs -f
docker-compose -f docker-compose.bookstack.yml logs -f
```

## 📊 Monitoring

### Key Metrics to Monitor

1. **Service Availability** - Are all containers running?
2. **Response Time** - How fast are pages loading?
3. **Database Performance** - Query times, connections
4. **Disk Usage** - Especially for Nextcloud uploads
5. **Memory Usage** - Ensure no memory leaks
6. **SSL Certificate Expiry** - Renew before expiration

### Quick Health Check Script

```bash
#!/bin/bash
echo "=== Service Health Check ==="
echo ""

services=("portfolio.itechportfolio.xyz" "site.itechportfolio.xyz" "opcr.itechportfolio.xyz" "asset.itechportfolio.xyz")

for service in "${services[@]}"; do
    if curl -s -o /dev/null -w "%{http_code}" "https://$service" | grep -q "200\|302\|301"; then
        echo "✅ $service - OK"
    else
        echo "❌ $service - DOWN"
    fi
done

echo ""
echo "=== Container Status ==="
docker ps --format "{{.Names}}: {{.Status}}"
```

## 🔄 Backup Strategy

```
Daily Backups:
├── Database
│   ├── mysqldump all databases
│   └── Store in /backups/db/
│
├── Application Files
│   ├── Portfolio code (git)
│   └── BookStack config volume
│
├── User Data
│   ├── Nextcloud files
│   └── Uploaded documents
│
└── Nginx Configs
    └── /etc/nginx/sites-available/
```

## 🎯 Development vs Production

| Aspect | Development | Production |
|--------|-------------|------------|
| **Domain** | localhost:8081 | portfolio.itechportfolio.xyz |
| **SSL** | Self-signed | Let's Encrypt |
| **Database** | docker-compose db | docker-compose db |
| **Debug Mode** | Enabled | Disabled |
| **Caching** | Disabled | Enabled |
| **Minification** | No | Yes |
| **Hot Reload** | Vite (5173) | N/A |

## 📚 Documentation Index

| Document | Purpose | Audience |
|----------|---------|----------|
| [BOOKSTACK_QUICKSTART.md](BOOKSTACK_QUICKSTART.md) | 5-min setup | Quick start |
| [BOOKSTACK_SETUP.md](BOOKSTACK_SETUP.md) | Complete guide | Detailed setup |
| [SERVICES_OVERVIEW.md](SERVICES_OVERVIEW.md) | All services | Operations |
| [ARCHITECTURE_OVERVIEW.md](ARCHITECTURE_OVERVIEW.md) | This doc | Technical |
| [README_SERVICES.md](README_SERVICES.md) | Organization | Overview |
| [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) | Deployment | DevOps |

## ✅ Health Checklist

Daily:
- [ ] All containers running
- [ ] All domains accessible
- [ ] No critical errors in logs

Weekly:
- [ ] Check disk space
- [ ] Review security logs
- [ ] Test backups

Monthly:
- [ ] Update Docker images
- [ ] Review SSL certificates
- [ ] System updates

---

**System Status**: Ready for production! 🚀

For setup instructions, see [BOOKSTACK_QUICKSTART.md](BOOKSTACK_QUICKSTART.md)
