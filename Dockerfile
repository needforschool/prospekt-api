FROM bitnami/symfony:5.4

ENV ALLOW_EMPTY_PASSWORD=yes

EXPOSE 8000

COPY . /app