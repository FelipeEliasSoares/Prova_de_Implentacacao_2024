# Imagem base do PHP com Apache
FROM ubuntu
LABEL maintainer="FelipeEliasSoares"

# Atualiza os pacotes do sistema e instala as dependências necessárias
RUN apt-get update && apt-get install -y apache2 libapache2-mod-php8.3 php8.3 php8.3-mysql

# Remove o arquivo index.html padrão do Apache
RUN rm -f /var/www/html/index.html

# Copia o código fonte para o diretório padrão do Apache
COPY src/ /var/www/html/

# Expondo a porta 80
EXPOSE 80

# Define o comando de inicialização do Apache
CMD ["apache2-foreground"]
