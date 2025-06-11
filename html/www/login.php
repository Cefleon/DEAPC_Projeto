<?php
session_start();

// Configuração do banco de dados SQLite
$dbPath = __DIR__ . '/database.sqlite';

// Criar conexão com SQLite
try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Criar tabelas se não existirem
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS deliveries (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        company_name TEXT NOT NULL,
        user_id INTEGER NOT NULL,
        delivery_date DATETIME NOT NULL,
        status TEXT DEFAULT 'in_progress',
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS delivery_products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        delivery_id INTEGER NOT NULL,
        product_name TEXT NOT NULL,
        delivery_date DATE NOT NULL,
        FOREIGN KEY (delivery_id) REFERENCES deliveries(id) ON DELETE CASCADE
    )");
    
    // Inserir usuário de teste se não existir (senha: 123456)
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE email = 'fornecedor@teste.com'");
    if ($stmt->fetchColumn() == 0) {
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Fornecedor Teste', 'fornecedor@teste.com', password_hash('123456', PASSWORD_DEFAULT), 'supplier']);
    }

} catch (PDOException $e) {
    die("Erro no banco de dados: " . $e->getMessage());
}

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'supplier'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Credenciais inválidas";
    }
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
                <img src="logo.png" alt="DEAPC Inventory">
                <h2>DEAPC Inventory</h2>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" required>
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