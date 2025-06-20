<?php
session_start();
date_default_timezone_set('Europe/Lisbon');

// Exibir erros durante o desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se o ficheiro da base de dados existe
    $dbPath = __DIR__ . '/inventory.db';
    if (!file_exists($dbPath)) {
        die('Base de dados não encontrada!');
    }

    // Conexão com o banco de dados
    $db = new SQLite3($dbPath);
    $db->exec("PRAGMA foreign_keys = ON");

    // Obtenção dos dados do formulário
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Buscar utilizador pelo email
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    if (!$stmt) {
        die("Erro ao preparar consulta: " . $db->lastErrorMsg());
    }

    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    // Verificação da password
    if ($user && password_verify($password, $user['password'])) {
        // Definir variáveis de sessão
        $_SESSION['user_id'] = $user['id'];      // Corrigido: adiciona user_id
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['name'];

        // Registo de login
        $user_id = $user['id'];
        $login_datetime = date('Y-m-d H:i:s');

        $insert = $db->prepare("INSERT INTO user_logins (user_id, login_datetime) VALUES (:user_id, :login_datetime)");
        if ($insert) {
            $insert->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            $insert->bindValue(':login_datetime', $login_datetime, SQLITE3_TEXT);
            $insert_result = $insert->execute();

            if (!$insert_result) {
                error_log("Erro ao inserir em user_logins: " . $db->lastErrorMsg());
                $_SESSION['erro_login'] = "Erro ao gravar login.";
            }
        } else {
            error_log("Erro na preparação do INSERT: " . $db->lastErrorMsg());
        }

        // Redirecionamento após login
        if ($user['role'] === 'Administrador') {
            header('Location: index.php');
        } else if ($user['role'] === 'Fornecedor') {
            header('Location: dashboard_fornecedor.php');
        } else {
            header('Location: dashboard_funcionario.php');
        }
        exit;
    } else {
        $_SESSION['erro_login'] = "Credenciais inválidas.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Inventário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <img src="images/logo.jpg" alt="Logo" class="logo">
            <form action="" method="POST">
                <h1>Login</h1>

                <?php if (isset($_SESSION['erro_login'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($_SESSION['erro_login']) ?>
                    </div>
                    <?php unset($_SESSION['erro_login']); ?>
                <?php endif; ?>

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

        <!-- Modal "Sobre o Projeto" -->
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
                            <li>André Tavares, nº1222016</li>
                            <li>Fernando Martins, nº1232091</li>
                            <li>Tiago Magalhães, nº1241742</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
