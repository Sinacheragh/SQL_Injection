FROM php:8.2-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_pgsql

WORKDIR /app

COPY . /app

EXPOSE 10000

CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
