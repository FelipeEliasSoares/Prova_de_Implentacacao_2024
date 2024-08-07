# Usando a imagem oficial do PHP 8.3 com Apache
FROM php:8.3-apache

# Instalar extensões necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Configurar o diretório de trabalho
WORKDIR /var/www/html

# Remover o index.html padrão do Apache
RUN rm /var/www/html/index.html

# Copiar os arquivos do projeto
COPY ./src /var/www/html 

# Definir permissões apropriadas
RUN chown -R www-data:www-data /var/www/html

# Expor a porta 80
EXPOSE 80
