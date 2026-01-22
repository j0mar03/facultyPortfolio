# BookStack Setup - Files Created

This document lists all the files created for the BookStack setup.

## 📁 Files Created

### Docker Configuration
- **`docker-compose.bookstack.yml`** - BookStack Docker Compose configuration
  - BookStack service on port 8084
  - Automatic database setup
  - Shared network with main application

### Nginx Configurations
- **`scripts/nginx/bookstack.conf`** - Nginx config for site.itechportfolio.xyz
  - SSL support
  - Reverse proxy to port 8084
  - Security headers
  
- **`scripts/nginx/portfolio.conf`** - Nginx config for portfolio.itechportfolio.xyz
  - SSL support
  - Reverse proxy to port 8081 (main Laravel app)
  - WebSocket support

### Setup Script
- **`scripts/setup-bookstack.sh`** - Automated setup script
  - Checks prerequisites
  - Configures nginx
  - Sets up SSL certificates
  - Starts containers
  - Fully automated!

### Documentation
- **`BOOKSTACK_SETUP.md`** - Complete detailed setup guide
  - Prerequisites
  - Step-by-step instructions
  - Configuration options
  - Troubleshooting
  - Backup/restore procedures
  - Security recommendations

- **`BOOKSTACK_QUICKSTART.md`** - Quick 5-minute setup guide
  - Fast setup instructions
  - Common commands
  - Quick troubleshooting
  - Essential tips

- **`README_BOOKSTACK.md`** - BookStack readme file
  - Overview
  - Quick commands
  - Feature list
  - Support resources

- **`SERVICES_OVERVIEW.md`** - Complete services overview
  - All service URLs
  - Database configuration
  - Network architecture
  - Backup strategies
  - Performance tuning
  - Security checklist

- **`bookstack.env.example`** - Environment variables example
  - All configurable options
  - LDAP/SAML examples
  - Mail configuration
  - Storage options

- **`BOOKSTACK_FILES_CREATED.md`** - This file! 📄

### Updated Files
- **`README_SERVICES.md`** - Updated with BookStack
- **`SERVICES_SETUP_GUIDE.md`** - Updated with BookStack setup steps

## 🎯 Service URLs

After setup, you'll have:

| Service | URL | Port | Purpose |
|---------|-----|------|---------|
| **Faculty Portfolio** | https://portfolio.itechportfolio.xyz | 8081 | Main Laravel application |
| **BookStack** | https://site.itechportfolio.xyz | 8084 | Documentation system |
| **Nextcloud** | https://opcr.itechportfolio.xyz | 8082 | File storage |
| **Snipe-IT** | https://asset.itechportfolio.xyz | 8083 | Asset management |

## 🚀 Quick Start

1. **Configure DNS** (if not already done):
   ```
   portfolio.itechportfolio.xyz → Your server IP
   site.itechportfolio.xyz      → Your server IP
   ```

2. **Run setup script**:
   ```bash
   sudo ./scripts/setup-bookstack.sh
   ```

3. **Access BookStack**:
   - URL: https://site.itechportfolio.xyz
   - Email: `admin@admin.com`
   - Password: `password`
   - **Change password immediately!**

## 📋 File Tree

```
facultyPortfolio/
├── docker-compose.bookstack.yml          # BookStack Docker config
├── bookstack.env.example                 # Environment variables template
│
├── scripts/
│   ├── setup-bookstack.sh                # Automated setup script
│   └── nginx/
│       ├── bookstack.conf                # BookStack nginx config
│       └── portfolio.conf                # Portfolio nginx config
│
└── Documentation/
    ├── BOOKSTACK_SETUP.md                # Complete setup guide
    ├── BOOKSTACK_QUICKSTART.md           # Quick start guide
    ├── README_BOOKSTACK.md               # BookStack README
    ├── SERVICES_OVERVIEW.md              # All services overview
    ├── README_SERVICES.md                # Services organization (updated)
    ├── SERVICES_SETUP_GUIDE.md           # Full setup guide (updated)
    └── BOOKSTACK_FILES_CREATED.md        # This file
```

## 🔧 Configuration Details

### Docker Compose
- **Image**: `lscr.io/linuxserver/bookstack:latest`
- **Port**: 8084 (host) → 80 (container)
- **Database**: Uses shared `facultyportfolio-db` container
  - Database name: `bookstack`
  - User: `bookstack_user`
  - Password: Configurable via `BOOKSTACK_DB_PASSWORD`

### Nginx Proxy
- **Domains**: 
  - site.itechportfolio.xyz → BookStack
  - portfolio.itechportfolio.xyz → Portfolio
- **SSL**: Let's Encrypt certificates
- **Upload limits**: 100M for BookStack, 50M for Portfolio

### Database Auto-Setup
The `bookstack-db-setup` service automatically:
- Creates `bookstack` database
- Creates `bookstack_user` with proper permissions
- Runs once on first startup

## 📚 Documentation Hierarchy

1. **Start here**: `BOOKSTACK_QUICKSTART.md` (5-min setup)
2. **Need details?**: `BOOKSTACK_SETUP.md` (complete guide)
3. **Daily use**: `README_BOOKSTACK.md` (commands & tips)
4. **All services**: `SERVICES_OVERVIEW.md` (full ecosystem)

## 🛠️ Maintenance Commands

```bash
# Start BookStack
docker-compose -f docker-compose.bookstack.yml up -d

# Stop BookStack
docker-compose -f docker-compose.bookstack.yml down

# View logs
docker-compose -f docker-compose.bookstack.yml logs -f

# Update BookStack
docker-compose -f docker-compose.bookstack.yml pull
docker-compose -f docker-compose.bookstack.yml up -d

# Backup database
docker exec facultyportfolio-db mysqldump -u root -proot bookstack > bookstack-backup.sql

# Restore database
docker exec -i facultyportfolio-db mysql -u root -proot bookstack < bookstack-backup.sql
```

## 🔒 Security Checklist

- [ ] Changed default admin password
- [ ] Configured HTTPS with valid SSL certificates
- [ ] Set strong database password (`BOOKSTACK_DB_PASSWORD`)
- [ ] Enabled two-factor authentication
- [ ] Configured firewall (UFW) - only ports 22, 80, 443 open
- [ ] Set up automated backups
- [ ] Reviewed user permissions
- [ ] Enabled audit logging

## 🐛 Troubleshooting

### BookStack won't start?
```bash
# Check database
docker ps | grep facultyportfolio-db

# View logs
docker-compose -f docker-compose.bookstack.yml logs -f
```

### Can't access via domain?
```bash
# Check DNS
nslookup site.itechportfolio.xyz

# Check nginx
sudo nginx -t
sudo systemctl status nginx

# View nginx logs
sudo tail -f /var/log/nginx/error.log
```

### SSL certificate issues?
```bash
# Check certificates
sudo certbot certificates

# Renew manually
sudo certbot --nginx -d site.itechportfolio.xyz
```

## 📖 Additional Resources

- **BookStack Official**: https://www.bookstackapp.com/docs/
- **Docker Image**: https://docs.linuxserver.io/images/docker-bookstack
- **API Docs**: https://www.bookstackapp.com/docs/admin/api/
- **Security**: https://www.bookstackapp.com/docs/admin/security/

## 🎉 What's Next?

1. Follow the [BOOKSTACK_QUICKSTART.md](BOOKSTACK_QUICKSTART.md) to get started
2. Customize BookStack appearance to match your portfolio
3. Create your first documentation book
4. Set up email notifications (optional)
5. Configure user roles and permissions
6. Integrate with your portfolio (add links, widgets, etc.)

## 💡 Integration Ideas

- **Link from Portfolio**: Add "Documentation" menu item linking to BookStack
- **Embed Content**: Use BookStack API to show docs in portfolio
- **Shared Branding**: Customize BookStack colors/logo to match portfolio
- **SSO**: Configure LDAP/SAML for single sign-on (optional)
- **Faculty Resources**: Create books for faculty handbooks, guides, policies

## ✅ Verification

After setup, verify everything works:

```bash
# 1. Check all containers running
docker ps -a | grep -E 'bookstack|facultyportfolio'

# 2. Test portfolio
curl -I https://portfolio.itechportfolio.xyz

# 3. Test BookStack
curl -I https://site.itechportfolio.xyz

# 4. Check SSL
echo | openssl s_client -showcerts -servername site.itechportfolio.xyz -connect localhost:443 2>/dev/null | grep "Verify return code"
```

All should return successful responses!

---

**You're all set!** 🎉 

Run `sudo ./scripts/setup-bookstack.sh` to get started, or check out [BOOKSTACK_QUICKSTART.md](BOOKSTACK_QUICKSTART.md) for detailed instructions.
