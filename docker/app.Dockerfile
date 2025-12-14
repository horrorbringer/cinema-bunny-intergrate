FROM php:8.3-fpm

# Install system deps & PHP extensions for Laravel
RUN apt-get update && apt-get install -y \
    git curl unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libfreetype6-dev libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy custom PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Set working directory
WORKDIR /var/www/html

# Create non-root user (if not exists)
RUN groupadd -g 1000 www-data || true && \
    useradd -u 1000 -g www-data -m -s /bin/bash www-data || true

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

