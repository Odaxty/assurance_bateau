# 1. On utilise une image PHP officielle avec Apache
FROM php:8.2-apache

# 2. Installation des dépendances système (nécessaires pour PHP et Dompdf)
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip libpng-dev libicu-dev \
    && docker-php-ext-install zip gd intl pdo pdo_mysql

# 3. Configuration d'Apache pour pointer vers le dossier /public de Symfony
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# 4. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Copie du projet
WORKDIR /var/www/html
COPY . .

# 6. Installation des dépendances PHP (sans les outils de dev)
RUN composer install --no-dev --optimize-autoloader

# 7. Droits d'écriture pour le cache et les logs
RUN chown -R www-data:www-data var/
