# Nextcloud Quick Start Guide

## Quick Setup (5 minutes)

### 1. Create Services Directory Structure

```bash
sudo bash scripts/setup-services-structure.sh
```

This creates organized folders in `/opt/services/` to keep everything separate.

### 2. Configure MySQL for Docker Access

**⚠️ SAFE VERSION - Won't break your existing setup:**

```bash
sudo bash scripts/configure-mysql-for-docker-safe.sh
```

This safe version:
- ✅ Creates a backup before making changes
- ✅ Tests your Faculty Portfolio connection
- ✅ Shows you exactly what will change
- ✅ Can be rolled back if needed
- ✅ Verifies everything still works after changes

**Or if you prefer the original script:**
```bash
sudo bash scripts/configure-mysql-for-docker.sh
```

### 3. Create Databases and Users

```bash
sudo bash scripts/setup-nextcloud-db.sh
```

You'll be prompted for:
- MySQL root password
- Nextcloud database user password
- Snipe-IT database user password (for future use)

### 4. Set Up Nextcloud

```bash
sudo bash scripts/setup-nextcloud.sh
```

You'll be prompted for:
- Nextcloud admin username (default: admin)
- Nextcloud admin password
- Nextcloud subdomain (default: opcr.itechportfolio.xyz)
- Database passwords

Nextcloud will be set up in `/opt/services/nextcloud/` - completely separate from your other projects!

### 5. Configure Nginx Reverse Proxy

```bash
sudo bash scripts/setup-nginx-proxies.sh
```

This sets up:
- Nginx configuration for opcr.itechportfolio.xyz
- SSL certificates with Let's Encrypt
- HTTPS access

### 6. Access Nextcloud

Open your browser and go to:
```
https://opcr.itechportfolio.xyz
```

Log in with the admin credentials you set during setup.

### 7. Enable Calendar App

1. Click your profile icon (top right)
2. Go to "Apps"
3. Search for "Calendar"
4. Click "Enable"

## Common Commands

```bash
# Start Nextcloud
docker compose -f docker-compose.nextcloud.yml up -d

# Stop Nextcloud
docker compose -f docker-compose.nextcloud.yml down

# View logs
docker compose -f docker-compose.nextcloud.yml logs -f

# Restart Nextcloud
docker compose -f docker-compose.nextcloud.yml restart

# Update Nextcloud
docker compose -f docker-compose.nextcloud.yml pull
docker compose -f docker-compose.nextcloud.yml up -d
docker compose -f docker-compose.nextcloud.yml exec nextcloud php occ upgrade
```

## Database Optimization

✅ **Using existing MySQL server** - saves ~800MB RAM compared to separate MySQL containers

- Faculty Portfolio: Uses host MySQL
- Nextcloud: Uses host MySQL  
- Snipe-IT: Ready to use host MySQL

**Total MySQL RAM usage: ~400MB** (instead of ~1200MB with separate containers)

## Troubleshooting

### Can't connect to MySQL?

1. Check MySQL is listening:
   ```bash
   sudo netstat -tlnp | grep mysql
   ```

2. Run MySQL configuration script:
   ```bash
   sudo bash scripts/configure-mysql-for-docker.sh
   ```

### Nextcloud won't start?

Check logs:
```bash
docker compose -f docker-compose.nextcloud.yml logs nextcloud
```

### Permission errors?

```bash
docker compose -f docker-compose.nextcloud.yml exec nextcloud chown -R www-data:www-data /var/www/html/data
```

## Full Documentation

See [NEXTCLOUD_SETUP.md](NEXTCLOUD_SETUP.md) for detailed setup instructions, reverse proxy configuration, and advanced options.
