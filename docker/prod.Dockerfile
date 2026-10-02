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


# FrankenPHP in classic mode (no worker), Debian bookworm: wkhtmltopdf is not
# packaged in trixie. Keep it until the PDF moves to Gotenberg (P9.5).
FROM dunglas/frankenphp:1.12.7-php8.4.26-bookworm AS php

# opcache is built into the base image
RUN install-php-extensions pdo_pgsql intl

RUN apt-get update && apt-get install -y --no-install-recommends \
      wkhtmltopdf \
      fonts-noto-cjk \
    && rm -rf /var/lib/apt/lists/*

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
 && { \
  echo "expose_php=Off"; \
  echo "opcache.enable=1"; \
  echo "opcache.enable_cli=0"; \
  echo "opcache.validate_timestamps=0"; \
  echo "opcache.memory_consumption=256"; \
  echo "opcache.max_accelerated_files=20000"; \
  echo "realpath_cache_size=4096K"; \
  echo "realpath_cache_ttl=600"; \
} > "$PHP_INI_DIR/conf.d/zz-app.ini"

WORKDIR /srv/app

COPY --from=vendor /app /srv/app

# The real .env is excluded by .dockerignore: secrets come from the container
# environment at runtime (env_file in docker-compose.prod.yaml). Symfony still
# needs a .env file to boot; real environment variables take precedence over it.
RUN printf 'APP_ENV=prod\n' > /srv/app/.env

# Server config: HTTP only on 8080 (TLS is done by Traefik, unprivileged port so
# no capability is needed), document root public/, uploads served as static files.
COPY docker/Caddyfile /etc/frankenphp/Caddyfile

# XDG_CACHE_HOME: writable cache dir for fontconfig (wkhtmltopdf) under www-data
ENV APP_ENV=prod \
    APP_DEBUG=0 \
    SERVER_NAME=:8080 \
    XDG_CACHE_HOME=/tmp

# The warmup resolves DATABASE_URL (Doctrine config, no connection: server_version
# is pinned) and DEFAULT_URI (router). Dummy build-only values, scoped to this RUN:
# not real credentials, not persisted in the image; the real ones come at runtime.
RUN export DATABASE_URL="postgresql://build:build@127.0.0.1:5432/build?serverVersion=17&charset=utf8" \
           DEFAULT_URI="http://localhost" \
 && php -d variables_order=EGPCS bin/console cache:clear --no-warmup \
 && php -d variables_order=EGPCS bin/console cache:warmup

# After the cache warmup, so that var/ is fully owned by the runtime user (QW.3).
# public/uploads is created empty and owned by www-data: a fresh named volume
# mounted there inherits this ownership. FrankenPHP writes its state to /data
# and /config (XDG_DATA_HOME / XDG_CONFIG_HOME).
# The binary's cap_net_bind_service is removed: port 8080 does not need it.
RUN mkdir -p /srv/app/var /srv/app/public/uploads \
 && chown -R www-data:www-data /srv/app/var /srv/app/public/uploads /data /config \
 && setcap -r /usr/local/bin/frankenphp

USER www-data

EXPOSE 8080

# Healthy on any non-5xx answer (no /health route until P1.7)
HEALTHCHECK --interval=10s --timeout=5s --start-period=10s --retries=3 \
  CMD code=$(curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8080/) \
      && [ "$code" -ge 200 ] && [ "$code" -lt 500 ]
