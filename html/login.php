<?php
session_start();

// Habilitar erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

$dbPath = '/var/www/html/database.sqlite';
$error = null;

try {
    // Conexão com o banco de dados
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        // Log para debug
        error_log("Tentativa de login - Email: $email");

        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            error_log("Usuário encontrado: " . print_r($user, true));
            
            // Verificação da senha
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                header('Location: dashboard.php');
                exit();
            } else {
                error_log("Senha incorreta para o usuário: $email");
            }
        } else {
            error_log("Usuário não encontrado: $email");
        }

        $error = "Credenciais inválidas. Use email: fornecedor@teste.com e senha: password";
    }
} catch (PDOException $e) {
    $error = "Erro no banco de dados. Por favor, tente novamente mais tarde.";
    error_log("PDO Error: " . $e->getMessage());
} catch (Exception $e) {
    $error = "Ocorreu um erro inesperado.";
    error_log("General Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DEAPC Inventory</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <h2>DEAPC Inventory</h2>
            </div>
            
            <?php if ($error): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Senha</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
            </form>
        </div>
    </div>
</body>
</html>
