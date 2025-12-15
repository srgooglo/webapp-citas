FROM php:8.4-apache

# Instalar dependencias del sistema y extensiones requeridas
RUN apt-get update && apt-get install -y \
    libzip-dev unzip libpng-dev libjpeg-dev libfreetype6-dev default-mysql-client \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) pdo_mysql mysqli gd zip opcache \
  && a2enmod rewrite headers \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# (Opcional) copiar composer vendor si quieres construir vendor en la imagen
# COPY --chown=www-data:www-data vendor /var/www/html/vendor

EXPOSE 80

CMD ["apache2-foreground"]
