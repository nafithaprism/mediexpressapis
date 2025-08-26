# ---------- Build vendors (no dev) ----------
FROM composer:2 AS vendor
WORKDIR /app

# Cache metadata
COPY composer.json composer.lock* ./
# No scripts here (prevents artisan hooks)
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts

# Now copy the full app
COPY . .

# Optimize autoload WITHOUT running composer scripts
RUN composer dump-autoload -o --classmap-authoritative --no-scripts

# ---------- Runtime: PHP-FPM on Lambda (Bref) ----------
FROM bref/php-81-fpm:2 AS production

# Copy app into Lambda task dir
COPY --from=vendor /app /var/task

# Ensure cache dir exists
RUN mkdir -p /var/task/bootstrap/cache

# Bref FPM entry = Laravel front controller
CMD ["public/index.php"]
