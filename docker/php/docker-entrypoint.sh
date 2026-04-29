#!/bin/sh
# entrypoint to adjust permissions and start php-fpm
set -e

echo "[entrypoint] Adjusting permissions if possible..."
if [ -d /var/www/html ]; then
  chown -R www-data:www-data /var/www/html/storage 2>/dev/null || true
  chown -R www-data:www-data /var/www/html/bootstrap/cache 2>/dev/null || true
fi

echo "[entrypoint] Executing: $@"
exec "$@"
