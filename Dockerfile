# Gunakan image resmi PHP + Apache
FROM php:8.2-apache

# Install dependensi yang dibutuhkan PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install pdo_mysql mysqli gd \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Node.js & npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /var/www/html

# Copy semua file project
COPY . .

# Install Tailwind & PostCSS
RUN npm install -D tailwindcss postcss autoprefixer

# Build CSS menggunakan Tailwind v4
RUN ./node_modules/.bin/tailwindcss -i ./assets/css/tailwind.css -o ./assets/css/style.css

# Set permission writable untuk logs dan storage
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html

# Expose port yang dipakai Railway
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
