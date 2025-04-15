# Usar una imagen base específica de PHP con FPM basada en Debian Bullseye
FROM php:8.2-fpm-bullseye

# Actualizar los índices de paquetes e instalar dependencias
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libxpm-dev \
    zip \
    unzip \
    curl \
    nginx \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurar la extensión GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp

# Instalar extensiones PHP
RUN docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mbstring xml bcmath

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar el código fuente
COPY . .

# Instalar dependencias PHP
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