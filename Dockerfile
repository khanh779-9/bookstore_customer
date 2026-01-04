FROM php:8.2-apache

# Cài extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Bật rewrite
RUN a2enmod rewrite

# Set timezone
RUN echo "date.timezone=Asia/Ho_Chi_Minh" > /usr/local/etc/php/conf.d/timezone.ini

# Apache listen đúng port Render
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:${PORT}/g' /etc/apache2/sites-enabled/000-default.conf

# Copy source
COPY . /var/www/html/

# Quyền
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
