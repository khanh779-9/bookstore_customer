FROM debian:bookworm

# Remove all MPM modules to avoid conflict 
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \ && rm -f /etc/apache2/mods-enabled/mpm_event.conf \ && rm -f /etc/apache2/mods-enabled/mpm_worker.load \ && rm -f /etc/apache2/mods-enabled/mpm_worker.conf

# Install Apache + PHP
RUN apt-get update && \
    apt-get install -y apache2 php php-cli php-mysql libapache2-mod-php && \
    apt-get clean

# Enable prefork MPM + rewrite
RUN a2dismod mpm_event && a2enmod mpm_prefork && a2enmod rewrite

# Set timezone
RUN echo "date.timezone=Asia/Ho_Chi_Minh" > /etc/php/8.2/apache2/conf.d/timezone.ini

# Apache listen đúng PORT Railway
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/ports.conf && \
    sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-enabled/000-default.conf

# Copy source
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apachectl", "-D", "FOREGROUND"]
