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

## Deployment

```bash
cd portfolio/back
git pull
# vendor/ and var/ must not exist in this folder (QW.3).
# .env must hold every variable of .env.sample: it is not baked into the image.
# It is read by Compose: keep values containing "$" in single quotes.
docker compose -f docker-compose.prod.yaml config --quiet
docker compose -f docker-compose.prod.yaml build --no-cache
docker compose -f docker-compose.prod.yaml up -d

curl -s -o /dev/null -w "%{http_code}\n" "https://back.maengdok.fr/curriculum/first?locale=fr"   # 200
curl -s -o /dev/null -w "%{http_code}\n" "https://back.maengdok.fr/user/"                        # 401
curl -s -o /tmp/cv.pdf "https://back.maengdok.fr/curriculum/pdf/1?locale=fr" && file /tmp/cv.pdf # PDF
docker inspect maengdok_portfolio_back --format '{{.State.Health.Status}}'                       # healthy
```

Bump the `image:` tag in `docker-compose.prod.yaml` for every release, so the
previous image stays on the VPS for a rollback (`1.0.0` = last PHP-FPM image,
`2.0.0` = first FrankenPHP image).
