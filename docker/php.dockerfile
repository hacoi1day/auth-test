FROM php:8-fpm

WORKDIR /var/www/html

# Instal PDO and Extensions for Export Excel
# RUN docker-php-ext-install pdo pdo_mysql
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

COPY --from=composer /usr/bin/composer /usr/bin/composer
# Copy composer.lock and composer.json
COPY /composer.lock /composer.json /var/www/html/

RUN install-php-extensions pdo pdo_mysql mysqli zip gd simplexml intl
# RUN docker-php-ext-configure intl

# Arguments defined in docker-compose.yml
# ARG user
# ARG uid

# Create system user to run Composer and Artisan Commands
# RUN useradd -G www-data,root -u $uid -d /home/$user $user
# RUN mkdir -p /home/$user/.composer && \
#     chown -R $user:$user /home/$user

# USER $user

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

EXPOSE 9000
CMD ["php-fpm"]
