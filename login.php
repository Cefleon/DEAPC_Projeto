<?php
session_start();
date_default_timezone_set('Europe/Lisbon');

// Ativa relatórios de erro
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new SQLite3(__DIR__ . '/inventory.db');
    $db->exec("PRAGMA foreign_keys = ON");

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];

        $user_id = $user['id'];
        $login_datetime = date('Y-m-d H:i:s');

        // DEBUG log
        error_log("Login OK: user_id=$user_id, datetime=$login_datetime");

        // Inserir registo de login
        $insert = $db->prepare("INSERT INTO user_logins (user_id, login_datetime) VALUES (:user_id, :login_datetime)");
        $insert->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $insert->bindValue(':login_datetime', $login_datetime, SQLITE3_TEXT);
        $insert_result = $insert->execute();

        if (!$insert_result) {
            error_log("Erro ao inserir em user_logins: " . $db->lastErrorMsg());
            $_SESSION['erro_login'] = "Erro ao gravar login.";
        }

        // Redirecionar
        if ($user['role'] === 'Administrador') {
            header('Location: index.php');
        } else {
            header('Location: dashboard_fornecedor.html');
        }
        exit;
    } else {
        $_SESSION['erro_login'] = "Credenciais inválidas.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Inventário</title>
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <img src="images/logo.jpg" alt="Logo" class="logo">
            <form action="" method="POST">
                <h1>Login</h1>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="exemplo@dominio.com" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Introduza a password" required>
                
                <button type="submit" class="login-btn">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>
