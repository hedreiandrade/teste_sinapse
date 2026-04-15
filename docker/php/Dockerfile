FROM php:8.3-fpm-alpine

# Instalar dependências do sistema e ferramentas de compilação
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    autoconf \
    g++ \
    make \
    && docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    zip \
    bcmath

# Instalar Redis (compilado)
RUN pecl install redis \
    && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Criar usuário não-root
RUN addgroup -g 1000 -S laravel && \
    adduser -S laravel -u 1000 -G laravel

USER laravel

CMD ["php-fpm"]