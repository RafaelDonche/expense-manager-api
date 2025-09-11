# Usa uma imagem oficial do PHP 8.1 com FPM
FROM php:8.1-fpm

# Instala dependências do sistema e extensões PHP necessárias para o Yii2
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql zip

# Define o diretório de trabalho
WORKDIR /var/www/html

# Instala o Composer (gerenciador de dependências do PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer