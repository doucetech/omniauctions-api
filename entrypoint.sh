#!/bin/bash

# Run Laravel setup commands
php artisan key:generate
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan storage:link

# Start PHP-FPM
exec "$@"
