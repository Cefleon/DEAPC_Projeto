<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$menus = [
    'Administrador' => [
        ['url' => 'index.php', 'icon' => 'fas fa-home', 'label' => 'Dashboard'],
        ['url' => 'stock.html', 'icon' => 'fas fa-boxes', 'label' => 'Gestão de Stock'],
        ['url' => 'encomendas.html', 'icon' => 'fas fa-truck', 'label' => 'Encomendas'],
        ['url' => 'relatorios.php', 'icon' => 'fas fa-chart-bar', 'label' => 'Relatórios'],
        ['url' => 'admin-users.php', 'icon' => 'fas fa-users-cog', 'label' => 'Utilizadores'],
    ],
    'Fornecedor' => [
        ['url' => 'dashboard_fornecedor.html', 'icon' => 'fas fa-home', 'label' => 'Dashboard'],
        ['url' => 'encomendas.html', 'icon' => 'fas fa-truck', 'label' => 'Encomendas'],
    ],
    'Utilizador' => [
        ['url' => 'index.php', 'icon' => 'fas fa-home', 'label' => 'Dashboard'],
        ['url' => 'stock.html', 'icon' => 'fas fa-boxes', 'label' => 'Gestão de Stock'],
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
                <li <?php echo ($_SERVER['PHP_SELF'] === '/' . $item['url']) ? 'class="active"' : ''; ?>>
                    <a href="<?php echo $item['url']; ?>">
                        <i class="<?php echo $item['icon']; ?>"></i> <?php echo $item['label']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="user-profile">
        <div>
            <span class="username"><?php echo htmlspecialchars($username); ?></span>
            <span class="user-role"><?php echo htmlspecialchars($role); ?></span>
            <form method="POST" action="logout.php" style="display:inline;">
                <a href="logout.php" class="btn btn-outline-light btn-sm">Sair</a>
            </form>
        </div>
    </div>
</aside>