# Dockerfile

FROM bitnami/symfony:5.4.18
WORKDIR /app
EXPOSE 8000
COPY . /app

# Setup env
ENV ALLOW_EMPTY_PASSWORD=yes