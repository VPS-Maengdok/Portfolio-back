# Maengdok - Portfolio back

Symfony API of the portfolio, served by FrankenPHP (classic mode, no worker) on
port 8080 and exposed directly to Traefik on `back.maengdok.fr`.

## Local

```bash
cp .env.sample .env        # then fill in the real local values
docker compose up -d --build
curl "http://localhost:8000/curriculum/first?locale=fr"
```

The repository is bind-mounted on `/srv/app`; the API listens on
`127.0.0.1:8000` only. Networks: `portfolio`, `postgresql` (external).

## Production image

- `docker/prod.Dockerfile`: `dunglas/frankenphp` (PHP 8.4, Debian bookworm for
  wkhtmltopdf), extensions `pdo_pgsql`, `intl`, `opcache`, runs as `www-data`.
- No secret in the image: `.env`, `.env.dev` and `public/uploads` are excluded by
  `.dockerignore`; the image only contains a `.env` with `APP_ENV=prod`.
  Every real value comes from `env_file: .env` in `docker-compose.prod.yaml`.
- Server config in `docker/Caddyfile` (both images): HTTP on 8080, `/uploads/*`
  (writable volume) served as static files only, PHP is never executed there.
- Networks: `proxy` (Traefik), `portfolio`, `postgresql`. No published port.
- Real client IP: `SYMFONY_TRUSTED_PROXIES=private_ranges` and
  `SYMFONY_TRUSTED_HEADERS` are set in the prod compose. Safe because only
  containers on these private Docker networks (Traefik) can reach the back, and
  Traefik overwrites any `X-Forwarded-For` sent by the client.

## Operations

Operations (deploy, rollback) are documented in the private ops handbook.
