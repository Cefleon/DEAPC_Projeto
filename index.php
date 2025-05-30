<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Inventário</title>
    <link rel="stylesheet" href="styles/dashboard.css">
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
                    <li class="active"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="stock.php"><i class="fas fa-boxes"></i> Gestão de Stock</a></li>
                    <li><a href="encomendas.php"><i class="fas fa-truck"></i> Encomendas</a></li>
                    <li><a href="relatorios.php"><i class="fas fa-chart-bar"></i> Relatórios</a></li>
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

        <!-- Main Content -->
        <main class="main-content">
            <!-- Seção Topo: 5 Espaçinhos Métricos -->
            <div class="top-metrics">
                <!-- 1. Objetivo Mensal -->
                <div class="metric-card">
                    <h3><i class="fas fa-bullseye"></i> Objetivo Mensal</h3>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 75%;"></div>
                    </div>
                    <p>7.500€ <span>/ 10.000€</span></p>
                </div>

                <!-- 2. Taxa de Conversão -->
                <div class="metric-card">
                    <h3><i class="fas fa-chart-pie"></i> Taxa de Conversão</h3>
                    <div class="pie-mini" style="--value: 62;"></div>
                    <p>62% <span>(meta: 70%)</span></p>
                </div>

                <!-- 3. Valor Médio Venda -->
                <div class="metric-card">
                    <h3><i class="fas fa-tag"></i> Venda Média</h3>
                    <p class="big-number">248€ <span class="trend up">↗ 12%</span></p>
                </div>

                <!-- 4. Vendas Totais -->
                <div class="metric-card">
                    <h3><i class="fas fa-shopping-cart"></i> Vendas Totais</h3>
                    <p class="big-number">1.024 <span class="trend up">↗ 8%</span></p>
                    <div class="mini-line-chart"></div>
                </div>

                <!-- 5. Stock Crítico -->
                <div class="metric-card alert">
                    <h3><i class="fas fa-exclamation-triangle"></i> Stock Crítico</h3>
                    <p class="big-number">9 <span>itens</span></p>
                    <p class="alert-text">Reabastecer urgente!</p>
                </div>
            </div>

            <!-- Seção Gráficos -->
            <section class="graph-section">
                <h2><i class="fas fa-chart-line"></i> Métricas</h2>
                <div class="graph-grid">
                    <!-- Gráfico 1: Stock por Categoria (CSS) -->
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

                    <!-- Gráfico 2: Encomendas (CSS) -->
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

            <!-- Seção Tabela -->
            <section class="report-section">
                <h2><i class="fas fa-table"></i> Últimas Atividades</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Utilizador</th>
                                <th>Ação</th>
                                <th>Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>10/05/2024</td>
                                <td>
                                    <div class="user-cell">
                                        <img src="images/user1.jpg" alt="Fernando Martins">
                                        Fernando Martins
                                    </div>
                                </td>
                                <td>Atualizou stock</td>
                                <td>+50 unidades (Produto A)</td>
                            </tr>
                            <tr>
                                <td>09/05/2024</td>
                                <td>
                                    <div class="user-cell">
                                        <img src="images/user2.jpg" alt="Tiago Magalhães">
                                        Tiago Magalhães
                                    </div>
                                </td>
                                <td>Registou encomenda</td>
                                <td>Fornecedor XYZ (3 itens)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
