# Services Setup Guide - Organized Structure

This guide helps you set up BookStack, Nextcloud, and Snipe-IT on your VPS with a clean, organized folder structure and subdomain access.

## 📁 Directory Structure

All services are organized in `/opt/services/` to keep everything clean and separate:

```
/opt/services/
├── nextcloud/          # Nextcloud files and configuration
│   ├── docker-compose.yml
│   └── .env
├── snipeit/            # Snipe-IT files and configuration
│   ├── docker-compose.yml
│   └── .env
├── bookstack/          # BookStack documentation
│   ├── docker-compose.yml
│   └── .env
└── nginx-configs/      # Nginx configuration templates

# Faculty Portfolio stays in its current location
# (or can be moved to /opt/services/faculty-portfolio/ if desired)
```

## 🌐 Subdomain Configuration

- **Faculty Portfolio**: `portfolio.itechportfolio.xyz` (main application)
- **BookStack**: `site.itechportfolio.xyz` (documentation)
- **Nextcloud**: `opcr.itechportfolio.xyz` (file storage)
- **Snipe-IT**: `asset.itechportfolio.xyz` (asset management)

## 🚀 Complete Setup Process

### Step 1: Create Directory Structure

```bash
sudo bash scripts/setup-services-structure.sh
```

This creates the organized folder structure in `/opt/services/`.

### Step 2: Configure MySQL for Docker

```bash
sudo bash scripts/configure-mysql-for-docker.sh
```

This allows Docker containers to connect to your host MySQL server.

### Step 3: Create Databases

```bash
sudo bash scripts/setup-nextcloud-db.sh
```

This creates:
- `nextcloud` database and user
- `snipeit` database and user

### Step 4: Set Up Nextcloud

```bash
sudo bash scripts/setup-nextcloud.sh
```

This will:
- Create Nextcloud directory in `/opt/services/nextcloud/`
- Copy docker-compose.yml
- Prompt for configuration (admin user, password, etc.)
- Start Nextcloud container
- Configure for `opcr.itechportfolio.xyz`

### Step 5: Set Up Snipe-IT

```bash
sudo bash scripts/setup-snipeit.sh
```

This will:
- Create Snipe-IT directory in `/opt/services/snipeit/`
- Copy docker-compose.yml
- Prompt for configuration
- Start Snipe-IT container
- Configure for `asset.itechportfolio.xyz`

### Step 6: Set Up BookStack

```bash
sudo bash scripts/setup-bookstack.sh
```

This will:
- Configure nginx reverse proxy for both BookStack and Portfolio
- Set up SSL certificates for both domains
- Create BookStack database and user automatically
- Start BookStack container
- Configure for `site.itechportfolio.xyz` and `portfolio.itechportfolio.xyz`

**Note**: BookStack uses a different setup approach and stays in your project directory rather than `/opt/services/`. See [BOOKSTACK_SETUP.md](BOOKSTACK_SETUP.md) for more details.

### Step 7: Configure Nginx Reverse Proxies

```bash
sudo bash scripts/setup-nginx-proxies.sh
```

This will:
- Copy Nginx configurations
- Enable sites
- Optionally set up SSL certificates with Let's Encrypt

### Step 8: Configure DNS

Add these DNS A records pointing to your VPS IP:

```
portfolio.itechportfolio.xyz -> Your VPS IP
site.itechportfolio.xyz      -> Your VPS IP
opcr.itechportfolio.xyz      -> Your VPS IP
asset.itechportfolio.xyz     -> Your VPS IP
```

## 📋 Service Management

### Faculty Portfolio

```bash
# Navigate to project directory
cd /home/jomar/dev/projects/facultyPortfolio

# Start
docker-compose up -d

# Stop
docker-compose down

# View logs
docker-compose logs -f

# Restart
docker-compose restart
```

### BookStack

```bash
# Navigate to project directory
cd /home/jomar/dev/projects/facultyPortfolio

# Start
docker-compose -f docker-compose.bookstack.yml up -d

# Stop
docker-compose -f docker-compose.bookstack.yml down

# View logs
docker-compose -f docker-compose.bookstack.yml logs -f

# Restart
docker-compose -f docker-compose.bookstack.yml restart
```

### Nextcloud

```bash
# Navigate to Nextcloud directory
cd /opt/services/nextcloud

# Start
docker compose up -d

# Stop
docker compose down

# View logs
docker compose logs -f

# Restart
docker compose restart
```

### Snipe-IT

```bash
# Navigate to Snipe-IT directory
cd /opt/services/snipeit

# Start
docker compose up -d

# Stop
docker compose down

# View logs
docker compose logs -f

# Restart
docker compose restart
```

## 🔧 Configuration Files

### Nextcloud Configuration
- Location: `/opt/services/nextcloud/.env`
- Contains: Admin credentials, database settings, domain configuration

### Snipe-IT Configuration
- Location: `/opt/services/snipeit/.env`
- Contains: Database settings, mail configuration, domain configuration

### Nginx Configurations
- Nextcloud: `/etc/nginx/sites-available/nextcloud`
- Snipe-IT: `/etc/nginx/sites-available/snipeit`

## 🔐 SSL Certificates

SSL certificates are automatically configured via Let's Encrypt when you run `setup-nginx-proxies.sh`.

To manually set up SSL:

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get certificates
sudo certbot --nginx -d opcr.itechportfolio.xyz
sudo certbot --nginx -d asset.itechportfolio.xyz
```

## 📊 Database Optimization

All services use the **same MySQL server** on the host:

- ✅ Faculty Portfolio → Host MySQL
- ✅ Nextcloud → Host MySQL
- ✅ Snipe-IT → Host MySQL

**RAM Savings**: ~800MB (avoiding 2 additional MySQL containers)

## 🗂️ Data Storage

- **Nextcloud data**: Docker volume `nextcloud-data`
- **Snipe-IT data**: Docker volume `snipeit-data`
- **MySQL data**: Host filesystem (existing)

To backup data:

```bash
# Backup Nextcloud
cd /opt/services/nextcloud
docker compose exec nextcloud tar -czf /tmp/nextcloud-backup.tar.gz /var/www/html/data

# Backup Snipe-IT
cd /opt/services/snipeit
docker compose exec snipeit tar -czf /tmp/snipeit-backup.tar.gz /var/www/html/storage
```

## 🔍 Troubleshooting

### Service won't start

```bash
# Check logs
cd /opt/services/[service-name]
docker compose logs

# Check if port is in use
sudo netstat -tlnp | grep [port-number]
```

### Can't access via subdomain

1. Check DNS records are pointing to your VPS
2. Verify Nginx configuration:
   ```bash
   sudo nginx -t
   sudo systemctl status nginx
   ```
3. Check SSL certificates:
   ```bash
   sudo certbot certificates
   ```

### Database connection issues

1. Verify MySQL is accessible:
   ```bash
   sudo netstat -tlnp | grep mysql
   ```
2. Check database users:
   ```bash
   mysql -u root -p -e "SELECT User, Host FROM mysql.user;"
   ```

## 📝 Quick Reference

### Service URLs
- Nextcloud: https://opcr.itechportfolio.xyz
- Snipe-IT: https://asset.itechportfolio.xyz
- Faculty Portfolio: https://portfolio.itechportfolio.xyz

### Service Directories
- Nextcloud: `/opt/services/nextcloud/`
- Snipe-IT: `/opt/services/snipeit/`
- Faculty Portfolio: Current location (or `/opt/services/faculty-portfolio/`)

### Ports
- Nextcloud: 8082 (internal) → 443 (external via Nginx)
- Snipe-IT: 8083 (internal) → 443 (external via Nginx)
- Faculty Portfolio: 8081 (internal) → 443 (external via Nginx)

## ✅ Benefits of This Structure

1. **Organized**: Each service in its own directory
2. **Clean**: No mixing of files between services
3. **Easy Management**: Simple to find and manage each service
4. **Scalable**: Easy to add more services
5. **Professional**: Standard Linux service organization

## 🎯 Next Steps

1. Run all setup scripts in order
2. Configure DNS records
3. Access services via subdomains
4. Set up backups
5. Configure email in each service
