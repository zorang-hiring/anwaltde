ARG PHP_FPM_VERSION
FROM php:${PHP_FPM_VERSION}-fpm

RUN docker-php-ext-install pdo_mysql

CMD ["php-fpm"]

WORKDIR /var/www/current/src
#RUN echo $(pwdr -1)
#RUN php composer.phar install
#CMD bash -c "cd /var/www/current/src && php composer.phar install"

# It depends where you want your Pipfile to be inside your container.
# It's COPY source_path target_path source path is on your host, relative to the Dockerfile,
# target_path is an absolut path in your container
#COPY ./docker-entrypoint.sh /docker-entrypoint.sh
#RUN chmod +x /docker-entrypoint.sh
#ENTRYPOINT ["/docker-entrypoint.sh"]

EXPOSE 9000
