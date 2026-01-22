# Services Overview - iTech Portfolio

This document provides a quick reference for all services in the Faculty Portfolio ecosystem.

## Service URLs

| Service | Domain | Port | Purpose |
|---------|--------|------|---------|
| **Faculty Portfolio** | portfolio.itechportfolio.xyz | 8081 | Main Laravel application for faculty portfolios |
| **BookStack** | site.itechportfolio.xyz | 8084 | Documentation and knowledge base |
| **Nextcloud** | opcr.itechportfolio.xyz | 8082 | OPCR file management and collaboration |
| **Snipe-IT** | asset.itechportfolio.xyz | 8083 | IT asset management |

## Quick Start Commands

### Start All Services
```bash
# Main portfolio
docker-compose up -d

# BookStack
docker-compose -f docker-compose.bookstack.yml up -d

# Nextcloud
docker-compose -f docker-compose.nextcloud.yml up -d

# Snipe-IT
docker-compose -f docker-compose.snipeit.yml up -d
```

### Stop All Services
```bash
docker-compose down
docker-compose -f docker-compose.bookstack.yml down
docker-compose -f docker-compose.nextcloud.yml down
docker-compose -f docker-compose.snipeit.yml down
```

### View Logs
```bash
# Portfolio
docker-compose logs -f

# BookStack
docker-compose -f docker-compose.bookstack.yml logs -f

# Nextcloud
docker-compose -f docker-compose.nextcloud.yml logs -f

# Snipe-IT
docker-compose -f docker-compose.snipeit.yml logs -f
```

## Database Configuration

All services share the same MySQL database container (`facultyportfolio-db`):

| Service | Database | User | Port |
|---------|----------|------|------|
| Portfolio | `faculty_portfolio` | `faculty` | 3306 (→3307) |
| BookStack | `bookstack` | `bookstack_user` | 3306 |
| Nextcloud | `nextcloud` | `nextcloud_user` | 3306 |
| Snipe-IT | `snipeit` | `snipeit_user` | 3306 |

**MySQL Root Password**: `root` (change in production!)

## Nginx Configuration Files

All nginx configurations are in `scripts/nginx/`:

- `portfolio.conf` - Main portfolio application
- `bookstack.conf` - BookStack documentation
- `nextcloud.conf` - Nextcloud file storage
- `snipeit.conf` - Snipe-IT asset management

## SSL Certificates

SSL certificates are managed by Let's Encrypt:

```bash
# Certificate locations
/etc/letsencrypt/live/portfolio.itechportfolio.xyz/
/etc/letsencrypt/live/site.itechportfolio.xyz/
/etc/letsencrypt/live/opcr.itechportfolio.xyz/
/etc/letsencrypt/live/asset.itechportfolio.xyz/
```

### Renew Certificates
```bash
sudo certbot renew
sudo systemctl reload nginx
```

## Default Admin Credentials

**⚠️ Change these immediately after first login!**

### Portfolio
- Configured during Laravel setup
- Check `.env` file for admin user

### BookStack
- Email: `admin@admin.com`
- Password: `password`

### Nextcloud
- Username: `admin`
- Password: Check `NEXTCLOUD_ADMIN_PASSWORD` in `.env`

### Snipe-IT
- Configured during first-time setup
- Follow on-screen instructions

## Network Architecture

```
Internet (443/80)
       ↓
   Nginx (Host)
       ↓
  ┌────┴────┬────────┬────────┐
  ↓         ↓        ↓        ↓
Portfolio BookStack Nextcloud Snipe-IT
(8081)    (8084)    (8082)   (8083)
  └─────────┴────────┴────────┘
            ↓
   facultyportfolio-db (MySQL)
          (3306)
```

## Setup Scripts

Located in `scripts/` directory:

- `setup-bookstack.sh` - Set up BookStack with nginx and SSL
- `setup-nextcloud.sh` - Set up Nextcloud with nginx and SSL
- `setup-snipeit.sh` - Set up Snipe-IT with nginx and SSL
- `setup-nginx-proxies.sh` - Configure all nginx reverse proxies

## Monitoring and Maintenance

### Check Service Status
```bash
docker ps -a
```

### Check Nginx Status
```bash
sudo systemctl status nginx
```

### Check SSL Certificate Expiry
```bash
sudo certbot certificates
```

### Disk Usage
```bash
docker system df
du -sh /var/lib/docker/volumes/*
```

## Backup Strategy

### Database Backup (All Services)
```bash
# Create backup directory
mkdir -p backups

# Backup all databases
docker exec facultyportfolio-db mysqldump -u root -proot --all-databases > backups/all-databases-$(date +%Y%m%d).sql

# Or individual databases
docker exec facultyportfolio-db mysqldump -u root -proot faculty_portfolio > backups/portfolio-$(date +%Y%m%d).sql
docker exec facultyportfolio-db mysqldump -u root -proot bookstack > backups/bookstack-$(date +%Y%m%d).sql
docker exec facultyportfolio-db mysqldump -u root -proot nextcloud > backups/nextcloud-$(date +%Y%m%d).sql
```

### Volume Backup
```bash
# List all volumes
docker volume ls

# Backup a specific volume
docker run --rm -v bookstack-config:/source -v $(pwd)/backups:/backup alpine tar czf /backup/bookstack-$(date +%Y%m%d).tar.gz -C /source .
```

## Environment Variables

Create a `.env` file in the project root:

```bash
# Portfolio
APP_URL=https://portfolio.itechportfolio.xyz

# BookStack
BOOKSTACK_URL=https://site.itechportfolio.xyz
BOOKSTACK_DB_PASSWORD=your-secure-password

# Nextcloud
NEXTCLOUD_HOST=opcr.itechportfolio.xyz
NEXTCLOUD_ADMIN_PASSWORD=your-secure-password
NEXTCLOUD_DB_PASSWORD=your-secure-password

# Snipe-IT
SNIPEIT_URL=https://asset.itechportfolio.xyz

# Database
MYSQL_ROOT_PASSWORD=your-secure-root-password

# Mail (Optional - for all services)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_FROM=noreply@itechportfolio.xyz
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

## Troubleshooting

### Service Won't Start
```bash
# Check logs
docker-compose -f docker-compose.SERVICE.yml logs

# Check if port is in use
sudo lsof -i :PORT

# Restart service
docker-compose -f docker-compose.SERVICE.yml restart
```

### Database Connection Issues
```bash
# Check if database is running
docker ps | grep facultyportfolio-db

# Connect to database
docker exec -it facultyportfolio-db mysql -u root -proot

# Check databases
SHOW DATABASES;
```

### Nginx Issues
```bash
# Test configuration
sudo nginx -t

# Reload nginx
sudo systemctl reload nginx

# Check nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

### SSL Certificate Issues
```bash
# Test SSL
curl -I https://site.itechportfolio.xyz

# Renew certificates
sudo certbot renew --dry-run
```

## Performance Tuning

### Docker Resource Limits

Add to each service in docker-compose files:

```yaml
deploy:
  resources:
    limits:
      cpus: '2'
      memory: 2G
    reservations:
      memory: 1G
```

### Nginx Optimization

Edit `/etc/nginx/nginx.conf`:

```nginx
worker_processes auto;
worker_connections 2048;

# Gzip compression
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss;
```

## Security Checklist

- [ ] Changed all default passwords
- [ ] Configured firewall (UFW) - only ports 22, 80, 443 open
- [ ] Set up automated backups
- [ ] Enabled automatic SSL renewal
- [ ] Configured fail2ban for brute force protection
- [ ] Regular system updates scheduled
- [ ] Docker volumes encrypted (optional)
- [ ] VPN access configured (optional)
- [ ] Monitoring/alerting set up (optional)

## Documentation Links

- [Portfolio Setup](DEPLOYMENT_GUIDE.md)
- [BookStack Setup](BOOKSTACK_SETUP.md)
- [Nextcloud Setup](NEXTCLOUD_QUICKSTART.md)
- [DNS & Nginx Setup](DNS_AND_NGINX_SETUP.md)
- [Docker MySQL Setup](DOCKER_MYSQL_SETUP.md)

## Support

For issues or questions:
1. Check service logs
2. Review documentation files
3. Check official documentation for each service
4. Review GitHub issues for similar problems

## Version Information

- Docker Compose: v2.x
- Nginx: Latest stable
- MySQL: 8.0
- PHP: 8.2 (Laravel)
- BookStack: Latest (LinuxServer.io)
- Nextcloud: Latest
- Snipe-IT: Latest
