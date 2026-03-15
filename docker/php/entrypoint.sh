#!/usr/bin/env bash
set -e

cd /var/www/app

if [ ! -f .env ] && [ -f .env.docker ]; then
  cp .env.docker .env
fi

if [ -f composer.json ] && [ ! -d vendor ]; then
  composer install --no-interaction --prefer-dist
fi

mkdir -p var/cache var/log var/sessions

chown -R www-data:www-data var || true
chmod -R 775 var || true

php bin/console doctrine:database:create --if-not-exists --no-interaction || true

if [ "${APP_ENV:-dev}" = "dev" ]; then
  php bin/console doctrine:migrations:diff --no-interaction --allow-empty-diff || true
fi

php bin/console doctrine:migrations:migrate --no-interaction || true
php bin/console messenger:setup-transports --no-interaction || true

exec "$@"