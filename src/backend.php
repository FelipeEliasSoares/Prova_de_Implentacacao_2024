<?php
session_start();

include 'config.php';

try {
    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['db']};charset=utf8", $config['user'], $config['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
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
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    return $stmt->execute([$username, $hashedPassword]);
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'login':
            if (loginUser($_POST['username'], $_POST['password'])) {
                echo json_encode(['success' => true, 'message' => 'Login bem-sucedido']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Usuário ou senha inválidos']);
            }
            break;

        case 'register':
            if (registerUser($_POST['username'], $_POST['password'])) {
                echo json_encode(['success' => true, 'message' => 'Usuário registrado com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao registrar usuário']);
            }
            break;

        case 'logout':
            session_destroy();
            echo json_encode(['success' => true, 'message' => 'Logout realizado com sucesso']);
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
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao salvar o livro']);
            }
            break;

        case 'deleteBook':
            requireLogin();
            $id = $_POST['id'];
            if (deleteBook($id)) {
                echo json_encode(['success' => true, 'message' => 'Livro excluído com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao excluir o livro']);
            }
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'getBooks') {
        requireLogin();
        echo json_encode(getBooks());
    } elseif (isset($_GET['action']) && $_GET['action'] === 'checkAuth') {
        echo json_encode(['authenticated' => isLoggedIn()]);
    }
}
?>