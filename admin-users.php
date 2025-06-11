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
$results = $db->query('SELECT id,username, email, role FROM users ORDER BY username');

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <div class="dashboard-container">
        <?php include 'header.php'; ?>

        <main class="main-content">


        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
    



            <div class="users-header">
                <h1><i class="fas fa-users-cog"></i> Utilizadores Registados</h1>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
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
                                    <?php
                                        $role_class_map = [
                                            'Administrador' => 'admin',
                                            'Fornecedor' => 'supplier',
                                            'Funcionario' => 'staff',
                                        ];
                                        $role = $user['role'];
                                        $badge_class = $role_class_map[$role] ?? 'badge-secondary';
                                        ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <?php echo htmlspecialchars($user['username']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo htmlspecialchars($role); ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button
                                            class="edit-btn"
                                            title="Editar"
                                            data-id="<?= $user['id'] ?>"
                                            data-username="<?= htmlspecialchars($user['username']) ?>"
                                            data-email="<?= htmlspecialchars($user['email']) ?>"
                                            data-role="<?= $user['role'] ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="delete-btn" title="Eliminar"
                                            onclick="if(confirm('Tem certeza que deseja eliminar este utilizador?')) 
                                                window.location.href='scripts/delete-user.php?id=<?= $user['id'] ?>'">
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


<!-- Modal para adicionar utilizador -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="scripts/add-user.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Adicionar Utilizador</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        <div class="mb-3">
          <label for="username" class="form-label">Nome de Utilizador</label>
          <input type="text" name="username" class="form-control" required />
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required />
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Palavra-passe</label>
          <input type="password" name="password" class="form-control" required />
        </div>

        <div class="mb-3">
          <label for="role" class="form-label">Tipo de Utilizador</label>
          <select name="role" class="form-select" required>
            <option value="Administrador">Administrador</option>
            <option value="Funcionario">Funcionário</option>
            <option value="Fornecedor">Fornecedor</option>
          </select>
        </div>

      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>


<!-- Modal para editar o utilizador -->

<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editUserForm" method="POST" action="scripts/editar_utilizador.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Utilizador</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="editUserId">

          <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" id="editUserEmail" readonly>
          </div>

          <div class="mb-3">
            <label>Username</label>
            <input type="text" class="form-control" id="editUsername" readonly>
          </div>

          <div class="mb-3">
            <label>Cargo</label>
            <select class="form-control" name="role" id="editUserRole" required>
              <option value="Administrador">Administrador</option>
              <option value="Fornecedor">Fornecedor</option>
            </select>
          </div>

          <div class="form-text">
            Para alterar a password, use a funcionalidade “Repor Password”.
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar alterações</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>


<script type="text/javascript" src="scripts/admin-users.js"></script>
<script type="text/javascript" src="scripts/edit-user.js"></script>
</body>
</html>
