# Uma imagem base do PHP com Apache
FROM php:7.4-apache

# Remove o arquivo index.html padrão do Apache
RUN rm -f /var/www/html/index.html

# Copia o código fonte para o diretório padrão do Apache
COPY src/ /var/www/html/

# Instala extensões do PHP necessárias (ex: mysqli)
RUN docker-php-ext-install mysqli

# Define a memória máxima para o PHP
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory-limit.ini
