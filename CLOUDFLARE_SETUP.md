# Cloudflare Setup Guide for Nextcloud

## 🌐 Using Cloudflare Proxy (Orange Cloud)

When using Cloudflare's proxy, **Cloudflare handles HTTPS** between users and Cloudflare. Your server only needs to handle HTTP.

## ✅ Quick Setup

### Step 1: Configure DNS in Cloudflare

1. **Log in to Cloudflare**
   - Go to https://dash.cloudflare.com
   - Select domain: `itechportfolio.xyz`

2. **Add A Record**
   - DNS → Records → Add record
   - Type: `A`
   - Name: `opcr`
   - IPv4: `[Your VPS IP]`
   - **Proxy status: Proxied (orange cloud)** ✅
   - TTL: Auto
   - Save

### Step 2: Configure Cloudflare SSL

1. **Go to SSL/TLS Settings**
   - Cloudflare Dashboard → SSL/TLS
   - Go to **Overview** tab

2. **Set Encryption Mode**
   - Choose: **Full** or **Full (strict)** ✅
   - **Full**: Cloudflare → Your server uses HTTPS (recommended)
   - **Full (strict)**: Requires valid SSL cert on your server (more secure)

3. **For "Full" mode:**
   - Your server can use HTTP (port 80)
   - Cloudflare handles SSL termination
   - No Let's Encrypt needed on server

4. **For "Full (strict)" mode:**
   - You need SSL certificate on your server
   - Can use Cloudflare Origin Certificate (free, easier)
   - Or Let's Encrypt certificate

### Step 3: Set Up Nginx on Your Server

Run the setup script (it will detect Cloudflare):

```bash
sudo bash scripts/setup-nextcloud-nginx.sh
```

When asked "Is your domain using Cloudflare proxy?", answer **yes**.

The script will:
- ✅ Use Cloudflare-specific Nginx config
- ✅ Configure HTTP only (port 80)
- ✅ Set proper Cloudflare headers
- ✅ Skip Let's Encrypt setup

## 🔧 Manual Setup

### Option 1: HTTP Only (Cloudflare "Full" mode)

```bash
# 1. Copy Cloudflare config
sudo cp scripts/nginx/nextcloud-cloudflare.conf /etc/nginx/sites-available/nextcloud

# 2. Update domain
sudo sed -i "s/opcr.itechportfolio.xyz/opcr.itechportfolio.xyz/g" /etc/nginx/sites-available/nextcloud

# 3. Enable site
sudo ln -s /etc/nginx/sites-available/nextcloud /etc/nginx/sites-enabled/

# 4. Test and reload
sudo nginx -t
sudo systemctl reload nginx
```

### Option 2: Cloudflare Origin Certificate (Recommended for "Full strict")

1. **Generate Origin Certificate in Cloudflare:**
   - SSL/TLS → Origin Server → Create Certificate
   - Hostnames: `opcr.itechportfolio.xyz`
   - Validity: 15 years
   - Private key type: RSA (2048)
   - Click **Create**

2. **Download Certificate:**
   - Copy the **Origin Certificate** (first box)
   - Copy the **Private Key** (second box)

3. **Install on Server:**
   ```bash
   # Create directory
   sudo mkdir -p /etc/ssl/cloudflare
   
   # Save certificate
   sudo nano /etc/ssl/cloudflare/origin.crt
   # Paste Origin Certificate, save
   
   # Save private key
   sudo nano /etc/ssl/cloudflare/origin.key
   # Paste Private Key, save
   
   # Set permissions
   sudo chmod 600 /etc/ssl/cloudflare/origin.key
   sudo chmod 644 /etc/ssl/cloudflare/origin.crt
   ```

4. **Update Nginx Config:**
   ```nginx
   server {
       listen 443 ssl http2;
       server_name opcr.itechportfolio.xyz;
       
       ssl_certificate /etc/ssl/cloudflare/origin.crt;
       ssl_certificate_key /etc/ssl/cloudflare/origin.key;
       
       # ... rest of config
   }
   ```

## 📋 Cloudflare SSL Modes Explained

### **Off (Gray Cloud)**
- No SSL between Cloudflare and your server
- Not recommended

### **Flexible**
- HTTPS: User → Cloudflare
- HTTP: Cloudflare → Your Server
- ⚠️ Less secure, not recommended

### **Full**
- HTTPS: User → Cloudflare
- HTTPS: Cloudflare → Your Server (but doesn't verify cert)
- ✅ Recommended, easier setup
- Your server can use HTTP or HTTPS

### **Full (strict)**
- HTTPS: User → Cloudflare
- HTTPS: Cloudflare → Your Server (validates cert)
- ✅ Most secure
- Requires valid SSL cert on your server

## 🔍 Verification

### Check Cloudflare Settings

1. **DNS Record:**
   - Should show orange cloud (proxied)
   - Points to your VPS IP

2. **SSL/TLS Mode:**
   - Should be "Full" or "Full (strict)"

3. **Test Connection:**
   ```bash
   # Should work
   curl -I https://opcr.itechportfolio.xyz
   
   # Check SSL certificate (should show Cloudflare)
   openssl s_client -connect opcr.itechportfolio.xyz:443 -servername opcr.itechportfolio.xyz
   ```

## 🛠️ Troubleshooting

### "502 Bad Gateway" from Cloudflare

**Check:**
1. Is Nextcloud container running?
   ```bash
   docker ps | grep nextcloud
   ```

2. Is Nginx running?
   ```bash
   sudo systemctl status nginx
   ```

3. Can Nginx reach Nextcloud?
   ```bash
   curl http://localhost:8082
   ```

4. Check Nginx logs:
   ```bash
   sudo tail -f /var/log/nginx/error.log
   ```

### SSL Errors

**If using "Full (strict)":**
- Make sure Origin Certificate is installed correctly
- Check certificate path in Nginx config
- Verify certificate permissions

**If using "Full":**
- Your server can use HTTP (port 80)
- No SSL certificate needed
- Cloudflare handles SSL

### Cloudflare Headers Not Working

Make sure Nginx config includes:
```nginx
proxy_set_header CF-Connecting-IP $http_cf_connecting_ip;
proxy_set_header CF-Ray $http_cf_ray;
proxy_set_header X-Forwarded-Proto $scheme;
```

## ✅ Recommended Setup

For most users, use:

1. **Cloudflare Proxy**: Orange cloud ✅
2. **SSL Mode**: Full ✅
3. **Server**: HTTP only (port 80) ✅
4. **Nginx**: Cloudflare config ✅

This is the **easiest and most secure** setup!

## 📝 Summary

- ✅ **DNS**: Add A record with orange cloud (proxied)
- ✅ **SSL Mode**: Set to "Full" in Cloudflare
- ✅ **Nginx**: Use Cloudflare config (HTTP only)
- ✅ **No Let's Encrypt needed** (Cloudflare handles SSL)

That's it! 🎉
