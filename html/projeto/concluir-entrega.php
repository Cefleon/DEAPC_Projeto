<?php
// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processa os dados do formulário
    $company = $_POST['company'] ?? '';
    $products = [];
    
    // Coleta todos os produtos e datas
    for ($i = 1; $i <= 2; $i++) {
        $product = $_POST["product$i"] ?? '';
        $date = $_POST["date$i"] ?? '';
        
        if (!empty($product) && !empty($date)) {
            $products[] = [
                'name' => $product,
                'date' => $date
            ];
        }
    }
    
    // Validação básica
    if (!empty($company) && !empty($products)) {
        // Aqui você pode adicionar o código para salvar no banco de dados
        // Por enquanto, vamos apenas exibir uma mensagem de sucesso
        $success_message = "Entrega para $company registrada com sucesso!";
    } else {
        $error_message = "Por favor, preencha pelo menos um produto e a data de entrega.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concluir Entrega - DEAPC Inventory</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="logo.png" alt="DEAPC Inventory">
                <h2>DEAPC Inventory</h2>
            </div>
            <nav class="menu">
                <ul>
                    <li><a href="dashboard.html"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                    <li class="active"><a href="concluir-entrega.php"><i class="fas fa-check-circle"></i> <span>Concluir Entrega</span></a></li>
                </ul>
            </nav>
            <div class="user-profile">
                <img src="images/user-avatar.png" alt="Fornecedor">
                <div>
                    <span class="username">Fornecedor</span>
                    <span class="user-role">Conta Fornecedor</span>
                </div>
            </div>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <h1><i class="fas fa-check-circle"></i> Concluir Entrega</h1>
            
            <?php if (isset($success_message)): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php elseif (isset($error_message)): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form action="concluir-entrega.php" method="post" class="delivery-form">
                <div class="form-group">
                    <label for="company"><i class="fas fa-building"></i> Nome da Companhia</label>
                    <input type="text" id="company" name="company" required value="<?php echo isset($company) ? htmlspecialchars($company) : ''; ?>">
                </div>
                
                <div class="products-table">
                    <h2><i class="fas fa-boxes"></i> Produtos Entregues</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Data de Entrega</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="product1" required value="<?php echo isset($products[0]['name']) ? htmlspecialchars($products[0]['name']) : ''; ?>"></td>
                                <td><input type="date" name="date1" required value="<?php echo isset($products[0]['date']) ? htmlspecialchars($products[0]['date']) : ''; ?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="product2" value="<?php echo isset($products[1]['name']) ? htmlspecialchars($products[1]['name']) : ''; ?>"></td>
                                <td><input type="date" name="date2" value="<?php echo isset($products[1]['date']) ? htmlspecialchars($products[1]['date']) : ''; ?>"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="form-footer">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-check"></i> Validar Entrega
                    </button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>