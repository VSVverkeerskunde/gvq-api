FROM 2dotstwice/nginx-php72-symfony4-dev as builder
ADD . /usr/share/nginx/html/
WORKDIR /usr/share/nginx/html
RUN composer install
RUN rm /usr/share/nginx/html/.env /usr/share/nginx/html/.env.dist

FROM 2dotstwice/nginx-php72-symfony4
COPY --from=builder /usr/share/nginx/html /usr/share/nginx/html/
RUN rm /usr/share/nginx/html/index.html && rm /usr/share/nginx/html/50x.html