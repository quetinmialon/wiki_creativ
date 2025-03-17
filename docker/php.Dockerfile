FROM php:8.2-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    libpq-dev zip unzip git curl \
    && docker-php-ext-install pdo pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer Node.js et npm pour Vite
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Définir le répertoire de travail
WORKDIR /var/www

# Copier le projet Laravel
COPY ./backend /var/www

# Installer les dépendances PHP et JS
RUN composer install
RUN npm install && npm run build  # Build des assets avec Vite

CMD ["php-fpm"]
