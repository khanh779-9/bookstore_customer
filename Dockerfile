FROM debian:bookworm

# Install Apache + PHP + Extensions
RUN apt-get update && \
    apt-get install -y apache2 php php-cli php-mysql libapache2-mod-php \
    php-mbstring php-intl php-curl php-zip php-gd php-pdo php-pdo-mysql php-mysqli && \
    apt-get clean

RUN a2enmod rewrite

# Xóa trang mặc định của Apache
RUN rm -f /var/www/html/index.html

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD bash -c "echo Listen \$PORT > /etc/apache2/ports.conf && apachectl -D FOREGROUND"

