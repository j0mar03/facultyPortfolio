# BookStack Documentation System

BookStack is a simple, self-hosted platform for organizing and storing documentation for the Faculty Portfolio project.

## Quick Access

- **URL**: https://site.itechportfolio.xyz
- **Default Login**: `admin@admin.com` / `password` (⚠️ change immediately!)

## Quick Start

### One-Command Setup

```bash
sudo ./scripts/setup-bookstack.sh
```

This will:
1. ✅ Configure Nginx reverse proxy
2. ✅ Set up SSL certificates
3. ✅ Create database and user
4. ✅ Start BookStack container
5. ✅ Configure portfolio.itechportfolio.xyz

### Manual Start/Stop

```bash
# Start BookStack
docker-compose -f docker-compose.bookstack.yml up -d

# Stop BookStack
docker-compose -f docker-compose.bookstack.yml down

# View logs
docker-compose -f docker-compose.bookstack.yml logs -f

# Restart
docker-compose -f docker-compose.bookstack.yml restart
```

## Service Architecture

```
portfolio.itechportfolio.xyz  → Port 8081 (Main Portfolio)
site.itechportfolio.xyz       → Port 8084 (BookStack)
                                     ↓
                            facultyportfolio-db (MySQL)
```

## Configuration Files

- `docker-compose.bookstack.yml` - Docker configuration
- `scripts/nginx/bookstack.conf` - Nginx reverse proxy for BookStack
- `scripts/nginx/portfolio.conf` - Nginx reverse proxy for Portfolio
- `scripts/setup-bookstack.sh` - Automated setup script
- `bookstack.env.example` - Environment variables example

## Environment Variables

Key variables you can customize:

```bash
BOOKSTACK_URL=https://site.itechportfolio.xyz
BOOKSTACK_DB_PASSWORD=BookstackDB2024!Secure
MAIL_HOST=smtp.gmail.com
MAIL_FROM=noreply@itechportfolio.xyz
```

## First-Time Setup Checklist

1. ✅ DNS records configured for both domains
2. ✅ Run setup script: `sudo ./scripts/setup-bookstack.sh`
3. ✅ Access https://site.itechportfolio.xyz
4. ✅ Login with default credentials
5. ✅ Change admin password immediately
6. ✅ Configure settings (Settings > Settings)
7. ✅ Customize appearance (Settings > Customization)
8. ✅ Create your first book

## Common Tasks

### Update BookStack

```bash
docker-compose -f docker-compose.bookstack.yml pull
docker-compose -f docker-compose.bookstack.yml up -d
```

### Backup

```bash
# Backup database
docker exec facultyportfolio-db mysqldump -u root -proot bookstack > bookstack-backup.sql

# Backup config/files
docker run --rm -v bookstack-config:/source -v $(pwd):/backup \
  alpine tar czf /backup/bookstack-files.tar.gz -C /source .
```

### Restore

```bash
# Restore database
docker exec -i facultyportfolio-db mysql -u root -proot bookstack < bookstack-backup.sql

# Restore config/files
docker run --rm -v bookstack-config:/target -v $(pwd):/backup \
  alpine tar xzf /backup/bookstack-files.tar.gz -C /target
```

### Troubleshooting

**Can't connect to database?**
```bash
# Check if main database is running
docker ps | grep facultyportfolio-db

# Start it if needed
docker-compose up -d db
```

**Port 8084 in use?**
```bash
sudo lsof -i :8084
```

**SSL certificate issues?**
```bash
# Check DNS first
nslookup site.itechportfolio.xyz

# Then obtain certificate
sudo certbot --nginx -d site.itechportfolio.xyz
```

## Features

- 📚 **Books & Chapters**: Organize documentation hierarchically
- 🔍 **Full-text Search**: Find content quickly
- 👥 **User Management**: Role-based access control
- 📝 **WYSIWYG Editor**: Easy content creation
- 🎨 **Customizable**: Brand it your way
- 📊 **Activity Tracking**: See who's doing what
- 🔗 **API Access**: Integrate with other tools
- 📱 **Mobile Friendly**: Works on all devices

## Security Best Practices

1. Change default admin password immediately
2. Enable two-factor authentication
3. Use strong database passwords
4. Keep BookStack updated
5. Regular backups
6. Limit user permissions
7. Review audit logs regularly

## Resources

- 📖 [Full Setup Guide](BOOKSTACK_SETUP.md)
- 🌐 [Services Overview](SERVICES_OVERVIEW.md)
- 📚 [Official BookStack Docs](https://www.bookstackapp.com/docs/)
- 🐳 [LinuxServer.io Image](https://docs.linuxserver.io/images/docker-bookstack)

## Support

Issues? Check:
1. Service logs: `docker-compose -f docker-compose.bookstack.yml logs -f`
2. Database status: `docker ps | grep facultyportfolio-db`
3. Nginx logs: `sudo tail -f /var/log/nginx/error.log`
4. Full documentation: [BOOKSTACK_SETUP.md](BOOKSTACK_SETUP.md)

---

**Need help?** Review the [BOOKSTACK_SETUP.md](BOOKSTACK_SETUP.md) for detailed instructions.
