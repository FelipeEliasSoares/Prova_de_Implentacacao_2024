# Use uma imagem base do PHP com Apache
FROM php:7.4-apache

# Atualize os pacotes e instale utilitários necessários
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Remove o arquivo index.html padrão do Apache
RUN rm -f /var/www/html/index.html

# Copia o código fonte para o diretório padrão do Apache
COPY src/ /var/www/html/

# Define a memória máxima para o PHP
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Ative o mod_rewrite do Apache
RUN a2enmod rewrite

# Defina o DocumentRoot e configure o Apache
RUN echo "<Directory /var/www/html/> \
    Options Indexes FollowSymLinks \
    AllowOverride All \
    Require all granted \
    </Directory>" > /etc/apache2/conf-available/docker-php.conf

RUN a2enconf docker-php

# Exponha a porta 80 para o Apache
EXPOSE 80

# Inicie o serviço do Apache
CMD ["apache2-foreground"]
