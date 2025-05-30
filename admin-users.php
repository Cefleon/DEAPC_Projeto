<?php
session_start();

// Apenas admins podem aceder
if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'Administrador') {
    header('Location: login.php');
    exit();
}

// Conexão à base de dados SQLite
$db = new SQLite3(__DIR__ . '/inventory.db');

// Obter lista de utilizadores
$results = $db->query('SELECT username, email, role FROM users ORDER BY username');

$users = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $users[] = $row;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestão de Utilizadores - Sistema de Inventário</title>
    <link rel="stylesheet" href="styles/dashboard.css" />
    <link rel="stylesheet" href="styles/admin-users.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <div class="dashboard-container">
        <?php include 'header.php'; ?>

        <main class="main-content">
            <div class="users-header">
                <h1><i class="fas fa-users-cog"></i> Utilizadores Registados</h1>
                <button class="add-user-btn" onclick="window.location.href='add-user.php'">
                    <i class="fas fa-plus"></i> Adicionar Utilizador
                </button>
            </div>

            <div class="table-container">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) === 0): ?>
                            <tr><td colspan="4">Nenhum utilizador registado.</td></tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <?php echo htmlspecialchars($user['username']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo strtolower($user['role']); ?>">
                                            <?php echo htmlspecialchars($user['role']); ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button class="edit-btn" title="Editar" onclick="window.location.href='edit-user.php?user=<?php echo urlencode($user['username']); ?>'">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="delete-btn" title="Eliminar" onclick="if(confirm('Tem certeza que deseja eliminar este utilizador?')) window.location.href='delete-user.php?user=<?php echo urlencode($user['username']); ?>'">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
