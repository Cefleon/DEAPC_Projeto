<aside class="sidebar">
    <div class="logo">
        <img src="logo.png" alt="DEAPC Inventory">
        <h2>DEAPC Inventory</h2>
    </div>
    <nav class="menu">
        <ul>
            <li <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'class="active"' : '' ?>>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
            </li>
            <li <?= basename($_SERVER['PHP_SELF']) === 'concluir-entrega.php' ? 'class="active"' : '' ?>>
                <a href="concluir-entrega.php"><i class="fas fa-check-circle"></i> <span>Concluir Entrega</span></a>
            </li>
        </ul>
    </nav>
    <div class="user-profile">
        <img src="images/user-avatar.png" alt="<?= htmlspecialchars($_SESSION['user']['name'] ?? 'Usuário') ?>">
        <div>
            <span class="username"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Usuário') ?></span>
            <span class="user-role">Conta Fornecedor</span>
        </div>
        <a href="logout.php" class="logout-btn" title="Sair">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</aside>