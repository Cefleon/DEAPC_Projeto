<?php
session_start();

if (!isset($_POST['id'], $_POST['role'])) {
    $_SESSION['error'] = 'Dados incompletos.';
    header('Location: /admin-users.php');
    exit();
}

$id = intval($_POST['id']);
$role = $_POST['role'];

$rolesPermitidos = ['Administrador', 'Fornecedor', 'Funcionario'];  // <-- aqui adicionei Funcionario
if (!in_array($role, $rolesPermitidos)) {
    $_SESSION['error'] = 'Cargo inválido.';
    header('Location: /admin-users.php');
    exit();
}

$db_path = __DIR__ . '/../inventory.db';
$db = new SQLite3($db_path);

$stmt = $db->prepare('UPDATE users SET role = :role WHERE id = :id');

if (!$stmt) {
    $_SESSION['error'] = 'Erro! ' . $db->lastErrorMsg();
    header('Location: /admin-users.php');
    exit();
}

$stmt->bindValue(':role', $role, SQLITE3_TEXT);
$stmt->bindValue(':id', $id, SQLITE3_INTEGER);

$result = $stmt->execute();

if ($result) {
    $_SESSION['success'] = 'Cargo atualizado com sucesso.';
} else {
    $_SESSION['error'] = 'Erro ao atualizar cargo: ' . $db->lastErrorMsg();
}
header('Location: /admin-users.php');
exit();
?>
