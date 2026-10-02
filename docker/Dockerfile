# Development image: same base and extensions as docker/prod.Dockerfile,
# the repository is bind-mounted on /srv/app by docker-compose.yaml.
FROM dunglas/frankenphp:1.12.7-php8.4.26-bookworm

# opcache is built into the base image
RUN install-php-extensions pdo_pgsql intl

RUN apt-get update && apt-get install -y --no-install-recommends \
      git \
      unzip \
      wkhtmltopdf \
      fonts-noto-cjk \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
 && { \
  echo "expose_php=Off"; \
  echo "opcache.enable=1"; \
  echo "opcache.enable_cli=0"; \
  echo "opcache.validate_timestamps=1"; \
  echo "opcache.revalidate_freq=0"; \
} > "$PHP_INI_DIR/conf.d/zz-app.ini"

WORKDIR /srv/app

# Same server config as prod: HTTP on 8080, document root public/,
# uploads served as static files.
COPY docker/Caddyfile /etc/frankenphp/Caddyfile

# Composer runs as www-data, whose home is not writable.
# XDG_CACHE_HOME: writable cache dir for fontconfig (wkhtmltopdf) under www-data
ENV APP_ENV=dev \
    SERVER_NAME=:8080 \
    COMPOSER_HOME=/tmp/composer \
    XDG_CACHE_HOME=/tmp

RUN chown -R www-data:www-data /data /config \
 && setcap -r /usr/local/bin/frankenphp

USER www-data

EXPOSE 8080
