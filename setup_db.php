<?php
// Mostrar erros para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Caminho do ficheiro da base de dados
$db_path = '/var/www/html/public_html/inventory.db';

// Apaga ficheiro antigo se existir (cuidado: isto apaga TUDO)
if (file_exists($db_path)) {
    unlink($db_path);
    echo "🗑️ Base de dados antiga apagada.<br>";
}

// Criar nova base de dados
$db = new SQLite3($db_path);
echo "✅ Nova base de dados criada com sucesso.<br>";

// Criar tabela de utilizadores
$create_table_sql = <<<SQL
CREATE TABLE usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    tipo TEXT NOT NULL
);
SQL;

if (!$db->exec($create_table_sql)) {
    die("❌ Erro ao criar tabela: " . $db->lastErrorMsg());
}
echo "✅ Tabela 'usuarios' criada com sucesso.<br>";

// Inserir utilizador de teste
$email = 'admin@example.com';
$password = password_hash('1234', PASSWORD_DEFAULT);
$tipo = 'Administrador';

$stmt = $db->prepare('INSERT INTO usuarios (email, password, tipo) VALUES (:email, :password, :tipo)');
$stmt->bindValue(':email', $email);
$stmt->bindValue(':password', $password);
$stmt->bindValue(':tipo', $tipo);

if ($stmt->execute()) {
    echo "✅ Utilizador admin@example.com criado com password '1234'.<br>";
} else {
    echo "⚠️ Erro ao inserir utilizador: " . $db->lastErrorMsg();
}
?>
