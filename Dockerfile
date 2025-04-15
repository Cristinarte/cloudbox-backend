# Usar imagen base de PHP 8.2 con FPM sobre Debian Bullseye
FROM php:8.2-fpm-bullseye

# Actualizar paquetes e instalar dependencias del sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libxpm-dev \
    libzip-dev \
    libonig-dev \                 # Necesario para mbstring
    zip \
    unzip \
    curl \
    nginx \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurar e instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        mbstring \
        xml \
        bcmath \
        zip

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php \
    -- --install-dir=/usr/local/bin --filename=composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar el código fuente
COPY . .

# Instalar dependencias de Composer (opcional si no tienes composer.lock)
RUN composer install --optimize-autoloader --no-dev || true

# Dar permisos correctos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copiar configuración de Nginx
COPY ./nginx.conf /etc/nginx/sites-available/default

# Copiar script de inicio
COPY ./start.sh /start.sh
RUN chmod +x /start.sh

# Exponer el puerto
EXPOSE 80

# Comando de inicio
CMD ["/start.sh"]