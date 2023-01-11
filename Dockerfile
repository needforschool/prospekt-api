FROM bitnami/symfony:5.4.18

#set the working directory
WORKDIR /app

# Expose port 8000
EXPOSE 8000

# Copy the source code
COPY --chown=symfony:symfony . /app

# Copy the .env file
COPY --chown=symfony:symfony .env /app/.env

ARG APP_ENV
ARG APP_DEBUG
ARG DATABASE_URL

ENV APP_ENV=${APP_ENV}
ENV APP_DEBUG=${APP_DEBUG}
ENV DATABASE_URL=${DATABASE_URL}

# Install dependencies
RUN chmod +x bin/console \
    && echo "<?php return [];" > .env.local.php \
    && composer install --no-interaction --optimize-autoloader --classmap-authoritative --no-dev \
    && bin/console doctrine:database:create --if-not-exists \
    && bin/console doctrine:schema:update --force \
    && bin/console cache:clear \
    && chown -R symfony:symfony var

# Run the application
CMD ["symfony", "server:start", "--no-tls", "--port=8000"]
