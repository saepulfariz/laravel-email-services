FROM php:8.2-fpm

# Install system deps
RUN apt-get update && apt-get install -y \
    gnupg2 \
    unixodbc \
    unixodbc-dev \
    libgssapi-krb5-2 \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        mysqli \
        intl \
        mbstring \
        pdo \
        pdo_mysql \
        zip \
        gd \
        opcache \
    && rm -rf /var/lib/apt/lists/*

# ARG untuk timezone
ARG APP_TIMEZONE=Asia/Jakarta
ENV TZ=${APP_TIMEZONE}

# Install tzdata dan set timezone
RUN apt-get update && apt-get install -y tzdata \
    && ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone \
    && dpkg-reconfigure -f noninteractive tzdata \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set PHP timezone
RUN echo "date.timezone=$TZ" > /usr/local/etc/php/conf.d/timezone.ini

# Microsoft ODBC Driver
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc \
    | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl -fsSL https://packages.microsoft.com/config/debian/12/prod.list \
    | tee /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18

# Install PHP SQL Server extensions
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first (biar cache efisien)
COPY composer.json composer.lock ./

# Copy all project files
COPY . .

# Install dependencies (composer install)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permission for storage
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage


RUN mkdir -p storage/uploads \
    && rm -rf public/uploads \
    && ln -s ../storage/uploads public/uploads

CMD ["php-fpm"]