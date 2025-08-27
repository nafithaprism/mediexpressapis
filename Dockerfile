# ================================
# Stage 1: Composer (build vendor)
# ================================
FROM composer:2 AS vendor
WORKDIR /app

# Install PHP dependencies (no dev, no scripts)
COPY composer.json composer.lock* ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts

# Copy the rest of the app
COPY . .
RUN composer dump-autoload -o --classmap-authoritative --no-scripts


# ================================================
# Stage 2: Build Laravel caches & manifests (CLI)
# ================================================
# We'll use the same PHP runtime image; it includes PHP CLI.
FROM bref/php-82-fpm:2 AS cache
WORKDIR /var/task

# Bring the built app into this stage
COPY --from=vendor /app /var/task

# Ensure cache dir exists (we'll pre-generate files so Laravel won't write at runtime)
RUN mkdir -p bootstrap/cache

# Run package discovery to generate bootstrap/cache/packages.php/services.php.
# If artisan fails (e.g., missing env), we still create empty, valid cache files.
# NOTE: We force production-like mode to avoid Whoops.
ENV APP_ENV=production
ENV APP_DEBUG=false

# Try to build package manifest; fall back to empty files if artisan isn't runnable
RUN php artisan package:discover --ansi || true \
 && test -f bootstrap/cache/packages.php || php -r 'file_put_contents("bootstrap/cache/packages.php","<?php return [];");' \
 && test -f bootstrap/cache/services.php || php -r 'file_put_contents("bootstrap/cache/services.php","<?php return [];");'

# (Optional) If your app allows it without DB, you can also pre-cache config/routes/views:
# RUN php artisan config:cache || true
# RUN php artisan route:cache  || true
# RUN php artisan view:cache   || true


# ===============================================
# Stage 3: Runtime for AWS Lambda (Bref PHP 8.2)
# ===============================================
FROM bref/php-82-fpm:2 AS production
WORKDIR /var/task

# Copy the fully prepared app (with vendor + cache files)
COPY --from=cache /var/task /var/task

# Final safety: the folder exists (it will be read-only at runtime, that's OK)
RUN mkdir -p bootstrap/cache

# Bref FPM expects your Laravel front controller
CMD ["public/index.php"]
