FROM php:7.4-apache

# Install necessary PHP extensions and tools
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    wget \
    git \
    imagemagick \
    && docker-php-ext-install pdo pdo_mysql mysqli zip gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Download Omeka zip file from GitHub
RUN wget https://github.com/omeka/Omeka/releases/download/v3.1.2/omeka-3.1.2.zip

# Unzip the downloaded file
RUN unzip omeka-3.1.2.zip

# Move files from extracted directory to current working directory
RUN mv omeka-3.1.2/* . && mv omeka-3.1.2/.htaccess .

# Clean up by removing the zip and extracted directories
RUN rm -rf omeka-3.1.2*

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
