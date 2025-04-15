# Usar una imagen base de PHP 8.2 con FPM
FROM php:8.2-fpm-bullseye

# Actualizar y instalar las dependencias necesarias
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    curl \
    nginx \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurar e instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_mysql \
    mbstring \
    xml \
    bcmath \
    zip

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

# Copiar archivo de configuración de Nginx
COPY ./nginx.conf /etc/nginx/sites-available/default

# Exponer el puerto 80
EXPOSE 80

# Copiar script de inicio
COPY ./start.sh /start.sh
RUN chmod +x /start.sh

# Comando para iniciar PHP-FPM y Nginx
CMD ["/start.sh"]