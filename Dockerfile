FROM debian:bullseye-slim

# Agregar repositorios de PHP
RUN apt-get update && apt-get install -y \
    lsb-release \
    ca-certificates \
    curl \
    gnupg2 \
    && curl -fsSL https://packages.sury.org/php/README.txt | bash -x \
    && apt-get update && apt-get install -y \
    php-cli \
    php-mbstring \
    php-xml \
    curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar tu código fuente al contenedor
COPY . /var/www/html

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Exponer el puerto 80 (o el que necesites)
EXPOSE 80

# Comando para iniciar el servidor PHP
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html"]