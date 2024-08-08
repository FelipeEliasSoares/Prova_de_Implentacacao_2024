+++
## Pré-requisitos
- **Docker**
- **Docker Compose**

## Configuração

### Passo 1: Clonar o repositório
Clone este repositório para a sua máquina local.

    git clone https://github.com/FelipeEliasSoares/Prova_de_Implentacacao_2024.git
    cd Prova_de_Implentacacao_2024

### Passo 2: Construir e iniciar os contêineres
Use o Docker Compose para construir e iniciar os contêineres.

    docker-compose up -d

Este comando irá:

- Construir a imagem do servidor web a partir do Dockerfile.
- Iniciar o contêiner MySQL e carregar o banco de dados inicial definido no arquivo `init.sql`.
- Iniciar o contêiner phpMyAdmin para gerenciar o banco de dados via interface gráfica.
- Esperar 30 segundos antes de iniciar o servidor Apache para garantir que o banco de dados esteja pronto.

### Passo 3: Acessar a aplicação
- **Aplicação Web:** Acesse [http://localhost](http://localhost).
- **phpMyAdmin:** Acesse [http://localhost:8081](http://localhost:8081) com as credenciais:

    - **Usuário:** `crud_user`
    - **Senha:** `senha_da_nasa`

## Estrutura do Projeto

- **src/:** Contém o código-fonte da aplicação Laravel.
- **init.sql:** Script SQL para inicializar o banco de dados.

## Configurações de Recursos
Os recursos dos serviços são limitados para garantir que a aplicação funcione corretamente em ambientes com hardware limitado.

- **Servidor Web (webserver):**
    - CPU: 1 núcleo
    - Memória: 300MB
- **Banco de Dados (db):**
    - CPU: 1 núcleo
    - Memória: 500MB
- **Gerenciador de Banco de Dados (phpMyAdmin):**
    - CPU: 4 núcleos
    - Memória: 200MB

## Observações Importantes

- **Sincronização do Banco de Dados:** Foi configurado um healthcheck no serviço `db` para garantir que o MySQL esteja pronto antes que outros serviços dependentes (como o servidor web e o phpMyAdmin) iniciem. Isso ajuda a evitar erros de conexão com o banco de dados durante a inicialização.
  
- **Atraso no Início do Servidor Web:** O serviço `webserver` possui um entrypoint customizado que adiciona um atraso de 30 segundos antes de iniciar o Apache. Isso é feito para garantir que o banco de dados esteja totalmente pronto para aceitar conexões.
  
- **Limitação de Recursos:** As limitações de recursos foram configuradas em conformidade com os requisitos de hardware do servidor. O serviço phpMyAdmin foi ajustado para utilizar até 4 núcleos de CPU, conforme o requisito estabelecido.
+++
