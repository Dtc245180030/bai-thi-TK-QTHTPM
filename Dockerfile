FROM php:8.2-apache

# Cài đặt extension pdo, pdo_mysql và mysqli cần thiết cho PHP thuần kết nối MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite

# Cấu hình PHP cơ bản (kích thước upload, memory limit)
RUN echo "upload_max_filesize = 32M\npost_max_size = 32M\nmemory_limit = 256M\ndate.timezone = Asia/Ho_Chi_Minh" > /usr/local/etc/php/conf.d/custom.ini

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Phân quyền thư mục web cho Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
