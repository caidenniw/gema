FROM php:8.3-apache

# System deps untuk gd, zip, mysqli
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
    && a2enmod rewrite headers \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files dulu untuk cache layer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts || true

# Copy seluruh project
COPY . .

# Permission
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/vendor \
    && chown -R www-data:www-data /var/www/html/vendor

# Apache: allow .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Entrypoint untuk Railway PORT dinamis
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080

CMD ["/usr/local/bin/docker-entrypoint.sh"]
