<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$db = new SQLite3(__DIR__ . '/inventory.db');

// Paginação
$limit = 5; // número de registos por página
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Contar total de registos
$totalQuery = $db->query("SELECT COUNT(*) as total FROM user_logins");
$total = $totalQuery->fetchArray(SQLITE3_ASSOC)['total'];
$totalPages = ceil($total / $limit);

// Buscar os registos com JOIN para obter o nome do utilizador
$query = $db->prepare("
    SELECT ul.login_datetime, u.name AS username
    FROM user_logins ul
    JOIN users u ON ul.user_id = u.id
    ORDER BY ul.login_datetime DESC
    LIMIT :limit OFFSET :offset
");
$query->bindValue(':limit', $limit, SQLITE3_INTEGER);
$query->bindValue(':offset', $offset, SQLITE3_INTEGER);
$result = $query->execute();


//Categorias (CRUD)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $catName = trim($_POST['category_name']);
    if ($catName !== '') {
        $stmt = $db->prepare("INSERT INTO Categorias (nome) VALUES (:nome)");
        $stmt->bindValue(':nome', $catName, SQLITE3_TEXT);
        $stmt->execute();
    }
    header("Location: index.php");
    exit();
}

// Editar categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {
    $catId = (int)$_POST['category_id'];
    $catName = trim($_POST['category_name']);
    if ($catName !== '') {
        $stmt = $db->prepare("UPDATE Categorias SET nome = :nome WHERE id = :id");
        $stmt->bindValue(':nome', $catName, SQLITE3_TEXT);
        $stmt->bindValue(':id', $catId, SQLITE3_INTEGER);
        $stmt->execute();
    }
    header("Location: index.php");
    exit();
}

// Eliminar categoria
if (isset($_GET['delete_category'])) {
    $catId = (int)$_GET['delete_category'];
    $stmt = $db->prepare("DELETE FROM Categorias WHERE id = :id");
    $stmt->bindValue(':id', $catId, SQLITE3_INTEGER);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

// Buscar categorias
$categories = $db->query("SELECT * FROM Categorias ORDER BY nome ASC");

// Buscar produtos e respetivo stock
$produtos = [];
$max_stock = 1; // evita divisão por zero
$res = $db->query("SELECT Produto, Quantidade FROM AtualizaStock ORDER BY Quantidade DESC");
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    $produtos[] = $row;
    if ($row['Quantidade'] > $max_stock) {
        $max_stock = $row['Quantidade'];
    }
}

?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Sistema de Inventário</title>
    <link rel="stylesheet" href="styles/dashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="dashboard-container">
        <?php include 'header.php'; ?>

        <main class="main-content">


            <section class="graph-section">
                <h2><i class="fas fa-chart-line"></i> Métricas</h2>
                <div class="graph-grid">
                    <div class="graph-card">
                        <h3>Gestão de Categorias</h3>
                        <div class="table-responsive" style="max-height: 200px; overflow-y: auto; margin-bottom: 10px;">
                            <table class="table table-sm align-middle mb-2">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nome</th>
                                        <th style="width: 90px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($cat = $categories->fetchArray(SQLITE3_ASSOC)): ?>
                                        <tr>
                                            <td>
                                                <form method="post" class="d-flex align-items-center gap-2 mb-0">
                                                    <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                                                    <input type="text" name="category_name" value="<?= htmlspecialchars($cat['nome']) ?>" class="form-control form-control-sm" style="width: 70%;">
                                                    <button type="submit" name="edit_category" class="btn btn-success btn-sm" title="Guardar">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                    <a href="?delete_category=<?= $cat['id'] ?>" onclick="return confirm('Eliminar esta categoria?')" class="btn btn-danger btn-sm" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </form>
                                            </td>
                                            <td></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <form method="post" class="d-flex gap-2">
                            <input type="text" name="category_name" placeholder="Nova categoria" required class="form-control form-control-sm">
                            <button type="submit" name="add_category" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Adicionar
                            </button>
                        </form>
                    </div>
                    <div class="graph-card">
                        <h3>Stock por Produto</h3>
                        <div class="barras-stock">
                            <?php foreach ($produtos as $prod):
                                $percent = $max_stock > 0 ? round(($prod['Quantidade'] / $max_stock) * 100, 2) : 0;
                            ?>
                                <div class="barra-produto">
                                    <div class="barra-label" title="<?= htmlspecialchars($prod['Produto']) ?>">
                                        <?= htmlspecialchars($prod['Produto']) ?>
                                    </div>
                                    <div class="barra-valor" style="width:<?= $percent ?>%"></div>
                                    <span class="barra-num"><?= $prod['Quantidade'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </section>

            <section class="report-section">
                <h2><i class="fas fa-clock"></i> Últimos Logins</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Utilizador</th>
                                <th>Data e Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= date('d/m/Y H:i:s', strtotime($row['login_datetime'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Navegação de páginas -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>" class="page-btn">Anterior</a>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>" class="page-btn">Próximo</a>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</body>

</html>