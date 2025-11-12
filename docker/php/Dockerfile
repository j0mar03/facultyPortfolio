FROM php:8.3-fpm

# System deps
RUN apt-get update && apt-get install -y \
    git curl zip unzip rsync libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
ENV COMPOSER_HOME=/tmp/composer-cache
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node (for Vite/asset building if needed)
ARG NODE_VERSION=20.10.0
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html


