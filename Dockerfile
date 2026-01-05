FROM debian:bookworm

# Install Apache + PHP + Extensions
RUN apt-get update && \
    apt-get install -y apache2 php php-cli php-mysql libapache2-mod-php \
    php-mbstring php-intl php-curl php-zip php-gd php-pdo php-pdo-mysql php-mysqli && \
    apt-get clean

# Enable rewrite
RUN a2enmod rewrite

# Set timezone
RUN echo "date.timezone=Asia/Ho_Chi_Minh" > /etc/php/8.2/apache2/conf.d/timezone.ini

# Remove default index.html
RUN rm -f /var/www/html/index.html

# Copy source
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

# Bind Apache to Railway PORT
CMD bash -c "echo Listen \$PORT > /etc/apache2/ports.conf && apachectl -D FOREGROUND"
