# Cloudflare Configuration for BookStack & Portfolio

This guide explains how BookStack and Portfolio are configured to work with Cloudflare's Flexible SSL mode.

## 🌐 Cloudflare Setup

### DNS Configuration

1. **Log in to Cloudflare Dashboard**
   - Go to https://dash.cloudflare.com
   - Select domain: `itechportfolio.xyz`

2. **Add/Verify DNS Records**
   - **Portfolio**: `portfolio.itechportfolio.xyz` → Your VPS IP (Proxied ✅)
   - **BookStack**: `site.itechportfolio.xyz` → Your VPS IP (Proxied ✅)

3. **Enable Proxy (Orange Cloud)**
   - Make sure both records have the orange cloud enabled
   - This routes traffic through Cloudflare's network

### SSL/TLS Configuration

1. **Go to SSL/TLS Settings**
   - Cloudflare Dashboard → SSL/TLS → Overview

2. **Set Encryption Mode**
   - Choose: **Flexible** ✅
   - This means:
     - ✅ Client → Cloudflare: HTTPS (encrypted)
     - ✅ Cloudflare → Your Server: HTTP (not encrypted)
     - ✅ Your server only needs to listen on port 80

3. **Why Flexible Mode?**
   - No SSL certificates needed on your server
   - Simpler configuration
   - Cloudflare handles all SSL/TLS termination
   - Perfect for this setup!

## 🔧 Nginx Configuration

The nginx configurations are already set up for Cloudflare:

### Key Features

1. **HTTP Only (Port 80)**
   - No SSL certificates needed
   - No HTTPS server block
   - Simpler configuration

2. **Cloudflare Headers**
   - `CF-Connecting-IP` - Real client IP address
   - `CF-Ray` - Cloudflare request ID
   - `CF-Visitor` - Cloudflare visitor info
   - `X-Forwarded-Proto: https` - Tells app it's HTTPS (even though origin is HTTP)

3. **Proper Proxy Headers**
   - `X-Forwarded-For` - Client IP chain
   - `X-Forwarded-Host` - Original hostname
   - `X-Forwarded-Ssl: on` - Indicates SSL was used

### Configuration Files

- `scripts/nginx/bookstack.conf` - BookStack (site.itechportfolio.xyz)

**Note**: Portfolio configuration is not included - your existing portfolio setup remains unchanged.

## ✅ Verification

### Check DNS

```bash
# Should show Cloudflare IPs, not your server IP
dig portfolio.itechportfolio.xyz
dig site.itechportfolio.xyz
```

### Check Cloudflare Status

1. Go to Cloudflare Dashboard → DNS
2. Verify both records show orange cloud (proxied)
3. Check SSL/TLS → Overview shows "Flexible"

### Test Access

```bash
# Should work via HTTPS (Cloudflare handles it)
curl -I https://portfolio.itechportfolio.xyz
curl -I https://site.itechportfolio.xyz

# Should return 200 or 301/302
```

## 🔒 Security Notes

### Flexible Mode Security

**Pros:**
- ✅ Client → Cloudflare is encrypted (HTTPS)
- ✅ DDoS protection from Cloudflare
- ✅ WAF (Web Application Firewall) available
- ✅ No SSL certificate management needed

**Cons:**
- ⚠️ Cloudflare → Server is HTTP (not encrypted)
- ⚠️ Traffic between Cloudflare and server is visible on your network

**For Better Security (Optional):**
- Use Cloudflare "Full" mode (requires SSL cert on server)
- Or use Cloudflare Origin Certificate (free, easier than Let's Encrypt)

### Recommended Settings

1. **Firewall Rules**
   - Only allow port 80 from Cloudflare IPs (optional)
   - Block direct access to your server IP

2. **Cloudflare Security**
   - Enable "Under Attack" mode if needed
   - Configure WAF rules
   - Enable Bot Fight Mode

3. **Rate Limiting**
   - Configure rate limiting in Cloudflare
   - Protect against brute force attacks

## 🚀 Setup Process

The setup script (`scripts/setup-bookstack.sh`) automatically:

1. ✅ Configures nginx for HTTP only (port 80)
2. ✅ Sets up Cloudflare headers
3. ✅ Skips SSL certificate setup (not needed)
4. ✅ Starts BookStack container

**No manual SSL configuration needed!**

## 🔄 Switching to Full Mode (Optional)

If you want end-to-end encryption (Cloudflare → Server):

1. **Get Cloudflare Origin Certificate**
   - Cloudflare Dashboard → SSL/TLS → Origin Server
   - Create Certificate
   - Download certificate and key

2. **Update Nginx Config**
   - Add HTTPS server block (port 443)
   - Use Cloudflare Origin Certificate
   - Update Cloudflare SSL mode to "Full"

3. **Benefits**
   - End-to-end encryption
   - More secure
   - Still get Cloudflare benefits

## 📊 How It Works

```
User (HTTPS)
    ↓
Cloudflare (HTTPS termination)
    ↓ (HTTP)
Your Server (Nginx on port 80)
    ↓
Docker Containers (BookStack/Portfolio)
```

**Flow:**
1. User connects via HTTPS to Cloudflare
2. Cloudflare terminates SSL
3. Cloudflare forwards HTTP to your server
4. Nginx proxies to Docker containers
5. Apps receive proper headers indicating HTTPS was used

## 🐛 Troubleshooting

### Can't Access Sites

1. **Check DNS**
   ```bash
   dig portfolio.itechportfolio.xyz
   # Should show Cloudflare IPs
   ```

2. **Check Cloudflare Status**
   - Verify orange cloud is enabled
   - Check SSL/TLS mode is "Flexible"

3. **Check Server**
   ```bash
   # Should be listening on port 80
   sudo netstat -tlnp | grep :80
   
   # Check nginx
   sudo systemctl status nginx
   sudo nginx -t
   ```

### Wrong IP Address

If apps show Cloudflare IP instead of real client IP:

1. **Check Headers**
   ```bash
   # In your app, check for CF-Connecting-IP header
   # Nginx should pass it through
   ```

2. **Verify Nginx Config**
   - Make sure `CF-Connecting-IP` header is set
   - Check `X-Real-IP` is also set

### SSL Errors

If you see SSL errors:

1. **Check Cloudflare SSL Mode**
   - Should be "Flexible" for this setup
   - Not "Full" or "Full (strict)"

2. **Check DNS**
   - Make sure domains are proxied (orange cloud)
   - Not DNS-only (grey cloud)

## 📚 Additional Resources

- [Cloudflare SSL Modes](https://developers.cloudflare.com/ssl/origin-configuration/ssl-modes/)
- [Cloudflare Headers](https://developers.cloudflare.com/fundamentals/get-started/reference/http-request-headers/)
- [Cloudflare Origin Certificates](https://developers.cloudflare.com/ssl/origin-configuration/origin-ca/)

## ✅ Checklist

Before running setup:

- [ ] DNS records added in Cloudflare
- [ ] Records set to "Proxied" (orange cloud)
- [ ] SSL/TLS mode set to "Flexible"
- [ ] Server has nginx installed
- [ ] Port 80 is open in firewall
- [ ] Docker and docker-compose installed

After setup:

- [ ] Can access https://portfolio.itechportfolio.xyz
- [ ] Can access https://site.itechportfolio.xyz
- [ ] No SSL certificate errors
- [ ] Apps show correct client IPs
- [ ] Cloudflare analytics showing traffic

---

**You're all set!** Cloudflare handles all the SSL complexity, and your server just needs to serve HTTP on port 80. 🚀
