FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock symfony.lock* ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts

COPY . .
RUN composer dump-autoload --classmap-authoritative --no-dev --no-interaction


FROM php:8.4-fpm-bookworm AS php

RUN apt-get update && apt-get install -y --no-install-recommends \
      libpq-dev \
      libonig-dev \
      libxml2-dev \
      wkhtmltopdf \
      fonts-noto-cjk \
      curl \
    && docker-php-ext-install pdo pdo_pgsql opcache \
    && rm -rf /var/lib/apt/lists/*

RUN { \
  echo "opcache.enable=1"; \
  echo "opcache.enable_cli=0"; \
  echo "opcache.validate_timestamps=0"; \
  echo "opcache.memory_consumption=256"; \
  echo "opcache.max_accelerated_files=20000"; \
  echo "realpath_cache_size=4096K"; \
  echo "realpath_cache_ttl=600"; \
} > /usr/local/etc/php/conf.d/zz-opcache.ini

WORKDIR /srv/app

COPY --from=vendor /app /srv/app

RUN mkdir -p /srv/app/var \
	&& chown -R www-data:www-data /srv/app/var

ENV APP_ENV=prod
ENV APP_DEBUG=0

RUN php -d variables_order=EGPCS bin/console cache:clear --no-warmup || true \
 && php -d variables_order=EGPCS bin/console cache:warmup || true

USER www-data

HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
  CMD php -v >/dev/null 2>&1 || exit 1
