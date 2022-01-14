FROM php:7.4-cli-alpine

ARG OS_UID
ARG OS_GID

RUN apk add --no-cache --update shadow composer
RUN /usr/sbin/groupadd -g ${OS_GID} package
RUN /usr/sbin/useradd -s /bin/sh -g ${OS_GID} -u ${OS_UID} package

WORKDIR /module

COPY --chown=package ./composer.* ./

RUN composer install -n --no-autoloader --no-scripts --no-progress --no-suggest

COPY --chown=package . .

RUN composer dump-autoload -o
