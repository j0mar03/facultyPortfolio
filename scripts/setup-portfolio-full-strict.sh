#!/bin/bash

# Configure host Nginx for Cloudflare Full (strict) on Faculty Portfolio domain.

set -euo pipefail

DOMAIN="${PORTFOLIO_DOMAIN:-portfolio.itechportfolio.xyz}"
CERT_MODE="${CERT_MODE:-letsencrypt}" # letsencrypt | origin | skip
CERTBOT_EMAIL="${CERTBOT_EMAIL:-}"
SRC_CONF="scripts/nginx/portfolio-cloudflare-full.conf"
DEST_CONF="/etc/nginx/sites-available/portfolio"
ENABLED_CONF="/etc/nginx/sites-enabled/portfolio"

if [ "${EUID}" -ne 0 ]; then
    exec sudo -E bash "$0" "$@"
fi

if [ ! -f "$SRC_CONF" ]; then
    echo "❌ Missing source config: $SRC_CONF"
    exit 1
fi

if ! command -v nginx >/dev/null 2>&1; then
    echo "❌ nginx is not installed on this VPS."
    exit 1
fi

mkdir -p /etc/nginx/sites-available /etc/nginx/sites-enabled

if [ -f "$DEST_CONF" ]; then
    cp "$DEST_CONF" "${DEST_CONF}.backup.$(date +%Y%m%d_%H%M%S)"
fi

cp "$SRC_CONF" "$DEST_CONF"
sed -i "s/portfolio\\.itechportfolio\\.xyz/${DOMAIN}/g" "$DEST_CONF"

ln -sfn "$DEST_CONF" "$ENABLED_CONF"

if [ "$CERT_MODE" = "letsencrypt" ]; then
    if [ -z "$CERTBOT_EMAIL" ]; then
        echo "❌ CERTBOT_EMAIL is required when CERT_MODE=letsencrypt"
        exit 1
    fi

    if ! command -v certbot >/dev/null 2>&1; then
        apt-get update
        apt-get install -y certbot python3-certbot-nginx
    fi

    # Obtain or renew certificate and update nginx config when needed.
    certbot --nginx \
        -d "$DOMAIN" \
        --non-interactive \
        --agree-tos \
        --email "$CERTBOT_EMAIL" \
        --redirect \
        --keep-until-expiring
fi

if [ "$CERT_MODE" = "origin" ]; then
    echo "⚠️ CERT_MODE=origin selected."
    echo "   Place your Cloudflare Origin cert/key in Nginx paths, then re-run deploy."
fi

nginx -t
systemctl reload nginx

echo "✅ Host Nginx is configured for domain: $DOMAIN"
echo "✅ Next manual step: Cloudflare Dashboard -> SSL/TLS -> set mode to Full (strict)."
