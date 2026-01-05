FROM php:8.2-apache

# Fix MPM conflict
RUN a2dismod mpm_event && a2dismod mpm_worker && a2enmod mpm_prefork

# Enable rewrite
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set timezone
RUN echo "date.timezone=Asia/Ho_Chi_Minh" > /usr/local/etc/php/conf.d/timezone.ini

# Remove default Apache index.html
RUN rm -f /var/www/html/index.html

# Copy source
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

# Apache listen đúng PORT Railway
CMD bash -c "echo Listen \$PORT > /etc/apache2/ports.conf && apache2-foreground"
