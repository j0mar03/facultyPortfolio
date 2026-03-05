# Cloudflare Full (strict) Setup - Faculty Portfolio

This setup keeps Cloudflare proxy enabled while enforcing end-to-end TLS:
- Browser -> Cloudflare: HTTPS
- Cloudflare -> VPS origin: HTTPS (validated certificate)

## 1) Prepare secure app environment

Edit `.env` on VPS:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://portfolio.itechportfolio.xyz

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=faculty_portfolio
DB_USERNAME=faculty_app
DB_PASSWORD=<strong-random-password>

DB_ROOT_PASSWORD=<strong-random-root-password>
DB_BIND_HOST=127.0.0.1
DB_PORT_FORWARD=3307
```

`DB_BIND_HOST=127.0.0.1` prevents public internet access to MySQL.

## 2) Deploy updated stack

```bash
cd ~/facultyPortfolio
git pull
./deploy.sh
```

`deploy.sh` now also configures host Nginx and runs cert setup automatically when:
- `SETUP_NGINX_FULL_STRICT=1`
- `CERT_MODE=letsencrypt` and `CERTBOT_EMAIL` is set

If you do not want auto Nginx/cert setup:
- set `SETUP_NGINX_FULL_STRICT=0`

## 3) Configure Nginx on VPS host for portfolio domain

```bash
sudo cp scripts/nginx/portfolio-cloudflare-full.conf /etc/nginx/sites-available/portfolio
sudo ln -s /etc/nginx/sites-available/portfolio /etc/nginx/sites-enabled/portfolio
sudo nginx -t
sudo systemctl reload nginx
```

## 4) Install certificate on origin

Choose one option.

### Option A: Let's Encrypt

```bash
sudo apt update
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d portfolio.itechportfolio.xyz
```

### Option B: Cloudflare Origin Certificate

1. Cloudflare Dashboard -> SSL/TLS -> Origin Server -> Create Certificate
2. Save cert/key on VPS:

```bash
sudo mkdir -p /etc/ssl/private /etc/ssl/certs
sudo nano /etc/ssl/certs/cloudflare-origin.crt
sudo nano /etc/ssl/private/cloudflare-origin.key
sudo chmod 600 /etc/ssl/private/cloudflare-origin.key
```

3. In `/etc/nginx/sites-available/portfolio`, switch SSL paths to origin cert paths shown in template comments.
4. Reload Nginx:

```bash
sudo nginx -t && sudo systemctl reload nginx
```

## 5) Switch Cloudflare SSL mode

Cloudflare Dashboard -> SSL/TLS -> Overview:
- Set mode to `Full (strict)`
- Enable `Always Use HTTPS`

## 6) Verify

```bash
curl -I https://portfolio.itechportfolio.xyz
```

If handshake errors occur:
- Check cert file paths in Nginx
- Check `sudo nginx -t`
- Check origin listens on 443: `sudo ss -tlnp | grep :443`
- Check logs: `sudo tail -f /var/log/nginx/error.log`
