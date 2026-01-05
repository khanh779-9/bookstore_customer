FROM debian:bookworm

# Install Apache + PHP
RUN apt-get update && \
    apt-get install -y apache2 php php-cli php-mysql libapache2-mod-php && \
    apt-get clean

# Enable rewrite
RUN a2enmod rewrite

# Set timezone
RUN echo "date.timezone=Asia/Ho_Chi_Minh" > /etc/php/8.2/apache2/conf.d/timezone.ini

# Copy source
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

RUN rm -f /var/www/html/index.html

# Railway PORT binding
CMD bash -c "echo Listen \$PORT > /etc/apache2/ports.conf && apachectl -D FOREGROUND"
