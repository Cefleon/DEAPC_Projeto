<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Utilizadores - Sistema de Inventário</title>
    <link rel="stylesheet" href="styles/dashboard.css">
    <link rel="stylesheet" href="styles/admin-users.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="images/logo.jpg" alt="Logo" class="logo">
                <h2>DEAPC Inventory</h2>
            </div>
            <nav class="menu">
                <ul>
                    <li><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="stock.php"><i class="fas fa-boxes"></i> Gestão de Stock</a></li>
                    <li><a href="encomendas.php"><i class="fas fa-truck"></i> Encomendas</a></li>
                    <li><a href="relatorios.php"><i class="fas fa-chart-bar"></i> Relatórios</a></li>
                    <li class="active" class="admin-only"><a href="admin-users.php"><i class="fas fa-users-cog"></i> Utilizadores</a></li>
                </ul>
            </nav>
            <div class="user-profile">
                <img src="images/user-avatar.png" alt="User">
                <div>
                    <span class="username">André Tavares</span>
                    <span class="user-role">Administrador</span>
                </div>
            </div>
        </aside>

    <!-- Conteúdo Principal -->
        <main class="main-content">
            <div class="users-header">
                <h1><i class="fas fa-users-cog"></i> Utilizadores Registados</h1>
                <button class="add-user-btn">
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


                    <tr>
                        <td>
                            <div class="user-cell">
                                <img src="images/user1.jpg" alt="André Tavares">
                                André Tavares
                            </div>
                        </td>
                        <td>1222016@isep.ipp.pt</td>
                        <td><span class="badge admin">Administrador</span></td>
                        <td class="actions">
                            <button class="edit-btn" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="delete-btn" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>

                                        <tr>
                        <td>
                            <div class="user-cell">
                                <img src="images/user1.jpg" alt="Fernando Martins">
                                Fernando Martins
                            </div>
                        </td>
                        <td>1232091@isep.ipp.pt</td>
                        <td><span class="badge admin">Administrador</span></td>
                        <td class="actions">
                            <button class="edit-btn" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="delete-btn" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="user-cell">
                                <img src="images/user1.jpg" alt="Tiago Magalhães">
                                Tiago Magalhães
                            </div>
                        </td>
                        <td>1241742@isep.ipp.pt</td>
                        <td><span class="badge admin">Administrador</span></td>
                        <td class="actions">
                            <button class="edit-btn" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="delete-btn" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>

                </table>
            </div>


        </main>
    </div>
</body>
</html>
