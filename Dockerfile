# ---- Build vendors (no dev) ----
FROM composer:2 AS vendor
WORKDIR /app

# Cache composer metadata
COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts

# Copy the app and finalize autoload
COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative

# ---- Production Lambda image (PHP-FPM) ----
# Pick a PHP version compatible with your deps; ^8.0 is fine with 8.1.
FROM bref/php-81-fpm:2 AS production

# Copy app into Lambda task dir
COPY --from=vendor /app /var/task

# Ensure cache dir exists; Laravel will use APP_STORAGE=/tmp at runtime
RUN mkdir -p /var/task/bootstrap/cache

# Lambda will start PHP-FPM and call the handler (your public/index.php)
# With Bref FPM, the CMD is the "handler" path (relative to /var/task)
CMD ["public/index.php"]
