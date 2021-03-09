FROM php:7.3-fpm

ARG user
ARG uid

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd
RUN docker-php-ext-configure mysqli --with-mysqli=mysqlnd
RUN docker-php-ext-install pdo_mysql mysqli mbstring bcmath
RUN docker-php-ext-enable pdo_mysql mysqli

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u ${uid} -d /home/${user} ${user}
RUN mkdir -p /home/${user}/.composer && \
    chown -R ${user}:${user} /home/${user}

WORKDIR /var/www/html

USER ${user}

EXPOSE 9000
CMD ["php-fpm"]
