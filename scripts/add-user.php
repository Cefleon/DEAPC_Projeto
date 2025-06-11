<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'Administrador') {
    header('Location: ../login.php');
    exit();
}

$db = new SQLite3(__DIR__ . '/../inventory.db');
$db->busyTimeout(3000);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($username === '' || $email === '' || $password === '' || $role === '') {
        $_SESSION['error'] = 'Todos os campos são obrigatórios.';
        header('Location: ../admin-users.php');
        exit();
    }

    $stmtCheck = $db->prepare('SELECT username, email FROM users WHERE username = :username OR email = :email');
    $stmtCheck->bindValue(':username', $username, SQLITE3_TEXT);
    $stmtCheck->bindValue(':email', $email, SQLITE3_TEXT);
    $resultCheck = $stmtCheck->execute();

    $erros = [];
    while ($row = $resultCheck->fetchArray(SQLITE3_ASSOC)) {
        if ($row['username'] === $username) $erros[] = 'O nome de utilizador já existe.';
        if ($row['email'] === $email) $erros[] = 'Esse email já está registado.';
    }
    $stmtCheck->close();

    if ($erros) {
        $_SESSION['error'] = implode(' ', $erros);
        header('Location: ../admin-users.php');
        exit();
    }

    $createdAt = date('Y-m-d');
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare('INSERT INTO users (username, email, password, role, created_at) VALUES (:username, :email, :password, :role, :created_at)');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
    $stmt->bindValue(':role', $role, SQLITE3_TEXT);
    $stmt->bindValue(':created_at', $createdAt, SQLITE3_TEXT);
    $stmt->execute();
    $stmt->close();

    $db->close();

    $_SESSION['success'] = 'Utilizador adicionado com sucesso.';
    header('Location: ../admin-users.php');
    exit();
}

$db->close();
header('Location: ../admin-users.php');
exit();*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'Administrador') {
    header('Location: ../login.php');
    exit();
}

$db = new SQLite3(__DIR__ . '/../inventory.db');
$db->busyTimeout(10000);
$db->exec('PRAGMA journal_mode = WAL;');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($username === '' || $email === '' || $password === '' || $role === '') {
        $_SESSION['error'] = 'Todos os campos são obrigatórios.';
        header('Location: ../admin-users.php');
        exit();
    }

    $stmtCheck = $db->prepare('SELECT username, email FROM users WHERE username = :username OR email = :email');
    $stmtCheck->bindValue(':username', $username, SQLITE3_TEXT);
    $stmtCheck->bindValue(':email', $email, SQLITE3_TEXT);
    $resultCheck = $stmtCheck->execute();

    $erros = [];
    while ($row = $resultCheck->fetchArray(SQLITE3_ASSOC)) {
        if ($row['username'] === $username) $erros[] = 'O nome de utilizador já existe.';
        if ($row['email'] === $email) $erros[] = 'Esse email já está registado.';
    }
    $stmtCheck->close();

    if ($erros) {
        $_SESSION['error'] = implode(' ', $erros);
        header('Location: ../admin-users.php');
        exit();
    }

    $createdAt = date('Y-m-d');
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare('INSERT INTO users (username, email, password, role, created_at) VALUES (:username, :email, :password, :role, :created_at)');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
    $stmt->bindValue(':role', $role, SQLITE3_TEXT);
    $stmt->bindValue(':created_at', $createdAt, SQLITE3_TEXT);

    $result = $stmt->execute();
    if (!$result) {
        $_SESSION['error'] = 'Erro ao adicionar utilizador: ' . $db->lastErrorMsg();
        $stmt->close();
        $db->close();
        header('Location: ../admin-users.php');
        exit();
    }
    $stmt->close();
    $db->close();

    $_SESSION['success'] = 'Utilizador adicionado com sucesso.';
    header('Location: ../admin-users.php');
    exit();
}

$db->close();
header('Location: ../admin-users.php');
exit();


?>