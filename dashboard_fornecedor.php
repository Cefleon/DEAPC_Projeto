<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$db = new SQLite3(__DIR__ . '/inventory.db');


$entregas = [];
$result = $db->query("SELECT id, company_name, Tipo, delivery_date, status FROM deliveries ORDER BY delivery_date DESC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $entregas[] = $row;
}

// Contar "entregas de hoje"
$hoje = date('Y-m-d');
$stmtHoje = $db->prepare("SELECT COUNT(*) as total FROM deliveries WHERE delivery_date = :hoje");
$stmtHoje->bindValue(':hoje', $hoje, SQLITE3_TEXT);
$entregas_hoje = $stmtHoje->execute()->fetchArray(SQLITE3_ASSOC)['total'] ?? 0;

// Contar "entregas em progresso"
$stmtProg = $db->query("SELECT COUNT(*) as total FROM deliveries WHERE status = 'in_progress'");
$em_progresso = $stmtProg->fetchArray(SQLITE3_ASSOC)['total'] ?? 0;

// Contar "entregas concluídas"
$stmtConc = $db->query("SELECT COUNT(*) as total FROM deliveries WHERE status = 'completed'");
$concluidas = $stmtConc->fetchArray(SQLITE3_ASSOC)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DEAPC Inventory</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="main-content">
        <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>

        <!-- Cards de Resumo -->
        <div class="dashboard-cards">
            <div class="card">
                <div class="card-header">
                    <h3>Entregas Hoje</h3>
                    <i class="fas fa-truck"></i>
                </div>
                <div class="card-value"><?php echo $entregas_hoje; ?></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Em Progresso</h3>
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="card-value"><?php echo $em_progresso; ?></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Concluídas</h3>
                    <i class="fas fa-check"></i>
                </div>
                <div class="card-value"><?php echo $concluidas; ?></div>
            </div>
        </div>

        <!-- Tabela de Entregas Recentes -->
        <section class="recent-activity">
            <h2><i class="fas fa-history"></i> Entregas Recentes</h2>
            <div class="table-scroll-container" style="max-height: 350px; overflow-y: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Companhia</th>
                            <th>Produto</th>
                            <th>Data</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entregas as $entrega): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($entrega['id']); ?></td>
                                <td><?php echo htmlspecialchars($entrega['company_name']); ?></td>
                                <td><?php echo htmlspecialchars($entrega['Tipo']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($entrega['delivery_date'])); ?></td>
                                <td>
                                    <span class="status <?php echo $entrega['status'] === 'completed' ? 'completed' : 'in-progress'; ?>">
                                        <?php echo $entrega['status'] === 'completed' ? 'Concluída' : 'Em Progresso'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    </div>
</body>

</html>