FROM alpine:3.13

ARG OS_UID
ARG OS_GID

RUN apk add --no-cache --update \
    shadow \
    curl \
    bash \
    composer \
    php7-cli \
    php7-pecl-redis \
    php7-dom \
    php7-tokenizer \
    php7-xml \
    php7-xmlwriter

RUN /usr/sbin/groupadd -g ${OS_GID} package
RUN /usr/sbin/useradd -s /bin/sh -g ${OS_GID} -u ${OS_UID} package

WORKDIR /module

COPY --chown=package ./composer.* ./

RUN composer install -n --no-autoloader --no-scripts --no-progress

COPY --chown=package . .

RUN composer dump-autoload -o
