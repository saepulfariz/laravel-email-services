#!/bin/sh

php artisan config:clear
php artisan cache:clear
php artisan l5-swagger:generate

exec php-fpm