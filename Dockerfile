# Étape 1 : Image officielle PHP avec Apache
FROM php:8.2-apache

# Étape 2 : Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Étape 3 : Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Étape 4 : Configuration du dossier Laravel
WORKDIR /var/www

# Étape 5 : Copier le code du projet
COPY . .

# Étape 6 : Donner les droits d’accès
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Étape 7 : Installer les dépendances Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Étape 8 : Nettoyer les caches Laravel (sans figer)
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan storage:link

# Étape 9 : Exposer le port
EXPOSE 8080

# Étape 10 : Lancer Laravel sur le port 8080
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
