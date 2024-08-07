# Use a imagem base do PHP com Apache
FROM php:7.4-apache

# Instale extensões PHP necessárias
RUN docker-php-ext-install pdo pdo_mysql

# Copie os arquivos da pasta src para o diretório padrão do Apache
COPY src/ /var/www/html/

# Ajuste as permissões dos arquivos
RUN chown -R www-data:www-data /var/www/html

# Configure o Apache para escutar na porta 80 (padrão)
EXPOSE 80