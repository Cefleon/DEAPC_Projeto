<?php

session_start();



if (!isset($_POST['id'], $_POST['role'])) {
    $_SESSION['error'] = 'Dados incompletos.';
    header('Location: /admin-users.php');
    exit();
}

$id = intval($_POST['id']);
$role = $_POST['role'];

$rolesPermitidos = ['Administrador', 'Fornecedor'];
if (!in_array($role, $rolesPermitidos)) {
    $_SESSION['error'] = 'Cargo inválido.';
    header('Location: /admin-users.php');
    exit();
}

$db_path = __DIR__ . '/../inventory.db';
echo "Abrindo DB em: $db_path\n";
$db = new SQLite3($db_path);

$stmt = $db -> prepare('UPDATE users SET role = :role WHERE id = :id');

if(!$stmt) {
    $_SESSION['error'] = 'Erro!' . $db->lastErrorMsg();
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




/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'Administrador') {
    header('Location: login.php'); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)($_POST['id']   ?? 0);
    $role = trim($_POST['role'] ?? '');

    if ($id && $role) {
        $db = new SQLite3(__DIR__ . '/../inventory.db');
        $stmt = $db->prepare('UPDATE users SET role = :role WHERE id = :id');
        $stmt->bindValue(':role', $role, SQLITE3_TEXT);
        $stmt->bindValue(':id',   $id,   SQLITE3_INTEGER);
        $ok = $stmt->execute();
        $_SESSION[$ok ? 'success' : 'error'] =
            $ok ? 'Cargo atualizado.' : 'Erro ao atualizar.';
    }
    header('Location: /admin-users.php');
    exit();
}*/
?>