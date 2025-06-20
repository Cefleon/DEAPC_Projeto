<?php
$db = new SQLite3(__DIR__ . '/inventory.db');

// Dados do novo usuário
$dados_usuario = [
    'name' => 'Nome do Administrador',
    'email' => 'admin@dominio.com',
    'password' => 'senha_forte_aqui', // Substitua por uma senha segura
    'role' => 'Administrador'
];

// Hash da senha
$password_hash = password_hash($dados_usuario['password'], PASSWORD_DEFAULT);

// Inserir no banco
$stmt = $db->prepare("
    INSERT INTO users (name, email, password, role, created_at) 
    VALUES (:name, :email, :password, :role, datetime('now'))
");
$stmt->bindValue(':name', $dados_usuario['name']);
$stmt->bindValue(':email', $dados_usuario['email']);
$stmt->bindValue(':password', $password_hash);
$stmt->bindValue(':role', $dados_usuario['role']);

if ($stmt->execute()) {
    echo "✅ Usuário criado com sucesso!\n";
    echo "Email: " . $dados_usuario['email'] . "\n";
    echo "Senha: " . $dados_usuario['password'] . "\n";
} else {
    echo "❌ Erro ao criar usuário: " . $db->lastErrorMsg();
}

$db->close();
?>