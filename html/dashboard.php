<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestão de Inventário</title>
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
                    <li><a href="stock.php"><i class="fas fa-boxes"></i> Stock</a></li>
                    <li><a href="encomendas.php"><i class="fas fa-truck"></i> Encomendas</a></li>
                </ul>
            </nav>
            <div class="user-profile">
                <img src="images/user-avatar.png" alt="User">
                <div>
                    <span class="username">Fernando Martins</span>
                    <span class="user-role">Funcionário</span>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
                  
            <!-- Seção Tabela -->
            <section class="report-section">
                <h2><i class="fas fa-table"></i> Stock Crítico</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Ficheiro</th>
                                <th>Categoria</th>
                                <th>Responsável</th>
				<th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nozes</td>
                                <td>PDF</td>
                                <td>CC</td>
                                <td>Tiago</td>
				<td>Enviado</td>
                            </tr>
                            <tr>
                                <td>Arroz Basmati</td>
                                <td>DOCX</td>
                                <td>Consumível</td>
                                <td>Albertino</td>
                                <td>Em Falta - Prioritário</td>
			     <tr>
				<td>Papel higiénico</td>
                                <td>CSV</td>
                                <td>Premium</td>
                                <td>Gervásio</td>
                                <td>A 30% do stock - Atenção</td>
			     </tr>
                        </tbody>
                    </table>
                </div>
            </section>
	    <section class="report-section">
                <h2><i class="fas fa-table"></i> Encomendas Urgentes</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Ficheiro</th>
                                <th>Categoria</th>
                                <th>Responsável</th>
				<th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nozes</td>
                                <td>PDF</td>
                                <td>CC</td>
                                <td>Tiago</td>
				<td>Enviado</td>
                            </tr>
                            <tr>
                                <td>Arroz Basmati</td>
                                <td>DOCX</td>
                                <td>Consumível</td>
                                <td>Albertino</td>
                                <td>Em Falta - Prioritário</td>
			     <tr>
				<td>Papel higiénico</td>
                                <td>CSV</td>
                                <td>Premium</td>
                                <td>Gervásio</td>
                                <td>A 30% do stock - Atenção</td>
			     </tr>
                        </tbody>
		    </table>
                </div>
            </section>
        </main>
    </div>
    <script>
// Verifica permissão para notificações
if ('Notification' in window) {
    if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                startDeliveryChecking();
            }
        });
    } else if (Notification.permission === 'granted') {
        startDeliveryChecking();
    }
}

function startDeliveryChecking() {
    // Verifica imediatamente ao carregar a página
    checkNewDeliveries();
    
    // Verifica a cada 30 segundos
    setInterval(checkNewDeliveries, 30000);
}

function checkNewDeliveries() {
    fetch('check-new-deliveries.php')
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                showNewDeliveryNotification(data.count);
            }
        })
        .catch(error => console.error('Error:', error));
}

function showNewDeliveryNotification(count) {
    const title = 'Nova Entrega Registada!';
    const options = {
        body: `Você tem ${count} nova(s) entrega(s) registada(s).`,
        icon: 'img/notification-icon.png',
        badge: 'img/badge.png'
    };
    
    // Notificação no navegador
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, options);
    }
    
    // Notificação na página (fallback)
    const notificationElement = document.createElement('div');
    notificationElement.className = 'floating-notification';
    notificationElement.innerHTML = `
        <strong>${title}</strong>
        <p>${options.body}</p>
    `;
    document.body.appendChild(notificationElement);
    
    setTimeout(() => {
        notificationElement.classList.add('fade-out');
        setTimeout(() => notificationElement.remove(), 500);
    }, 5000);
}
</script>
</body>
</html>
