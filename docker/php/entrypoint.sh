#!/usr/bin/env bash
set -e

cd /var/www/app

if [ -f composer.json ] && [ ! -d vendor ]; then
  composer install --no-interaction --prefer-dist
fi

mkdir -p var/cache var/log
chmod -R 777 var || true

php bin/console doctrine:database:create --if-not-exists --no-interaction || true
php bin/console doctrine:migrations:diff --no-interaction --allow-empty-diff || true
php bin/console doctrine:migrations:migrate --no-interaction || true
php bin/console messenger:setup-transports --no-interaction || true

exec "$@"