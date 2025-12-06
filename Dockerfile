# Base image PHP + Apache
FROM php:8.1-apache

# Install dependencies untuk PHP extensions & Node
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    gnupg \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql gd

# Enable Apache rewrite
RUN a2enmod rewrite
RUN service apache2 restart

# Install Node.js 18 + npm terbaru (RESMI)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Set WORKDIR
WORKDIR /var/www/html

# Copy semua file project
COPY . .

# Install Node dependencies untuk Tailwind
RUN npm install

# Build Tailwind CSS sekali saja (TANPA --watch)
RUN npx tailwindcss -i ./assets/css/tailwind.css -o ./assets/css/style.css

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependency PHP
RUN composer install --no-dev --optimize-autoloader || true

# Set Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
