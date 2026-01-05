FROM php:8.2-apache

# Remove all MPM modules to avoid conflict 
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \ && rm -f /etc/apache2/mods-enabled/mpm_event.conf \ && rm -f /etc/apache2/mods-enabled/mpm_worker.load \ && rm -f /etc/apache2/mods-enabled/mpm_worker.conf

# Disable event MPM and enable prefork (fix Apache MPM conflict)
RUN a2dismod mpm_event && a2enmod mpm_prefork

# Enable rewrite
RUN a2enmod rewrite

# Cài extension cần thiết
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy source
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

# Sửa Apache để lắng nghe đúng PORT của Render
RUN sed -i "s/Listen 80/Listen \${PORT}/" /etc/apache2/ports.conf \
    && sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-enabled/000-default.conf

CMD ["apache2-foreground"]
