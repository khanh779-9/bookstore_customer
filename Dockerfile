FROM debian:bookworm

RUN apt-get update && \
    apt-get install -y apache2 php libapache2-mod-php php-mysql && \
    apt-get clean

RUN a2dismod mpm_event && a2enmod mpm_prefork && a2enmod rewrite

RUN echo "date.timezone=Asia/Ho_Chi_Minh" > /etc/php/8.2/apache2/conf.d/timezone.ini

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD bash -c "echo Listen \$PORT > /etc/apache2/ports.conf && apachectl -D FOREGROUND"
