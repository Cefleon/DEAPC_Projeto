<?php
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
    SELECT ul.login_datetime, u.username 
    FROM user_logins ul
    JOIN users u ON ul.user_id = u.id
    ORDER BY ul.login_datetime DESC
    LIMIT :limit OFFSET :offset
");
$query->bindValue(':limit', $limit, SQLITE3_INTEGER);
$query->bindValue(':offset', $offset, SQLITE3_INTEGER);
$result = $query->execute();
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Sistema de Inventário</title>
    <link rel="stylesheet" href="styles/dashboard.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <div class="dashboard-container">
        <?php include 'header.php'; ?>
        
        <main class="main-content">

            <div class="top-metrics">
                <div class="metric-card">
                    <h3><i class="fas fa-bullseye"></i> Objetivo Mensal</h3>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 75%;"></div>
                    </div>
                    <p>7.500€ <span>/ 10.000€</span></p>
                </div>

                <div class="metric-card">
                    <h3><i class="fas fa-chart-pie"></i> Taxa de Conversão</h3>
                    <div class="pie-mini" style="--value: 62;"></div>
                    <p>62% <span>(meta: 70%)</span></p>
                </div>

                <div class="metric-card">
                    <h3><i class="fas fa-tag"></i> Venda Média</h3>
                    <p class="big-number">248€ <span class="trend up">↗ 12%</span></p>
                </div>

                <div class="metric-card">
                    <h3><i class="fas fa-shopping-cart"></i> Vendas Totais</h3>
                    <p class="big-number">1.024 <span class="trend up">↗ 8%</span></p>
                    <div class="mini-line-chart"></div>
                </div>

                <div class="metric-card alert">
                    <h3><i class="fas fa-exclamation-triangle"></i> Stock Crítico</h3>
                    <p class="big-number">9 <span>itens</span></p>
                    <p class="alert-text">Reabastecer urgente!</p>
                </div>
            </div>

            <section class="graph-section">
                <h2><i class="fas fa-chart-line"></i> Métricas</h2>
                <div class="graph-grid">
                    <div class="graph-card">
                        <h3>Stock por Categoria</h3>
                        <div class="bar-chart">
                            <div class="bar-container">
                                <div class="bar-label">Alimentação</div>
                                <div class="bar" style="--value: 75%; background: #3498db;"></div>
                                <div class="bar-value">75</div>
                            </div>
                            <div class="bar-container">
                                <div class="bar-label">Informática</div>
                                <div class="bar" style="--value: 50%; background: #2ecc71;"></div>
                                <div class="bar-value">50</div>
                            </div>
                            <div class="bar-container">
                                <div class="bar-label">Mobiliário</div>
                                <div class="bar" style="--value: 30%; background: #f39c12;"></div>
                                <div class="bar-value">30</div>
                            </div>
                        </div>
                    </div>

                    <div class="graph-card">
                        <h3>Encomendas Mensais</h3>
                        <div class="line-chart">
                            <div class="chart-lines">
                                <div class="line" style="--height: 80%;"></div>
                                <div class="line" style="--height: 60%;"></div>
                                <div class="line" style="--height: 30%;"></div>
                                <div class="line" style="--height: 90%;"></div>
                            </div>
                            <div class="chart-labels">
                                <span>Jan</span>
                                <span>Fev</span>
                                <span>Mar</span>
                                <span>Abr</span>
                            </div>
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
                    </table>
                </div>
            </section>

        </main>
    </div>
</body>
</html>
