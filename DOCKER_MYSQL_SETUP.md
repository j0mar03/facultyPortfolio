# Docker MySQL Setup Guide

## ✅ Perfect! You're Using Docker MySQL

Since Faculty Portfolio uses **Docker MySQL**, we can connect Nextcloud and Snipe-IT directly via **Docker networking** - no need to configure host MySQL!

## 🎯 Benefits

- ✅ **No host MySQL changes** - Your VPS MySQL stays untouched
- ✅ **Better performance** - Docker containers communicate directly
- ✅ **Same MySQL container** - All services use Faculty Portfolio's MySQL
- ✅ **No security concerns** - Everything stays in Docker network
- ✅ **Easy to manage** - One MySQL container for all services

## 🚀 Quick Setup

### Step 1: Create Databases in Docker MySQL

```bash
sudo bash scripts/setup-docker-mysql.sh
```

This script will:
- ✅ Check if Faculty Portfolio MySQL is running
- ✅ Create `nextcloud` database
- ✅ Create `snipeit` database  
- ✅ Create database users
- ✅ Save passwords securely

### Step 2: Set Up Nextcloud

```bash
sudo bash scripts/setup-nextcloud.sh
```

The Nextcloud docker-compose.yml is already configured to use:
- `MYSQL_HOST=facultyportfolio-db` (Docker container name)
- Connects via Docker network (no host.docker.internal needed)

### Step 3: Set Up Snipe-IT

```bash
sudo bash scripts/setup-snipeit.sh
```

Snipe-IT is also configured to use:
- `DB_HOST=facultyportfolio-db` (Docker container name)

## 📊 Architecture

```
┌─────────────────────────────────────────┐
│         Docker Network                  │
│                                         │
│  ┌──────────────────┐                  │
│  │ facultyportfolio │                  │
│  │      -db         │◄─────────────────┼─── MySQL Container
│  │   (MySQL 8.0)    │                  │    (Port 3306)
│  └──────────────────┘                  │
│         ▲                               │
│         │                                │
│    ┌────┴────┐                           │
│    │         │                           │
│  ┌─┴──┐   ┌─┴──┐                        │
│  │Next│   │Snip│                        │
│  │cloud│   │e-IT│                        │
│  └────┘   └────┘                        │
│                                         │
└─────────────────────────────────────────┘
```

All containers communicate via Docker's internal network - fast and secure!

## 🔍 How It Works

1. **Faculty Portfolio MySQL** runs in container `facultyportfolio-db`
2. **Nextcloud** connects to `facultyportfolio-db:3306` via Docker network
3. **Snipe-IT** connects to `facultyportfolio-db:3306` via Docker network
4. All containers are on the same Docker network (`facultyportfolio_default`)

## ✅ Verification

After setup, verify connections:

```bash
# Check all containers are running
docker ps

# Test Nextcloud can connect to MySQL
docker exec nextcloud ping -c 2 facultyportfolio-db

# Test Snipe-IT can connect to MySQL  
docker exec snipeit ping -c 2 facultyportfolio-db

# Check MySQL databases
docker exec facultyportfolio-db mysql -uroot -proot -e "SHOW DATABASES;"
```

## 🛠️ Troubleshooting

### Containers can't connect

Make sure they're on the same network:

```bash
# Check networks
docker network ls

# Check Faculty Portfolio network name
docker inspect facultyportfolio-db | grep NetworkMode

# Connect Nextcloud to Faculty Portfolio network
docker network connect facultyportfolio_default nextcloud

# Connect Snipe-IT to Faculty Portfolio network
docker network connect facultyportfolio_default snipeit
```

### MySQL container not found

```bash
# Check if Faculty Portfolio is running
cd ~/facultyPortfolio
docker compose ps

# Start if not running
docker compose up -d db
```

### Database connection errors

Check MySQL is accessible:

```bash
# Test from Nextcloud container
docker exec nextcloud mysql -h facultyportfolio-db -u nextcloud_user -p

# Check MySQL logs
docker logs facultyportfolio-db
```

## 📝 Configuration Files

The docker-compose files are already configured:

**Nextcloud** (`docker-compose.nextcloud.yml`):
```yaml
environment:
  - MYSQL_HOST=facultyportfolio-db  # Docker container name
  - MYSQL_DATABASE=nextcloud
  - MYSQL_USER=nextcloud_user
networks:
  - facultyportfolio_default  # Same network as Faculty Portfolio
```

**Snipe-IT** (`docker-compose.snipeit.yml`):
```yaml
environment:
  - DB_HOST=facultyportfolio-db  # Docker container name
  - DB_DATABASE=snipeit
  - DB_USERNAME=snipeit_user
networks:
  - facultyportfolio_default  # Same network as Faculty Portfolio
```

## 🎉 Summary

- ✅ **No host MySQL configuration needed**
- ✅ **Uses existing Docker MySQL container**
- ✅ **All services share one MySQL** (saves RAM!)
- ✅ **Secure Docker networking**
- ✅ **Easy to manage**

Your Faculty Portfolio continues working exactly as before - we're just adding more databases to the same MySQL container!
