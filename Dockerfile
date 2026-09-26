FROM php:8.3-cli

# Deps untuk ekstensi PHP yang dipakai GEMA (gd, zip, mysqli, pdo_mysql)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd zip mbstring mysqli pdo pdo_mysql bcmath xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts || true

COPY . .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 8080

# Railway inject PORT (misal 8080) — pakai PHP built-in server, tanpa Apache jadi gak ada MPM
CMD ["sh", "-c", "echo \"Starting GEMA AI on port ${PORT:-8080}\"; echo \"PHP Version: $(php -v | head -n1)\"; php -S 0.0.0.0:${PORT:-8080} -t /var/www/html"]
