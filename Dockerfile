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


# ===============================================
# Stage 2: Runtime for AWS Lambda (Bref PHP 8.2)
# ===============================================
FROM bref/php-82-fpm:2 AS production

# (No docker-php-ext-install here; Bref images don't include it.
# pdo_mysql is already included in php-82-fpm:2.)

# Copy the built app into Lambda's task dir
COPY --from=vendor /app /var/task

# Ensure Laravel cache dir exists
RUN mkdir -p /var/task/bootstrap/cache

# Bref FPM expects your Laravel front controller
CMD ["public/index.php"]
