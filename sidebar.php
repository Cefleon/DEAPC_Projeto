<aside class="sidebar">
    <div class="logo">
        <h2>DEAPC Inventory</h2>
    </div>
    <nav class="menu">
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li class="active"><a href="concluir-entrega.php"><i class="fas fa-check-circle"></i> <span>Concluir Entrega</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a></li>
        </ul>
    </nav>
    <div class="user-profile">
        <div>
            <span class="username"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Usuário') ?></span>
            <span class="user-role"><?= htmlspecialchars($_SESSION['user']['role'] ?? 'Perfil') ?></span>
        </div>
    </div>
</aside>