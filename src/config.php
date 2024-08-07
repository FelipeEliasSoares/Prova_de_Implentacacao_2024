<?php
$servername = "db"; // Nome do serviço do MySQL definido no docker-compose
$username = "crud_user"; // Usuário do MySQL
$password = "senha_da_nasa"; // Senha do MySQL
$dbname = "biblioteca"; // Nome do banco de dados

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
echo "Conexão bem-sucedida ao banco de dados '$dbname'!";

// Fechar a conexão
$conn->close();


?>
