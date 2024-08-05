FROM dunglas/frankenphp

RUN install-php-extensions \
    bcmath \
    pcntl \
    exif \
    gd \
    zip \
    pdo_pgsql \
    pdo_mysql \
    redis \
    intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ARG USER=www-data

RUN \
	useradd -D ${USER}; \
	setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp; \
	chown -R ${USER}:${USER} /data/caddy && chown -R ${USER}:${USER} /config/caddy

COPY . /app
RUN chown -R ${USER}:${USER} /app
USER ${USER}
WORKDIR /app

RUN composer install
CMD ["frankenphp", "run", "--config", "/app/docker/php/Caddyfile", "--adapter", "caddyfile"]
