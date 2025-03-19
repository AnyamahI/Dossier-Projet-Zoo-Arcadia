# Utilisation de l'image officielle PHP avec Apache
FROM php:8.2-apache

# Définir le répertoire de travail dans le conteneur
WORKDIR /var/www/arcadia

# Installation des extensions nécessaires pour PHP
RUN apt-get update && apt-get install -y libzip-dev unzip \
    && docker-php-ext-install pdo pdo_mysql mysqli zip \
    && pecl install redis && docker-php-ext-enable redis

# Activation des modules Apache
RUN a2enmod rewrite

# Copier l'application dans le conteneur
COPY . /var/www/arcadia

# Installer les dépendances PHP avec Composer
COPY composer.json composer.lock /var/www/arcadia/
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# Gérer le port dynamique utilisé par Heroku
RUN echo "Listen 0.0.0.0:\${PORT}" > /etc/apache2/ports.conf

# Exposer le port utilisé (80 par défaut, remplacé dynamiquement par Heroku)
EXPOSE 80

# Commande pour démarrer Apache
CMD ["apache2-foreground"]
