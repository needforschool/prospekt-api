# Dockerfile

FROM bitnami/symfony:5.4.18
WORKDIR /app
EXPOSE 8000
COPY . /app

# Setup env
ENV ALLOW_EMPTY_PASSWORD=yes

RUN mkdir -p var && \
    # avoid 'php\r': No such file or directory"
    sed -i 's/\r$//' bin/console && \
    chmod +x bin/console && \
    echo "<?php return [];" > .env.local.php && \
    # Install dependencies
    APP_ENV=prod composer install --prefer-dist --optimize-autoloader --classmap-authoritative --no-interaction --no-ansi --no-dev && \
    APP_ENV=prod bin/console cache:clear --no-warmup && \
    APP_ENV=prod bin/console cache:warmup && \
    mkdir -p var/storage && \
    chown -R www-data:www-data var && \
    # Reduce container size
    rm -rf .git assets /root/.composer /tmp/*