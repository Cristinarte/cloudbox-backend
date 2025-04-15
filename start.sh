#!/bin/bash

# Crear el enlace simbólico si no existe
php artisan storage:link || true

# Cachear configuración
php artisan config:cache

# Iniciar PHP-FPM y Nginx
php-fpm -D
nginx -g "daemon off;"