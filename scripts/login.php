<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$db_path = '/var/www/html/public_html/inventory.db';

if (!file_exists($db_path)) {
    die("Database file not found in $db_path.");
}

$db = new SQLite3($db_path);

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $db->prepare('SELECT * FROM usuarios WHERE email = :email');
$stmt->bindValue(':email', $email);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    // Autenticação bem-sucedida
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];

    switch ($user['tipo']) {
        case 'Administrador':
            header('Location: ../index.html');
            break;
        default:
            header('Location: ../index.html'); // ou outra página para outros tipos
            break;
    }
    exit();
} else {
    // Autenticação falhou
    header('Location: ../login.html?error=Credenciais inválidas');
    exit();
}
?>
