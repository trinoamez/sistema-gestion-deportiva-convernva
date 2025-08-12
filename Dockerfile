# ===== DOCKERFILE PARA SISTEMA DE GESTIÓN DEPORTIVA CONVERNVA =====

# Usar imagen base oficial de PHP 8.2 con Apache
FROM php:8.2-apache

# Información del proyecto
LABEL maintainer="Equipo Convernva <desarrollo@convernva.com>"
LABEL version="1.2.0"
LABEL description="Sistema de Gestión Deportiva Convernva - Aplicación web PHP con Apache"

# Variables de entorno
ENV APACHE_DOCUMENT_ROOT=/var/www/html
ENV PHP_MEMORY_LIMIT=512M
ENV PHP_MAX_EXECUTION_TIME=300
ENV PHP_UPLOAD_MAX_FILESIZE=50M
ENV PHP_POST_MAX_SIZE=50M

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    libssl-dev \
    unzip \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        mysqli \
        zip \
        intl \
        xml \
        mbstring \
        opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite \
    && a2enmod headers \
    && a2enmod ssl

# Crear directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de configuración
COPY docker/php/php.ini /usr/local/etc/php/
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copiar código fuente del proyecto
COPY . /var/www/html/

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/uploads \
    && chmod -R 777 /var/www/html/logs

# Crear directorios necesarios
RUN mkdir -p /var/www/html/logs \
    && mkdir -p /var/www/html/uploads \
    && mkdir -p /var/www/html/cache \
    && mkdir -p /var/www/html/temp

# Configurar PHP OPcache para producción
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Configurar variables de entorno de PHP
RUN echo "memory_limit = ${PHP_MEMORY_LIMIT}" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_execution_time = ${PHP_MAX_EXECUTION_TIME}" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE}" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size = ${PHP_POST_MAX_SIZE}" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "date.timezone = America/Caracas" >> /usr/local/etc/php/conf.d/custom.ini

# Configurar Apache para mejor rendimiento
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf \
    && echo "ServerSignature Off" >> /etc/apache2/apache2.conf \
    && echo "TraceEnable Off" >> /etc/apache2/apache2.conf

# Exponer puertos
EXPOSE 80 443

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Script de inicio personalizado
COPY docker/scripts/start.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/start.sh

# Comando por defecto
CMD ["/usr/local/bin/start.sh"]


