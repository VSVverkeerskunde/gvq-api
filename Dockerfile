FROM 2dotstwice/nginx-php72-symfony4-dev as builder
ADD . /usr/share/nginx/html/
WORKDIR /usr/share/nginx/html
RUN composer install
RUN rm /usr/share/nginx/html/.env /usr/share/nginx/html/.env.dist || true

FROM 2dotstwice/nginx-php72-symfony4
COPY --from=builder /usr/share/nginx/html /usr/share/nginx/html/
RUN touch /usr/share/nginx/html/.env && \
    rm /usr/share/nginx/html/index.html && \
    rm /usr/share/nginx/html/50x.html && \
    chown www-data:www-data /usr/share/nginx/html/public/uploads && \
    chown -R www-data:www-data /usr/share/nginx/html/var && \
    chown -R www-data:www-data /var/lib/php/sessions

# Increase Nginx worker processes from 2 to 4.
RUN sed -i -e "s/worker_processes 2/worker_processes 4/g" /etc/nginx/nginx.conf

# Tuned php-fpm configuration
ADD ./docker/files/etc/php/fpm/pool.d/www.conf ${PHP_CONFIG_DIR}/fpm/pool.d/www.conf

# Turn off nginx access log
RUN sed -i -e "s/access_log .*/access_log off;/g" /etc/nginx/nginx.conf