# Usar una imagen base de PHP con FPM
FROM php:8.2-fpm

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    curl \
    nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring xml bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar el código fuente
COPY . .

# Instalar dependencias de Composer
RUN composer install --optimize-autoloader --no-dev

# Dar permisos correctos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configurar Nginx
COPY ./nginx.conf /etc/nginx/sites-available/default

# Exponer el puerto 80
EXPOSE 80

# Copiar script de inicio
COPY ./start.sh /start.sh
RUN chmod +x /start.sh

# Comando para iniciar PHP-FPM y Nginx
CMD ["/start.sh"]