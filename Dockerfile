# Use symfonycorp/cli as builder image
FROM symfonycorp/cli as builder

# Use silarhi/php-apache:8.1-symfony as the final image
FROM silarhi/php-apache:8.1-symfony

# Copy the files from the builder image to the final image
COPY --from=builder /app /app
