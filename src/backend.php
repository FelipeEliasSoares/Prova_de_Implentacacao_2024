<?php
session_start();

$servername = "db";
$username = "crud_user";
$password = "senha_da_nasa";
$database = "biblioteca";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro na conexão: ' . $e->getMessage()]);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
        exit;
    }
}

function loginUser($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        return true;
    }
    return false;
}

function registerUser($username, $password) {
    global $pdo;

    // Verificar se o nome de usuário já está em uso
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        return ['success' => false, 'message' => 'Nome de usuário já está em uso.'];
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt->execute([$username, $hashedPassword])) {
        return ['success' => true, 'message' => 'Usuário registrado com sucesso'];
    } else {
        return ['success' => false, 'message' => 'Erro ao registrar usuário'];
    }
}

function addBook($title, $author) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO books (title, author) VALUES (?, ?)");
    return $stmt->execute([$title, $author]);
}

function updateBook($id, $title, $author) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ? WHERE id = ?");
    return $stmt->execute([$title, $author, $id]);
}

function deleteBook($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    return $stmt->execute([$id]);
}

function getBooks() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM books");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function sendJsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'login':
                if (loginUser($_POST['username'], $_POST['password'])) {
                    sendJsonResponse(['success' => true, 'message' => 'Login bem-sucedido']);
                } else {
                    sendJsonResponse(['success' => false, 'message' => 'Usuário ou senha inválidos']);
                }
                break;

            case 'register':
                $result = registerUser($_POST['username'], $_POST['password']);
                sendJsonResponse($result);
                break;

            case 'logout':
                session_destroy();
                sendJsonResponse(['success' => true, 'message' => 'Logout realizado com sucesso']);
                break;

            case 'addEditBook':
                requireLogin();
                $id = $_POST['id'] ?? null;
                $title = $_POST['title'];
                $author = $_POST['author'];

                if ($id) {
                    $result = updateBook($id, $title, $author);
                    $message = 'Livro atualizado com sucesso';
                } else {
                    $result = addBook($title, $author);
                    $message = 'Livro adicionado com sucesso';
                }

                if ($result) {
                    sendJsonResponse(['success' => true, 'message' => $message]);
                } else {
                    sendJsonResponse(['success' => false, 'message' => 'Erro ao salvar o livro']);
                }
                break;

            case 'deleteBook':
                requireLogin();
                $id = $_POST['id'];
                if (deleteBook($id)) {
                    sendJsonResponse(['success' => true, 'message' => 'Livro excluído com sucesso']);
                } else {
                    sendJsonResponse(['success' => false, 'message' => 'Erro ao excluir o livro']);
                }
                break;
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';

        if ($action === 'getBooks') {
            requireLogin();
            sendJsonResponse(getBooks());
        } elseif ($action === 'checkAuth') {
            sendJsonResponse(['authenticated' => isLoggedIn()]);
        }
    }
} catch (Exception $e) {
    sendJsonResponse(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>
