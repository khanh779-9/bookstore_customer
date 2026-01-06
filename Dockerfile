FROM debian:bookworm

# Cài Apache + PHP + Extensions
RUN apt-get update && \
    apt-get install -y apache2 php php-cli php-mysql libapache2-mod-php \
    php-mbstring php-intl php-curl php-zip php-gd php-pdo php-pdo-mysql php-mysqli && \
    apt-get clean

# Bật rewrite
RUN a2enmod rewrite

# FIX lỗi "More than one MPM loaded"
RUN a2dismod mpm_event && a2enmod mpm_prefork

# Xóa trang mặc định
RUN rm -f /var/www/html/index.html

# Copy source
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

# Ưu tiên index.php
RUN sed -i "s/DirectoryIndex .*/DirectoryIndex index.php index.html/" /etc/apache2/mods-enabled/dir.conf


# Sửa Apache để lắng nghe đúng PORT của Railway/Render
RUN sed -i "s/Listen 80/Listen \${PORT}/" /etc/apache2/ports.conf && \
    sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-enabled/000-default.conf

# Expose (không bắt buộc)
EXPOSE 80

# Chạy Apache foreground
CMD ["apachectl", "-D", "FOREGROUND"]
