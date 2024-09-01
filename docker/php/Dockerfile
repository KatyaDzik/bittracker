FROM php:8.2-fpm-alpine

ARG UID
ARG GID

RUN apk --update --no-cache add \
  git \
  bash \
  curl \
  icu-dev \
  libpng-dev \
  libjpeg-turbo-dev \
  freetype-dev \
  libzip-dev \
  libwebp-dev

#Install symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash \
    && apk add symfony-cli

# Install PostgreSQL PDO extension dependencies
RUN apk update && apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-configure intl && docker-php-ext-install intl

# Get Composer
RUN apk --no-cache add curl && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN addgroup -g $GID webgroup
RUN adduser -D -u $UID -G webgroup user

# Set access rights to the directories where Composer will be installed
RUN chown -R user:webgroup /usr/local/bin

# Install xdebug
RUN apk add --update linux-headers \
    && apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug-3.3.0 \
    && docker-php-ext-enable xdebug

# Install Redis ext
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS && \
    pecl update-channels && \
    pecl install redis && \
    docker-php-ext-enable redis && \
    apk del $PHPIZE_DEPS

COPY docker/php/xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini

USER user

WORKDIR /var/www/html

EXPOSE 9000