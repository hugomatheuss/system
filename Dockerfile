FROM php:8.4-fpm

# Instala dependências do sistema e ferramentas de build
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        gnupg \
        curl \
        git \
        unzip \
        zip \
        libicu-dev \
        libonig-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libxml2-dev \
        libxslt-dev \
        libssl-dev \
        pkg-config \
        build-essential \
        cmake \
        autoconf \
        automake \
        libtool \
        libpq-dev \
        librabbitmq-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instala extensões PHP necessárias para Laravel e desenvolvimento
RUN docker-php-ext-install -j"$(nproc)" \
        bcmath \
        intl \
        mbstring \
        pcntl \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        zip \
        opcache \
        gd || true

# Instalar Xdebug e AMQP via PECL e habilitar
RUN pecl channel-update pecl.php.net \
    && pecl install xdebug amqp \
    && docker-php-ext-enable xdebug amqp

# Configuração padrão do Xdebug (ajustável via env vars no docker-compose)
RUN { \
    echo "; Xdebug development defaults"; \
    echo "xdebug.mode=develop,debug"; \
    echo "xdebug.start_with_request=trigger"; \
    echo "xdebug.discover_client_host=1"; \
    echo "xdebug.client_port=9003"; \
    echo "xdebug.log=/tmp/xdebug.log"; \
} > /usr/local/etc/php/conf.d/zz-xdebug.ini

# Instala Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version

# (Opcional) Instala Node.js LTS mínimo para builds frontend — útil em dev
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update \
    && apt-get install -y --no-install-recommends nodejs \
    && node --version && npm --version \
    && rm -rf /var/lib/apt/lists/*

# Criar diretório da aplicação e ajustar permissões
WORKDIR /var/www

# Copiar composer.json primeiro para aproveitar cache de camadas e instalar dependências
COPY composer.json composer.lock* /var/www/
RUN if [ -f composer.json ]; then composer install --no-interaction --no-ansi --no-progress --prefer-dist --no-scripts; fi || true

# Copiar o restante da aplicação
COPY . /var/www

# Ajusta permissões básicas para Laravel (storage / cache)
RUN chown -R www-data:www-data /var/www || true \
    && chmod -R 0755 /var/www || true

# Copiar entrypoint que ajusta permissões em tempo de inicialização (útil com bind mounts)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh || true

EXPOSE 9000

# Usar entrypoint para garantir permissões e depois iniciar php-fpm
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

# Usar php-fpm como comando padrão
CMD ["php-fpm"]
