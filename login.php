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
         echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['erro_login']) . '</div>';

    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Inventário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            
            <div class="text-center mt-4">
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#projetoModal">
                    Sobre o Projeto
                </button>
            </div>
</div>

<!--Sobre o Projeto -->
        <div class="modal fade" id="projetoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Descrição do Projeto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>
                    Este projeto consiste em um Sistema de Gestão de Inventário com as seguintes funcionalidades:
                <ul>
                    <li>Gestão de níveis de stock em tempo real.</li>
                    <li>Processamento de encomendas e acompanhamento do seu cumprimento.</li>
                    <li>Interação com um servidor de base de dados para armazenar e recuperar dados de inventário.</li>
                    <li>Monitorização de compras e alertas de reabastecer stock.</li>
                </ul>
                </p>

                <h6>Elementos do Grupo</h6>
                <ul>
                    <li>[André Tavares, nº1222016]</li>
                    <li>[Fernando Martins, nº1232091]</li>
                    <li>[Tiago Magalhães, nº1241742]</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
            </div>
        </div>
        </div>

            
        </div>
    </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>