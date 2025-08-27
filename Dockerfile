# ================================
# Stage 1: Composer (build vendor)
# ================================
FROM composer:2 AS vendor
WORKDIR /app

# 1) Install PHP dependencies without dev packages or scripts
#    (Assumes you've already run:
#     composer require bref/bref:^2
#     …and committed composer.json + composer.lock)
COPY composer.json composer.lock* ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts

# 2) Copy the rest of the application code
COPY . .

# 3) Optimize autoload (still no scripts in CI image build)
RUN composer dump-autoload -o --classmap-authoritative --no-scripts


# ===============================================
# Stage 2: Runtime for AWS Lambda (Bref PHP 8.2)
# ===============================================
FROM bref/php-82-fpm:2 AS production

# Enable required PHP extensions for Laravel + MariaDB/RDS
# pdo_mysql is essential for DB connectivity
RUN docker-php-ext-install pdo_mysql

# (Optional) If you use Intervention Image (GD), uncomment the lines below:
# RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev && rm -rf /var/lib/apt/lists/*
# RUN docker-php-ext-configure gd --with-freetype --with-jpeg
# RUN docker-php-ext-install gd

# Copy the built app (with vendor) into Lambda's task directory
COPY --from=vendor /app /var/task

# Ensure Laravel cache dir exists (Lambda FS is read-only except /tmp)
RUN mkdir -p /var/task/bootstrap/cache

# Bref FPM expects your Laravel front controller as the command
CMD ["public/index.php"]
