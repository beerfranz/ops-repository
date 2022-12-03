#!/usr/bin/env sh

bin/console doctrine:migrations:migrate --no-interaction

exec docker-php-entrypoint "$@"
