<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$dbPath = __DIR__ . '/database.sqlite';
try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obter estatísticas
    $today = date('Y-m-d');
    $userId = $_SESSION['user']['id'];

    $stats = [
        'today' => $db->query("SELECT COUNT(*) FROM deliveries WHERE user_id = $userId AND date(delivery_date) = '$today'")->fetchColumn(),
        'in_progress' => $db->query("SELECT COUNT(*) FROM deliveries WHERE user_id = $userId AND status = 'in_progress'")->fetchColumn(),
        'completed' => $db->query("SELECT COUNT(*) FROM deliveries WHERE user_id = $userId AND status = 'completed'")->fetchColumn()
    ];

    // Obter entregas recentes
    $recentDeliveries = $db->query("
        SELECT d.id, d.company_name, p.product_name, 
               strftime('%d/%m/%Y', p.delivery_date) as formatted_date, 
               d.status
        FROM deliveries d
        JOIN delivery_products p ON d.id = p.delivery_id
        WHERE d.user_id = $userId
        ORDER BY p.delivery_date DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro no banco de dados: " . $e->getMessage());
}
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
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-header">
                        <h3>Entregas Hoje</h3>
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="card-value"><?= $stats['today'] ?></div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Em Progresso</h3>
                        <i class="fas fa-spinner"></i>
                    </div>
                    <div class="card-value"><?= $stats['in_progress'] ?></div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Concluídas</h3>
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="card-value"><?= $stats['completed'] ?></div>
                </div>
            </
