#!/bin/bash
set -e
PORT_TO_USE=${PORT:-8080}
echo "Starting GEMA AI on port $PORT_TO_USE"
echo "PHP Version: $(php -v | head -n1)"
# Tampilkan MPM aktif untuk debug AH00534
echo "=== MPM enabled ==="; ls -l /etc/apache2/mods-enabled/mpm* || true
# Ubah PORT jika perlu (tanpa utak-atik MPM lagi, karena sudah fix di build)
if [ "$PORT_TO_USE" != "80" ]; then
    sed -i "s/Listen 80/Listen ${PORT_TO_USE}/g" /etc/apache2/ports.conf || true
    if ! grep -q "Listen ${PORT_TO_USE}" /etc/apache2/ports.conf; then
        echo "Listen ${PORT_TO_USE}" >> /etc/apache2/ports.conf
    fi
    sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT_TO_USE}>/g" /etc/apache2/sites-available/000-default.conf || true
fi
cat /etc/apache2/ports.conf
echo "--- vhost ---"
cat /etc/apache2/sites-available/000-default.conf
# Test config sebelum start — kalau ada double MPM akan ketahuan di sini
apache2ctl -t || (echo "apache2ctl -t FAILED"; ls -l /etc/apache2/mods-enabled/; exit 1)
exec apache2-foreground
