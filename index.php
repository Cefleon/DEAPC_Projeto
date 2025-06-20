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


// Obter stock por categoria
/*$stockQuery = $db->query("
    SELECT c.nome, SUM(p.stock) as total_stock
    FROM Categorias c
    LEFT JOIN Produtos p ON p.categoria_id = c.id
    GROUP BY c.id
    ORDER BY c.nome ASC
");

$stockData = [];
$maxStock = 0;
while ($row = $stockQuery->fetchArray(SQLITE3_ASSOC)) {
    $stockData[] = $row;
    if ($row['total_stock'] > $maxStock) {
        $maxStock = $row['total_stock'];
    }
}*/

?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Sistema de Inventário</title>
    <link rel="stylesheet" href="styles/dashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
    <div class="dashboard-container">
        <?php include 'header.php'; ?>

        <main class="main-content">


            <section class="graph-section">
                <h2><i class="fas fa-chart-line"></i> Métricas</h2>
                <div class="graph-grid">
                    <div class="graph-card">

                    <div class="graph-card">
                        <h3>Gestão de Categorias</h3>
                        <div style="max-height: 200px; overflow-y: auto; margin-bottom: 10px;">
                            <table style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th style="width: 90px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($cat = $categories->fetchArray(SQLITE3_ASSOC)): ?>
                                        <tr>
                                            <form method="post" style="display:inline;">
                                                <td>
                                                    <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                                                    <input type="text" name="category_name" value="<?= htmlspecialchars($cat['nome']) ?>" style="width:90%;">
                                                </td>
                                                <td>
                                                    <button type="submit" name="edit_category" title="Guardar"><i class="fas fa-save"></i></button>
                                                    <a href="?delete_category=<?= $cat['id'] ?>" onclick="return confirm('Eliminar esta categoria?')" title="Eliminar"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </form>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <form method="post" style="display:flex; gap:5px;">
                            <input type="text" name="category_name" placeholder="Nova categoria" required style="flex:1;">
                            <button type="submit" name="add_category"><i class="fas fa-plus"></i> Adicionar</button>
                        </form>
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