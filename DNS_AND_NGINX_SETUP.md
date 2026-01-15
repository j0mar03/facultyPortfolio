# DNS and Nginx Setup Guide for Nextcloud

## 🌐 Complete Setup Process

You need to configure **3 things**:
1. **DNS Record** (in Cloudflare or Namecheap)
2. **Nginx Reverse Proxy** (on your VPS)
3. **SSL Certificate** (Let's Encrypt)

## Step 1: Configure DNS Record

### Option A: Cloudflare

1. **Log in to Cloudflare**
   - Go to https://dash.cloudflare.com
   - Select your domain: `itechportfolio.xyz`

2. **Add A Record**
   - Go to **DNS** → **Records**
   - Click **Add record**
   - Configure:
     ```
     Type: A
     Name: opcr
     IPv4 address: [Your VPS IP]
     Proxy status: DNS only (gray cloud) OR Proxied (orange cloud)
     TTL: Auto
     ```
   - Click **Save**

3. **Get Your VPS IP**
   ```bash
   curl ifconfig.me
   # or
   curl ipinfo.io/ip
   ```

### Option B: Namecheap

1. **Log in to Namecheap**
   - Go to https://www.namecheap.com
   - Go to **Domain List** → Click **Manage** on `itechportfolio.xyz`

2. **Add A Record**
   - Go to **Advanced DNS** tab
   - Under **Host Records**, click **Add New Record**
   - Configure:
     ```
     Type: A Record
     Host: opcr
     Value: [Your VPS IP]
     TTL: Automatic (or 3600)
     ```
   - Click **Save** (checkmark icon)

### Verify DNS

Wait 5-10 minutes, then verify:

```bash
# Check DNS resolution
dig opcr.itechportfolio.xyz
# or
nslookup opcr.itechportfolio.xyz

# Online checker
# Visit: https://dnschecker.org/#A/opcr.itechportfolio.xyz
```

## Step 2: Configure Nginx Reverse Proxy

Run the automated script:

```bash
sudo bash scripts/setup-nextcloud-nginx.sh
```

This script will:
- ✅ Check DNS configuration
- ✅ Install Nginx (if needed)
- ✅ Configure reverse proxy
- ✅ Set up SSL certificate (optional)
- ✅ Test everything

### Manual Nginx Setup (if script doesn't work)

```bash
# 1. Install Nginx
sudo apt update
sudo apt install -y nginx

# 2. Copy Nginx config
sudo cp scripts/nginx/nextcloud.conf /etc/nginx/sites-available/nextcloud

# 3. Update domain in config (if needed)
sudo nano /etc/nginx/sites-available/nextcloud

# 4. Enable site
sudo ln -s /etc/nginx/sites-available/nextcloud /etc/nginx/sites-enabled/

# 5. Test configuration
sudo nginx -t

# 6. Reload Nginx
sudo systemctl reload nginx
```

## Step 3: Set Up SSL Certificate

### Automated (Recommended)

The setup script will offer to set up SSL automatically. Or run:

```bash
sudo certbot --nginx -d opcr.itechportfolio.xyz
```

### Manual SSL Setup

```bash
# 1. Install Certbot
sudo apt update
sudo apt install -y certbot python3-certbot-nginx

# 2. Get certificate
sudo certbot --nginx -d opcr.itechportfolio.xyz

# 3. Follow prompts:
#    - Enter email address
#    - Agree to terms
#    - Choose redirect HTTP to HTTPS (recommended)
```

### Auto-Renewal

Certbot sets up auto-renewal automatically. Test it:

```bash
sudo certbot renew --dry-run
```

## 🔍 Troubleshooting

### "We can't connect to the server"

**Check DNS:**
```bash
# Should show your VPS IP
dig opcr.itechportfolio.xyz +short
```

**If DNS is wrong:**
- Wait 10-15 minutes for propagation
- Check DNS settings in Cloudflare/Namecheap
- Clear DNS cache: `sudo systemd-resolve --flush-caches`

**Check Firewall:**
```bash
# Check if ports 80 and 443 are open
sudo ufw status
# If not, allow them:
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

### "502 Bad Gateway"

**Check Nextcloud container:**
```bash
# Is it running?
docker ps | grep nextcloud

# If not, start it:
cd /opt/services/nextcloud
docker compose up -d

# Check logs:
docker logs nextcloud
```

**Check Nginx:**
```bash
# Check error logs
sudo tail -f /var/log/nginx/error.log

# Test Nginx config
sudo nginx -t
```

### SSL Certificate Issues

**If certbot fails:**

1. **Check DNS is pointing correctly:**
   ```bash
   dig opcr.itechportfolio.xyz
   ```

2. **Check ports are open:**
   ```bash
   sudo netstat -tlnp | grep -E ':(80|443)'
   ```

3. **Check firewall:**
   ```bash
   sudo ufw status
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   ```

4. **Try manual verification:**
   ```bash
   sudo certbot certonly --nginx -d opcr.itechportfolio.xyz
   ```

### Cloudflare Proxy Issues

If you're using **Cloudflare Proxy** (orange cloud):

- ✅ **Pros**: DDoS protection, faster CDN
- ⚠️ **Cons**: SSL might need Cloudflare SSL settings

**Option 1: Use Cloudflare SSL**
- Cloudflare Dashboard → SSL/TLS
- Set to **Full** or **Full (strict)**
- Use Cloudflare's SSL certificate

**Option 2: Disable Proxy (DNS only)**
- In DNS record, click the orange cloud to make it gray
- Use Let's Encrypt SSL directly

## ✅ Verification Checklist

After setup, verify:

- [ ] DNS resolves: `dig opcr.itechportfolio.xyz`
- [ ] HTTP works: `curl -I http://opcr.itechportfolio.xyz`
- [ ] HTTPS works: `curl -I https://opcr.itechportfolio.xyz`
- [ ] Nextcloud loads: Visit https://opcr.itechportfolio.xyz
- [ ] SSL certificate valid: Check browser padlock icon
- [ ] Can log in: Use admin credentials

## 📋 Quick Reference

### DNS Providers
- **Cloudflare**: https://dash.cloudflare.com
- **Namecheap**: https://www.namecheap.com

### Useful Commands

```bash
# Check DNS
dig opcr.itechportfolio.xyz
nslookup opcr.itechportfolio.xyz

# Check VPS IP
curl ifconfig.me

# Check Nginx status
sudo systemctl status nginx

# Check Nextcloud container
docker ps | grep nextcloud
docker logs nextcloud

# Check Nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# Test SSL
openssl s_client -connect opcr.itechportfolio.xyz:443 -servername opcr.itechportfolio.xyz
```

## 🎯 Summary

1. **DNS**: Add A record `opcr` → Your VPS IP (in Cloudflare or Namecheap)
2. **Nginx**: Run `sudo bash scripts/setup-nextcloud-nginx.sh`
3. **SSL**: Run `sudo certbot --nginx -d opcr.itechportfolio.xyz`
4. **Access**: Visit https://opcr.itechportfolio.xyz

That's it! 🎉
