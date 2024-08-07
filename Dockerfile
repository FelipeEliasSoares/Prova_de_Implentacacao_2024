# Imagem base do PHP com Apache
FROM php:8.3-apache

# Atualiza os pacotes do sistema e instala o Apache (embora a imagem base já inclua o Apache)
RUN apt-get update && apt-get install -y apache2

# Remove o arquivo index.html padrão do Apache
RUN rm -f /var/www/html/index.html

# Copia o código fonte para o diretório padrão do Apache
COPY src/ /var/www/html/

# Instala a extensão mysqli do PHP
RUN docker-php-ext-install mysqli

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
