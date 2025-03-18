# Utilisation de PHP et Apache
FROM php:8.2-apache

# Définir le répertoire de travail dans le conteneur
WORKDIR /var/www/arcadia

# Installation des extensions nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Copie des fichiers du projet dans le conteneur
COPY . /var/www/arcadia

# Activation des modules Apache (si nécessaire)
RUN a2enmod rewrite

# Exposer le port 80
EXPOSE 80

CMD ["apache2-foreground"]
