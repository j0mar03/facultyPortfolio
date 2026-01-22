# BookStack Setup Guide

This guide explains how to set up BookStack documentation system for your Faculty Portfolio project.

## Overview

- **Portfolio Application**: `portfolio.itechportfolio.xyz` (Port 8081)
- **BookStack Documentation**: `site.itechportfolio.xyz` (Port 8084)

## Architecture

BookStack runs as a Docker container and shares the MySQL database with the main Faculty Portfolio application. The setup includes:

- BookStack container (LinuxServer.io image)
- Nginx reverse proxy with SSL
- Shared MySQL database from the main portfolio
- Automatic database initialization

## Prerequisites

1. **Docker and Docker Compose** installed
2. **Main Faculty Portfolio** running (`docker-compose up -d`)
3. **Nginx** installed on the host
4. **DNS Records** configured:
   - `portfolio.itechportfolio.xyz` → Your server IP
   - `site.itechportfolio.xyz` → Your server IP

## Quick Start

### Automated Setup (Recommended)

Run the automated setup script:

```bash
sudo ./scripts/setup-bookstack.sh
```

This script will:
- Create nginx configurations
- Set up SSL certificates with Let's Encrypt
- Start BookStack containers
- Initialize the database

### Manual Setup

If you prefer to set up manually:

#### 1. Configure DNS

Add these A records to your DNS:
```
portfolio.itechportfolio.xyz  →  YOUR_SERVER_IP
site.itechportfolio.xyz       →  YOUR_SERVER_IP
```

#### 2. Start the Database

Ensure the main database is running:
```bash
docker-compose up -d db
```

#### 3. Configure Nginx

Copy nginx configurations:
```bash
sudo cp scripts/nginx/bookstack.conf /etc/nginx/sites-available/bookstack
sudo ln -s /etc/nginx/sites-available/bookstack /etc/nginx/sites-enabled/
```

Test nginx configuration:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

#### 4. Obtain SSL Certificates

Get SSL certificates for both domains:
```bash
sudo certbot --nginx -d portfolio.itechportfolio.xyz
sudo certbot --nginx -d site.itechportfolio.xyz
```

#### 5. Start BookStack

```bash
docker-compose -f docker-compose.bookstack.yml up -d
```

## Default Credentials

After installation, access BookStack at `https://site.itechportfolio.xyz` with:

- **Email**: `admin@admin.com`
- **Password**: `password`

**⚠️ IMPORTANT**: Change these credentials immediately after first login!

## Environment Variables

You can customize BookStack by creating a `.env` file with these variables:

```bash
# BookStack Configuration
BOOKSTACK_URL=https://site.itechportfolio.xyz
BOOKSTACK_DB_PASSWORD=BookstackDB2024!Secure

# Mail Configuration (Optional)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_FROM=noreply@itechportfolio.xyz
MAIL_FROM_NAME=BookStack
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

## Port Mapping

- **8081**: Faculty Portfolio (Laravel)
- **8082**: Nextcloud
- **8083**: Snipe-IT
- **8084**: BookStack (NEW)

## Database Information

BookStack uses the same MySQL container as the main application:

- **Host**: `facultyportfolio-db`
- **Database**: `bookstack`
- **User**: `bookstack_user`
- **Password**: Set via `BOOKSTACK_DB_PASSWORD` (default: `BookstackDB2024!Secure`)

The database and user are automatically created by the `bookstack-db-setup` service.

## Common Commands

### View Logs
```bash
docker-compose -f docker-compose.bookstack.yml logs -f
```

### Restart BookStack
```bash
docker-compose -f docker-compose.bookstack.yml restart
```

### Stop BookStack
```bash
docker-compose -f docker-compose.bookstack.yml down
```

### Update BookStack
```bash
docker-compose -f docker-compose.bookstack.yml pull
docker-compose -f docker-compose.bookstack.yml up -d
```

### Access BookStack Container
```bash
docker exec -it bookstack /bin/bash
```

## Troubleshooting

### BookStack Can't Connect to Database

Check if the main database is running:
```bash
docker ps | grep facultyportfolio-db
```

If not running, start it:
```bash
docker-compose up -d db
```

### SSL Certificate Issues

Ensure your DNS records are properly configured before running certbot. Check DNS propagation:
```bash
nslookup site.itechportfolio.xyz
nslookup portfolio.itechportfolio.xyz
```

### Port Already in Use

If port 8084 is in use, check what's using it:
```bash
sudo lsof -i :8084
```

### Check BookStack Database Setup

Connect to MySQL and verify:
```bash
docker exec -it facultyportfolio-db mysql -u root -proot
```

Then run:
```sql
SHOW DATABASES;
SELECT user, host FROM mysql.user WHERE user = 'bookstack_user';
```

### View BookStack Logs
```bash
# View all logs
docker-compose -f docker-compose.bookstack.yml logs -f

# View only BookStack container logs
docker logs bookstack -f

# View database setup logs
docker logs bookstack-db-setup
```

## Nginx Configuration Files

### Portfolio (portfolio.itechportfolio.xyz)
- **Config**: `/etc/nginx/sites-available/portfolio`
- **Port**: 8081
- **SSL**: `/etc/letsencrypt/live/portfolio.itechportfolio.xyz/`

### BookStack (site.itechportfolio.xyz)
- **Config**: `/etc/nginx/sites-available/bookstack`
- **Port**: 8084
- **SSL**: `/etc/letsencrypt/live/site.itechportfolio.xyz/`

## Backup and Restore

### Backup BookStack Data

```bash
# Backup BookStack config and data
docker run --rm \
  -v bookstack-config:/source \
  -v $(pwd)/backups:/backup \
  alpine tar czf /backup/bookstack-backup-$(date +%Y%m%d).tar.gz -C /source .

# Backup BookStack database
docker exec facultyportfolio-db mysqldump -u root -proot bookstack > bookstack-db-backup-$(date +%Y%m%d).sql
```

### Restore BookStack Data

```bash
# Restore BookStack config and data
docker run --rm \
  -v bookstack-config:/target \
  -v $(pwd)/backups:/backup \
  alpine tar xzf /backup/bookstack-backup-YYYYMMDD.tar.gz -C /target

# Restore BookStack database
docker exec -i facultyportfolio-db mysql -u root -proot bookstack < bookstack-db-backup-YYYYMMDD.sql
```

## Security Recommendations

1. **Change Default Password**: Immediately after installation
2. **Enable Two-Factor Authentication**: In BookStack settings
3. **Regular Updates**: Keep BookStack image updated
4. **Strong Database Password**: Change `BOOKSTACK_DB_PASSWORD` in production
5. **Firewall Rules**: Only allow ports 80 and 443 from outside
6. **SSL Certificates**: Keep them renewed (certbot handles this automatically)
7. **Regular Backups**: Schedule automated backups

## Integration with Portfolio

BookStack can be integrated with your Faculty Portfolio by:

1. **Embedding Documentation Links**: Add links to BookStack from your portfolio
2. **SSO (Optional)**: Configure LDAP or SAML for single sign-on
3. **API Integration**: Use BookStack API to display documentation in portfolio
4. **Shared Styling**: Customize BookStack theme to match portfolio branding

## Additional Configuration

### Custom Logo

1. Access BookStack container:
   ```bash
   docker exec -it bookstack /bin/bash
   ```

2. Upload logo via BookStack admin interface (Settings > Customization)

### Email Notifications

Configure SMTP in `.env` file and restart:
```bash
docker-compose -f docker-compose.bookstack.yml restart
```

### LDAP/Active Directory

Edit `docker-compose.bookstack.yml` to add LDAP environment variables. See [BookStack LDAP docs](https://www.bookstackapp.com/docs/admin/ldap-auth/).

## Support and Documentation

- **BookStack Official Docs**: https://www.bookstackapp.com/docs/
- **LinuxServer.io Image**: https://docs.linuxserver.io/images/docker-bookstack
- **API Documentation**: https://www.bookstackapp.com/docs/admin/api/

## Service Status

Check all services:
```bash
docker ps -a | grep -E 'bookstack|facultyportfolio'
```

Expected output:
- `bookstack` - Running
- `bookstack-db-setup` - Exited (this is normal, it only runs once)
- `facultyportfolio-app` - Running
- `facultyportfolio-web` - Running
- `facultyportfolio-db` - Running

## Next Steps

1. Access BookStack at `https://site.itechportfolio.xyz`
2. Change default admin password
3. Configure your documentation structure
4. Customize appearance and settings
5. Create your first book/documentation
6. Set up email notifications (optional)
7. Configure user permissions and roles
