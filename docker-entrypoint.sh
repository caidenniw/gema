#!/bin/bash
set -e

# Railway injects PORT env (contoh: 8080). Jika tidak ada, default 80
PORT_TO_USE=${PORT:-80}

echo "Starting GEMA AI on port $PORT_TO_USE"

# Ubah Listen di ports.conf dan VirtualHost
if [ "$PORT_TO_USE" != "80" ]; then
    # Ganti Listen 80 -> Listen $PORT
    sed -i "s/Listen 80/Listen ${PORT_TO_USE}/g" /etc/apache2/ports.conf || true
    # Tambahkan jika belum ada
    if ! grep -q "Listen ${PORT_TO_USE}" /etc/apache2/ports.conf; then
        echo "Listen ${PORT_TO_USE}" >> /etc/apache2/ports.conf
    fi
    # Ubah VirtualHost *:80 -> *:PORT
    sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT_TO_USE}>/g" /etc/apache2/sites-available/000-default.conf || true
    sed -i "s/<VirtualHost \*:${PORT_TO_USE}>/<VirtualHost *:${PORT_TO_USE}>/g" /etc/apache2/sites-available/000-default.conf || true
fi

# Pastikan DocumentRoot mengarah ke /var/www/html
# (default sudah benar, tapi set eksplisit untuk aman)
# PHP config: tampilkan error di log saja
echo "PHP Version: $(php -v | head -1)"
echo "DocumentRoot: /var/www/html"

# Start Apache foreground
exec apache2-foreground
