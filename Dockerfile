FROM debian:bookworm

# Install Apache + PHP
RUN apt-get update && \
    apt-get install -y apache2 php php-cli php-mysql libapache2-mod-php && \
    apt-get clean

# Enable prefork MPM + rewrite
RUN a2dismod mpm_event && a2enmod mpm_prefork && a2enmod rewrite

# Set timezone
RUN echo "date.timezone=Asia/Ho_Chi_Minh" > /etc/php/8.2/apache2/conf.d/timezone.ini

# Apache listen đúng PORT Railway
RUN sed -i "s/Listen 80/Listen \${PORT}/" /etc/apache2/ports.conf && \
    sed -i "s/:80/:${PORT}/" /etc/apache2/sites-enabled/000-default.conf

# Copy source
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apachectl", "-D", "FOREGROUND"]
