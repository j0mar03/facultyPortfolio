# Nextcloud Calendar Setup Guide

This guide will help you set up Nextcloud Calendar on your VPS using the existing MySQL server, optimizing database usage by avoiding additional MySQL containers.

## Prerequisites

- VPS with MySQL server running (~400MB RAM usage)
- Docker and Docker Compose installed
- Faculty Portfolio app already running
- Root or sudo access to configure MySQL

## Overview

This setup will:
- Use your existing MySQL server for Nextcloud (no additional MySQL container)
- Use your existing MySQL server for Snipe-IT (no additional MySQL container)
- Save ~400MB RAM per MySQL container avoided
- Set up Nextcloud with Calendar app enabled

## Step 1: Configure MySQL for Docker Access

Docker containers need to connect to the host MySQL server. Update MySQL configuration:

```bash
# Backup current MySQL config
sudo cp /etc/mysql/mysql.conf.d/mysqld.cnf /etc/mysql/mysql.conf.d/mysqld.cnf.backup

# Edit MySQL configuration
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Find the `bind-address` line and change it:
```ini
# Change from:
bind-address = 127.0.0.1

# To:
bind-address = 0.0.0.0
```

**Security Note:** This allows MySQL to accept connections from any IP. For better security, you can:
1. Use a firewall to restrict access
2. Use MySQL's user host restrictions (already configured in setup script)
3. Consider using Docker's host network mode for better isolation

Restart MySQL:
```bash
sudo systemctl restart mysql
```

Verify MySQL is listening on all interfaces:
```bash
sudo netstat -tlnp | grep mysql
# Should show: 0.0.0.0:3306
```

## Step 2: Create Databases and Users

Run the database setup script:

```bash
cd /home/jomar/dev/projects/facultyPortfolio
bash scripts/setup-nextcloud-db.sh
```

This script will:
- Create `nextcloud` database
- Create `nextcloud_user` with appropriate privileges
- Create `snipeit` database (for future Snipe-IT setup)
- Create `snipeit_user` with appropriate privileges

**Important:** Save the passwords securely!

## Step 3: Set Up Nextcloud

Run the Nextcloud setup script:

```bash
bash scripts/setup-nextcloud.sh
```

This script will:
- Prompt for Nextcloud admin credentials
- Prompt for database passwords
- Create `.env.nextcloud` configuration file
- Start Nextcloud containers
- Configure Nextcloud to use host MySQL

## Step 4: Access Nextcloud

1. **Access Nextcloud:**
   ```
   http://your-vps-ip:8082
   ```

2. **Log in with admin credentials** (set during setup)

3. **Install Calendar App:**
   - Click on your profile icon (top right)
   - Go to "Apps"
   - Search for "Calendar"
   - Click "Enable" on the Calendar app

## Step 5: Configure Reverse Proxy (Optional)

If you want to access Nextcloud via a domain name (e.g., `calendar.yourdomain.com`), set up Nginx reverse proxy:

### Create Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/nextcloud
```

Add this configuration:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name calendar.yourdomain.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name calendar.yourdomain.com;

    # SSL Configuration (use Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/calendar.yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/calendar.yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;

    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;

    # Increase upload size for Nextcloud
    client_max_body_size 512M;

    location / {
        proxy_pass http://127.0.0.1:8082;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $host;
        
        # WebSocket support
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/nextcloud /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

Update Nextcloud trusted domains:
```bash
# Edit .env.nextcloud
nano .env.nextcloud

# Update NEXTCLOUD_TRUSTED_DOMAINS
NEXTCLOUD_TRUSTED_DOMAINS=calendar.yourdomain.com your-vps-ip localhost

# Restart Nextcloud
docker compose -f docker-compose.nextcloud.yml restart nextcloud
```

## Database Optimization Summary

### Before Optimization:
- Faculty Portfolio MySQL container: ~400MB RAM
- Nextcloud MySQL container: ~400MB RAM
- Snipe-IT MySQL container: ~400MB RAM
- **Total: ~1200MB RAM for databases**

### After Optimization:
- Single MySQL server (host): ~400MB RAM
- **Total: ~400MB RAM for databases**
- **Savings: ~800MB RAM**

## Maintenance Commands

### Start/Stop Nextcloud
```bash
# Start
docker compose -f docker-compose.nextcloud.yml up -d

# Stop
docker compose -f docker-compose.nextcloud.yml down

# Restart
docker compose -f docker-compose.nextcloud.yml restart

# View logs
docker compose -f docker-compose.nextcloud.yml logs -f nextcloud
```

### Backup Nextcloud
```bash
# Backup database
mysqldump -u nextcloud_user -p nextcloud > nextcloud_backup_$(date +%Y%m%d).sql

# Backup data directory
docker compose -f docker-compose.nextcloud.yml exec nextcloud tar -czf /tmp/nextcloud-backup.tar.gz /var/www/html/data
docker cp nextcloud:/tmp/nextcloud-backup.tar.gz ./nextcloud-backup.tar.gz
```

### Update Nextcloud
```bash
# Pull latest image
docker compose -f docker-compose.nextcloud.yml pull nextcloud

# Restart with new image
docker compose -f docker-compose.nextcloud.yml up -d nextcloud

# Run upgrade
docker compose -f docker-compose.nextcloud.yml exec nextcloud php occ upgrade
```

## Troubleshooting

### Nextcloud can't connect to MySQL

1. **Check MySQL is listening on all interfaces:**
   ```bash
   sudo netstat -tlnp | grep mysql
   ```

2. **Test connection from Docker container:**
   ```bash
   docker compose -f docker-compose.nextcloud.yml exec nextcloud ping host.docker.internal
   ```

3. **Check MySQL user permissions:**
   ```bash
   mysql -u root -p -e "SELECT User, Host FROM mysql.user WHERE User='nextcloud_user';"
   ```

4. **Check Nextcloud logs:**
   ```bash
   docker compose -f docker-compose.nextcloud.yml logs nextcloud
   ```

### Permission Issues

If you see permission errors:
```bash
# Fix Nextcloud data directory permissions
docker compose -f docker-compose.nextcloud.yml exec nextcloud chown -R www-data:www-data /var/www/html/data
docker compose -f docker-compose.nextcloud.yml exec nextcloud chmod -R 750 /var/www/html/data
```

### Memory Issues

Monitor MySQL memory usage:
```bash
# Check MySQL memory usage
mysql -u root -p -e "SHOW VARIABLES LIKE 'max_connections';"
mysql -u root -p -e "SHOW PROCESSLIST;"
```

Optimize MySQL configuration if needed:
```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Add/update:
```ini
max_connections = 100
innodb_buffer_pool_size = 256M
```

## Security Considerations

1. **Firewall:** Use UFW or iptables to restrict MySQL port (3306) access:
   ```bash
   sudo ufw allow from 172.17.0.0/16 to any port 3306
   ```

2. **Strong Passwords:** Use strong, unique passwords for all database users

3. **Regular Updates:** Keep Nextcloud and MySQL updated:
   ```bash
   docker compose -f docker-compose.nextcloud.yml pull
   sudo apt update && sudo apt upgrade mysql-server
   ```

4. **Backups:** Set up automated backups for both database and files

## Next Steps

1. **Set up Snipe-IT** using the same MySQL server (see SNIPEIT_SETUP.md)
2. **Configure automated backups** for Nextcloud
3. **Set up SSL certificates** for secure access
4. **Configure email** in Nextcloud settings
5. **Set up external storage** if needed

## Support

For issues or questions:
- Check Nextcloud logs: `docker compose -f docker-compose.nextcloud.yml logs`
- Check MySQL logs: `sudo tail -f /var/log/mysql/error.log`
- Nextcloud documentation: https://docs.nextcloud.com/
