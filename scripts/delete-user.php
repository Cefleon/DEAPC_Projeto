<?php
session_start();

if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'Administrador') {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'ID do utilizador não fornecido.';
    header('Location: ../admin-users.php');
    exit();
}

$id = intval($_GET['id']);

$db = new SQLite3(__DIR__ . '/../inventory.db');

$stmt = $db->prepare('DELETE FROM users WHERE id = :id');
$stmt->bindValue(':id', $id, SQLITE3_INTEGER);

$result = $stmt->execute();

if ($result) {
    $_SESSION['success'] = 'Utilizador eliminado com sucesso.';
} else {
    $_SESSION['error'] = 'Erro ao eliminar utilizador: ' . $db->lastErrorMsg();
}

header('Location: ../admin-users.php');
exit();
?>
