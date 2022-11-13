FROM php:8.1.7-apache-bullseye

# PHP_INIT=development|production
ARG PHP_INI=development

RUN apt update && \
    apt install -y \
      # maybe postgresql-dev for alpine
      libpq-dev \
      zlib1g-dev \
      libssl-dev \
      libzip-dev \
      unzip \
      libicu-dev \
      wget \
      # alpine equivalent: 
      # icu-dev \
    && apt clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-configure zip; \
  docker-php-ext-install \
    pdo \
    pdo_pgsql \
    intl \
    zip \
  ; \
  pecl install apcu; \
  pecl install xdebug-3.1.5; \
  pecl clear-cache; \
  docker-php-ext-enable \
    apcu \
    opcache \
    xdebug \
  ;

# Install symfony CLI
RUN wget https://github.com/symfony-cli/symfony-cli/releases/download/v5.4.10/symfony-cli_linux_amd64.tar.gz \
      && tar xzf symfony-cli_linux_amd64.tar.gz \
      && chmod 733 symfony && mv symfony /usr/local/bin/ \
      && rm -rf symfony-cli_linux_amd64.tar.gz

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN cp /usr/local/etc/php/php.ini-${PHP_INI} /usr/local/etc/php/php.ini
