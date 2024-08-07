# Imagem base do PHP com Apache
FROM php:8.3-apache

# Atualiza os pacotes do sistema e instala as dependências necessárias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Remove o arquivo index.html padrão do Apache
RUN rm -f /var/www/html/index.html

# Copia o código fonte para o diretório padrão do Apache
COPY src/ /var/www/html/

# Define a memória máxima para o PHP
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Habilita o mod_rewrite do Apache (caso seja necessário para reescrita de URLs)
RUN a2enmod rewrite

# Define o diretório de trabalho
WORKDIR /var/www/html

# Expondo a porta 80
EXPOSE 80

# Define o comando de inicialização do Apache
CMD ["apache2-foreground"]
