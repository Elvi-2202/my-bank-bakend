# Utiliser l'image officielle de PHP avec Apache
FROM php:8.2-apache

# Nettoyer le cache APT et installer les dépendances
RUN rm -rf /var/lib/apt/lists/* && apt-get clean \
    && apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    g++ \
    libxml2-dev \
    zlib1g-dev \
    libzip-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    libssl-dev

# Configurer et installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd intl pdo pdo_mysql xml zip

# Activer le module Apache rewrite
RUN a2enmod rewrite

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier le code source de l'application dans le container
COPY . /var/www/html/

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer les dépendances Symfony via Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Donner les bonnes permissions aux fichiers et dossiers
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/vendor /var/www/html/public

# Exposer le port 80
EXPOSE 80

# Lancer Apache avec l'application Symfony
CMD ["apache2-foreground"]