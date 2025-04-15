# Usar una imagen base específica de PHP con FPM basada en Debian Bullseye
FROM php:8.2-fpm-bullseye

# Actualizar los índices de paquetes
RUN apt-get update

# Instalar dependencias necesarias para GD y otras extensiones
RUN apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar una versión específica de libzip que sea compatible con PHP
RUN apt-get install -y libzip-dev

# Configurar la extensión GD por separado
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Instalar las extensiones PHP necesarias
RUN docker-php-ext-install -j$(nproc) \
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

# Configurar Nginx
COPY ./nginx.conf /etc/nginx/sites-available/default

# Exponer el puerto 80
EXPOSE 80

# Copiar script de inicio
COPY ./start.sh /start.sh
RUN chmod +x /start.sh

# Comando para iniciar PHP-FPM y Nginx
CMD ["/start.sh"]