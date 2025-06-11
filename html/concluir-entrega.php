<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$dbPath = __DIR__ . '/database.sqlite';
$error = null;
$success = null;

try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_STRING);
        $products = [];
        
        // Coletar produtos
        for ($i = 1; $i <= 2; $i++) {
            $product = filter_input(INPUT_POST, "product$i", FILTER_SANITIZE_STRING);
            $date = filter_input(INPUT_POST, "date$i", FILTER_SANITIZE_STRING);
            
            if (!empty($product) && !empty($date)) {
                $products[] = [
                    'name' => $product,
                    'date' => $date
                ];
            }
        }
        
        if (!empty($company) && !empty($products)) {
            // Inserir entrega principal
            $stmt = $db->prepare("INSERT INTO deliveries (company_name, user_id, delivery_date, status) 
                                 VALUES (?, ?, ?, 'completed')");
            $stmt->execute([$company, $_SESSION['user']['id'], date('Y-m-d H:i:s')]);
            $deliveryId = $db->lastInsertId();
            
            // Inserir produtos
            foreach ($products as $product) {
                $stmt = $db->prepare("INSERT INTO delivery_products (delivery_id, product_name, delivery_date) 
                                     VALUES (?, ?, ?)");
                $stmt->execute([$deliveryId, $product['name'], $product['date']]);
            }
            
            $success = "Entrega para $company registrada com sucesso!";
            $_SESSION['new_delivery_added'] = true;
        } else {
            $error = "Por favor, preencha pelo menos um produto e a data de entrega.";
        }
    }
} catch (PDOException $e) {
    $error = "Erro no banco de dados: " . $e->getMessage();
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
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <h1><i class="fas fa-check-circle"></i> Concluir Entrega</h1>
            
            <?php if ($success): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php elseif ($error): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="post" class="delivery-form">
                <div class="form-group">
                    <label for="company"><i class="fas fa-building"></i> Nome da Companhia</label>
                    <input type="text" id="company" name="company" required 
                           value="<?= isset($_POST['company']) ? htmlspecialchars($_POST['company']) : '' ?>">
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
                            <?php for ($i = 1; $i <= 2; $i++): ?>
                            <tr>
                                <td>
                                    <input type="text" name="product<?= $i ?>" 
                                           <?= $i === 1 ? 'required' : '' ?>
                                           value="<?= isset($_POST["product$i"]) ? htmlspecialchars($_POST["product$i"]) : '' ?>">
                                </td>
                                <td>
                                    <input type="date" name="date<?= $i ?>" 
                                           <?= $i === 1 ? 'required' : '' ?>
                                           value="<?= isset($_POST["date$i"]) ? htmlspecialchars($_POST["date$i"]) : '' ?>">
                                </td>
                            </tr>
                            <?php endfor; ?>
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