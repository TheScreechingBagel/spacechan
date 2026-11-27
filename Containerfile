# 1. FrankenPHP with two extra Caddy modules:
#    - caddy-dns/desec: Let's Encrypt certificates through the DNS-01 challenge
#      (works on a private IP);
#    - caddy-dynamicdns: keeps the A record pointed at the machine's local IP.
#    No XCADDY_SETCAP: nonroot binds 80/443 through a host sysctl instead.
FROM dunglas/frankenphp:1-builder-php8.5-trixie AS caddy
COPY --from=caddy:builder /usr/bin/xcaddy /usr/bin/xcaddy
RUN CGO_ENABLED=1 \
    XCADDY_GO_BUILD_FLAGS="-ldflags='-w -s' -tags=nobadger,nomysql,nopgx" \
    CGO_CFLAGS=$(php-config --includes) \
    CGO_LDFLAGS="$(php-config --ldflags) $(php-config --libs)" \
    xcaddy build \
        --output /usr/local/bin/frankenphp \
        --with github.com/dunglas/frankenphp=./ \
        --with github.com/dunglas/frankenphp/caddy=./caddy/ \
        --with github.com/caddy-dns/desec \
        --with github.com/mholt/caddy-dynamicdns

# 2. Frontend assets (Vite + Tailwind)
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# 3. PHP extensions, Composer packages and the app, prepared in a full image
FROM dunglas/frankenphp:1-php8.5-trixie AS app
COPY --from=caddy /usr/local/bin/frankenphp /usr/local/bin/frankenphp
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN install-php-extensions pdo_sqlite gd exif intl zip pcntl opcache

# php.ini-production only allows 2 MB uploads, the forms accept images up to 4 MB
RUN echo 'upload_max_filesize = 5M' > /usr/local/etc/php/conf.d/uploads.ini

# Collect the shared libraries FrankenPHP and the extensions need,
# because the final image has no package manager (from the FrankenPHP docs).
RUN <<-EOF
	apt-get update
	apt-get install -y --no-install-recommends libtree
	mkdir -p /tmp/libs
	for target in $(which frankenphp) \
		$(find "$(php -r 'echo ini_get("extension_dir");')" -maxdepth 2 -name "*.so"); do
		libtree -pv "$target" 2>/dev/null | grep -oP '(?:── )\K/\S+(?= \[)' | while IFS= read -r lib; do
			[ -f "$lib" ] && cp -n "$lib" /tmp/libs/
		done
	done
EOF

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

COPY . .
COPY --from=assets /app/public/build public/build
RUN composer dump-autoload --optimize --no-dev \
    && ln -s ../storage/app/public public/storage \
    && mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && touch storage/database.sqlite \
    && chown -R 65532:65532 storage

# 4. Final image: distroless (no shell, no package manager), runs as nonroot
FROM gcr.io/distroless/base-debian13

COPY --from=app /usr/local/bin/frankenphp /usr/local/bin/frankenphp
COPY --from=app /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=app /tmp/libs /usr/lib
COPY --from=app /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=app /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Caddy's config/data dirs and Laravel's storage are the only writable places.
ENV XDG_CONFIG_HOME=/config XDG_DATA_HOME=/data
COPY --from=app --chown=nonroot:nonroot /data /data
COPY --from=app --chown=nonroot:nonroot /config /config

# The app stays root-owned (read-only for nonroot), except storage/ (chowned above).
COPY --from=app /app /app
COPY container/Caddyfile /etc/frankenphp/Caddyfile

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_DATABASE=/app/storage/database.sqlite \
    QUEUE_CONNECTION=sync \
    SESSION_DRIVER=file \
    SESSION_SECURE_COOKIE=true \
    CACHE_STORE=file

USER nonroot
WORKDIR /app
# The container uses host networking (so Caddy sees the machine's real IP),
# so nonroot can only bind ports below 1024 if the host allows it:
# sysctl net.ipv4.ip_unprivileged_port_start=80 (keep it in /etc/sysctl.d/).
EXPOSE 80 443 443/udp

ENTRYPOINT ["/usr/local/bin/frankenphp"]
CMD ["run", "--config", "/etc/frankenphp/Caddyfile"]
