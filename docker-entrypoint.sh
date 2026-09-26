#!/bin/bash
set -e

PORT_TO_USE=${PORT:-8080}

echo "Starting GEMA AI on port $PORT_TO_USE"
echo "PHP Version: $(php -v | head -n1)"
echo "DocumentRoot: /var/www/html"

# Railway memberi PORT=8080, jadi kita paksa Apache listen di PORT tersebut
# Tanpa utak-atik MPM, cukup ubah ports.conf dan vhost
if [ "$PORT_TO_USE" != "80" ]; then
    # Ubah Listen 80 jadi Listen PORT_TO_USE
    sed -i "s/Listen 80/Listen ${PORT_TO_USE}/g" /etc/apache2/ports.conf || true
    # Jika belum ada Listen PORT_TO_USE, tambahkan
    if ! grep -q "Listen ${PORT_TO_USE}" /etc/apache2/ports.conf; then
        echo "Listen ${PORT_TO_USE}" >> /etc/apache2/ports.conf
    fi
    # Ubah VirtualHost *:80 -> *:PORT_TO_USE
    sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT_TO_USE}>/g" /etc/apache2/sites-available/000-default.conf || true
fi

cat /etc/apache2/ports.conf
echo "--- vhost ---"
cat /etc/apache2/sites-available/000-default.conf

# Jangan pakai a2enmod/a2dismod di runtime — itu bikin MPM dobel
exec apache2-foreground
