# Services Organization Guide

## 📁 Clean Folder Structure

All services are organized separately to keep your VPS clean and organized:

```
/opt/services/
├── nextcloud/          # Nextcloud (opcr.itechportfolio.xyz)
│   ├── docker-compose.yml
│   └── .env
├── snipeit/            # Snipe-IT (asset.itechportfolio.xyz)
│   ├── docker-compose.yml
│   └── .env
└── bookstack/          # BookStack (site.itechportfolio.xyz)
    ├── docker-compose.yml
    └── .env

# Your Faculty Portfolio project stays where it is
# No mixing of files! Everything is organized.
```

## 🎯 Why This Structure?

✅ **Organized**: Each service has its own directory  
✅ **Clean**: No file mixing between services  
✅ **Professional**: Standard Linux service organization  
✅ **Easy Management**: Simple to find and manage each service  
✅ **Scalable**: Easy to add more services later  

## 🚀 Quick Setup

See [SERVICES_SETUP_GUIDE.md](SERVICES_SETUP_GUIDE.md) for complete setup instructions.

## 📋 Service URLs

- **Faculty Portfolio**: https://portfolio.itechportfolio.xyz
- **BookStack**: https://site.itechportfolio.xyz
- **Nextcloud**: https://opcr.itechportfolio.xyz
- **Snipe-IT**: https://asset.itechportfolio.xyz

## 💾 Database Optimization

All services use the **same MySQL server** (saves ~800MB RAM):

- Faculty Portfolio → Host MySQL ✅
- BookStack → Host MySQL ✅
- Nextcloud → Host MySQL ✅
- Snipe-IT → Host MySQL ✅

## 📚 Documentation

- **Complete Setup**: [SERVICES_SETUP_GUIDE.md](SERVICES_SETUP_GUIDE.md)
- **Services Overview**: [SERVICES_OVERVIEW.md](SERVICES_OVERVIEW.md)
- **BookStack Quick Start**: [README_BOOKSTACK.md](README_BOOKSTACK.md)
- **BookStack Full Guide**: [BOOKSTACK_SETUP.md](BOOKSTACK_SETUP.md)
- **Nextcloud Quick Start**: [NEXTCLOUD_QUICKSTART.md](NEXTCLOUD_QUICKSTART.md)
- **Nextcloud Full Guide**: [NEXTCLOUD_SETUP.md](NEXTCLOUD_SETUP.md)
