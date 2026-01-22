# BookStack Quick Start Guide

Get BookStack running in 5 minutes! 🚀

## Prerequisites

- ✅ Main Faculty Portfolio running (`docker-compose up -d`)
- ✅ DNS records configured:
  - `portfolio.itechportfolio.xyz` → Your server IP
  - `site.itechportfolio.xyz` → Your server IP
- ✅ Nginx installed on host
- ✅ Port 8084 available

## One-Command Setup

```bash
sudo ./scripts/setup-bookstack.sh
```

That's it! The script will:
1. Configure nginx for both portfolio and BookStack
2. Obtain SSL certificates
3. Create database and user
4. Start BookStack

## Access

- **BookStack**: https://site.itechportfolio.xyz
- **Portfolio**: https://portfolio.itechportfolio.xyz

### Default Login

- Email: `admin@admin.com`
- Password: `password`

**⚠️ CHANGE IMMEDIATELY AFTER LOGIN!**

## Manual Setup (Optional)

If you prefer manual steps:

### 1. Start Database

```bash
docker-compose up -d db
```

### 2. Configure Nginx

```bash
sudo cp scripts/nginx/portfolio.conf /etc/nginx/sites-available/
sudo cp scripts/nginx/bookstack.conf /etc/nginx/sites-available/
sudo ln -s /etc/nginx/sites-available/portfolio /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/bookstack /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

### 3. Get SSL Certificates

```bash
sudo certbot --nginx -d portfolio.itechportfolio.xyz
sudo certbot --nginx -d site.itechportfolio.xyz
```

### 4. Start BookStack

```bash
docker-compose -f docker-compose.bookstack.yml up -d
```

### 5. Wait & Access

Wait ~15 seconds for initialization, then visit:
https://site.itechportfolio.xyz

## Common Commands

```bash
# View logs
docker-compose -f docker-compose.bookstack.yml logs -f

# Restart
docker-compose -f docker-compose.bookstack.yml restart

# Stop
docker-compose -f docker-compose.bookstack.yml down

# Update
docker-compose -f docker-compose.bookstack.yml pull
docker-compose -f docker-compose.bookstack.yml up -d
```

## Troubleshooting

### Port 8084 in use?
```bash
sudo lsof -i :8084
```

### Database not running?
```bash
docker ps | grep facultyportfolio-db
docker-compose up -d db
```

### SSL issues?
```bash
# Verify DNS first
nslookup site.itechportfolio.xyz
nslookup portfolio.itechportfolio.xyz
```

### Can't connect?
Check nginx status:
```bash
sudo systemctl status nginx
sudo tail -f /var/log/nginx/error.log
```

## Next Steps

1. ✅ Change admin password
2. ✅ Go to Settings → Customization
3. ✅ Create your first book
4. ✅ Add users (Settings → Users)
5. ✅ Configure email (optional)

## Documentation

- **Full Guide**: [BOOKSTACK_SETUP.md](BOOKSTACK_SETUP.md)
- **All Services**: [SERVICES_OVERVIEW.md](SERVICES_OVERVIEW.md)
- **Official Docs**: https://www.bookstackapp.com/docs/

## Quick Tips

💡 **Tip 1**: Use "Books" for major topics (e.g., "Faculty Guide", "Student Handbook")

💡 **Tip 2**: Use "Chapters" to organize within books (e.g., "Getting Started", "Advanced Topics")

💡 **Tip 3**: Enable two-factor auth in Settings → Authentication

💡 **Tip 4**: Backup regularly:
```bash
docker exec facultyportfolio-db mysqldump -u root -proot bookstack > backup.sql
```

💡 **Tip 5**: Customize theme to match your portfolio branding

## Need Help?

Check the logs:
```bash
docker-compose -f docker-compose.bookstack.yml logs -f bookstack
```

Still stuck? See [BOOKSTACK_SETUP.md](BOOKSTACK_SETUP.md) for detailed troubleshooting.

---

**Ready to document!** 📚 Visit https://site.itechportfolio.xyz to get started.
