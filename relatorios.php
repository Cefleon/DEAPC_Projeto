<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Sistema de Inventário</title>
    <link rel="stylesheet" href="styles/dashboard.css">
    <link rel="stylesheet" href="styles/relatorios.css">
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
                    <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="stock.php"><i class="fas fa-boxes"></i> Gestão de Stock</a></li>
                    <li><a href="encomendas.php"><i class="fas fa-truck"></i> Encomendas</a></li>
                    <li class="active"><a href="relatorios.php"><i class="fas fa-chart-bar"></i> Relatórios</a></li>
                    <li class="admin-only"><a href="admin-users.php"><i class="fas fa-users-cog"></i> Utilizadores</a></li>
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
            <h1><i class="fas fa-file-export"></i> Gerar Relatório</h1>
            
            <form class="report-form">
                <!-- Tipo de Relatório -->
                <div class="form-group">
                    <label for="report-type">Tipo de Relatório:</label>
                    <select id="report-type" name="report-type">
                        <option value="stock">Stock</option>
                        <option value="orders">Encomendas</option>
                        <!-- Mostrar apenas para Admin -->
                        <option value="users">Utilizadores</option>
                    </select>
                </div>

                <!-- Datas Início e Fim -->
                <div class="form-group">
                    <label for="date-start">Data Início:</label>
                    <input type="date" id="date-start" name="date-start">
                </div>
                <div class="form-group">
                    <label for="date-end">Data Fim:</label>
                    <input type="date" id="date-end" name="date-end">
                </div>

                <!-- Formato -->
                <div class="form-group">
                    <label>Formato:</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="format" value="csv" checked> CSV
                        </label>
                        <label>
                            <input type="radio" name="format" value="pdf"> PDF
                        </label>
                    </div>
                </div>

                <!-- Botão -->
                <button type="submit" class="submit-btn">
                    <i class="fas fa-download"></i> Gerar Relatório
                </button>
            </form>
        </main>
    </div>
</body>
</html>
