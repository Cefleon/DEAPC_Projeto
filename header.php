<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$menus = [
    'Administrador' => [
        ['url' => 'index.php', 'icon' => 'fas fa-home', 'label' => 'Dashboard'],
        ['url' => 'stock.php', 'icon' => 'fas fa-boxes', 'label' => 'Gestão de Stock'],
        ['url' => 'encomendas.php', 'icon' => 'fas fa-truck', 'label' => 'Encomendas'],
        ['url' => 'relatorios.php', 'icon' => 'fas fa-chart-bar', 'label' => 'Relatórios'],
        ['url' => 'admin-users.php', 'icon' => 'fas fa-users-cog', 'label' => 'Utilizadores'],
    ],
    'Fornecedor' => [
        ['url' => 'dashboard_fornecedor.php', 'icon' => 'fas fa-home', 'label' => 'Dashboard'],
        ['url' => 'concluir-entrega.php', 'icon' => 'fas fa-truck', 'label' => 'Confirmar Entrega'],
    ],
    'Utilizador' => [
        ['url' => 'dashboard_funcionario.php', 'icon' => 'fas fa-home', 'label' => 'Dashboard'],
        ['url' => 'stock.php', 'icon' => 'fas fa-boxes', 'label' => 'Gestão de Stock'],
        ['url' => 'encomendas.php', 'icon' => 'fas fa-truck', 'label' => 'Encomendas'],
    ],
];

$menuItems = $menus[$role] ?? $menus['Utilizador'];
?>

<aside class="sidebar">
    <div class="logo">
        <img src="images/logo.jpg" alt="Logo" class="logo" />
        <h2>DEAPC Inventory</h2>
    </div>
    <nav class="menu">
        <ul>
            <?php foreach ($menuItems as $item): ?>
                <li <?= (basename($_SERVER['PHP_SELF']) === $item['url']) ? 'class="active"' : ''; ?>>
                    <a href="<?= $item['url']; ?>">
                        <i class="<?= $item['icon']; ?>"></i> <?= $item['label']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="user-profile">
        <div>
            <span class="username"><?= htmlspecialchars($username); ?></span>
            <span class="user-role"><?= htmlspecialchars($role); ?></span>
            <a href="logout.php" class="logout-btn">Sair</a>
        </div>
    </div>
</aside>
