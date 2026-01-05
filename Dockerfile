FROM php:8.2-apache

# Disable event MPM and enable prefork (fix Apache MPM conflict)
RUN a2dismod mpm_event && a2enmod mpm_prefork

# Enable rewrite
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set timezone
RUN echo "date.timezone=Asia/Ho_Chi_Minh" > /usr/local/etc/php/conf.d/timezone.ini

# Apache listen to Railway PORT
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/ports.conf
RUN sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-enabled/000-default.conf

# Copy source
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
