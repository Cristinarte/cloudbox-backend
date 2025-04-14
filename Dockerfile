# Usa una imagen base de PHP
FROM php:8.0-fpm

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    php-cli \
    php-xml \
    php-mbstring \
    curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar los archivos de tu proyecto al contenedor
COPY . /var/www/html

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Instalar las dependencias del proyecto con Composer
RUN composer install --no-dev

# Exponer el puerto que utilizará PHP-FPM (Render lo asignará dinámicamente)
EXPOSE 80

# Comando para iniciar PHP-FPM (Render manejará el puerto automáticamente)
CMD ["php-fpm"]