FROM php:8.2-apache

# Cài extension cần thiết
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Bật rewrite
RUN a2enmod rewrite

# Copy source
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

# Sửa Apache để lắng nghe đúng PORT của Render
RUN sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf \
    && sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-enabled/000-default.conf

CMD ["apache2-foreground"]
