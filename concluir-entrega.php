<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$db = new SQLite3('/var/www/html/public_html/inventory.db');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Buscar categorias da BD
$categories = [];
$catResult = $db->query("SELECT id, nome FROM Categorias");
while ($row = $catResult->fetchArray(SQLITE3_ASSOC)) {
    $categories[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company = $_POST['company'] ?? '';
    $user_id = $_SESSION['user_id'];
    $products = [];

    for ($i = 1; $i <= 2; $i++) {
        $product = trim($_POST["product$i"] ?? '');
        $date = $_POST["date$i"] ?? '';
        $type = $_POST["type$i"] ?? '';
        $quantity = intval($_POST["quantity$i"] ?? 0);

        if (!empty($product) && !empty($date) && !empty($type) && $quantity > 0) {
            $products[] = [
                'name' => $product,
                'date' => $date,
                'type' => $type,
                'quantity' => $quantity
            ];
        }
    }

    if (!empty($company) && count($products) > 0) {
        try {
            $db->exec('BEGIN');

            $stmt = $db->prepare("INSERT INTO deliveries (company_name, user_id, delivery_date, status) VALUES (:company, :user_id, :delivery_date, 'completed')");
            $stmt->bindValue(':company', $company, SQLITE3_TEXT);
            $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            $stmt->bindValue(':delivery_date', $products[0]['date'], SQLITE3_TEXT);
            $stmt->execute();

            $delivery_id = $db->lastInsertRowID();

            foreach ($products as $prod) {
                // Exemplo: inserir na tabela delivery_products (ajuste conforme sua estrutura)
                $stmtProd = $db->prepare("INSERT INTO delivery_products (delivery_id, product_name, product_type, quantity, delivery_date) VALUES (:delivery_id, :product, :type, :quantity, :date)");
                $stmtProd->bindValue(':delivery_id', $delivery_id, SQLITE3_INTEGER);
                $stmtProd->bindValue(':product', $prod['name'], SQLITE3_TEXT);
                $stmtProd->bindValue(':type', $prod['type'], SQLITE3_TEXT);
                $stmtProd->bindValue(':quantity', $prod['quantity'], SQLITE3_INTEGER);
                $stmtProd->bindValue(':date', $prod['date'], SQLITE3_TEXT);
                $stmtProd->execute();
            }

            $db->exec('COMMIT');
            $success_message = "Entrega para $company registrada com sucesso!";
        } catch (Exception $e) {
            $db->exec('ROLLBACK');
            $error_message = "Erro ao registrar entrega: " . $e->getMessage();
        }
    } else {
        $error_message = "Por favor, preencha todos os campos obrigatórios.";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="dashboard-container">

        <?php include 'header.php'; ?>

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
                                <th>Tipo</th>
                                <th>Quantidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 1; $i <= 2; $i++): ?>
                                <tr>
                                    <td>
                                        <input type="text" name="product<?php echo $i; ?>" <?php echo $i == 1 ? 'required' : ''; ?> value="<?php echo isset($products[$i - 1]['name']) ? htmlspecialchars($products[$i - 1]['name']) : ''; ?>">
                                    </td>
                                    <td>
                                        <input type="date" name="date<?php echo $i; ?>" <?php echo $i == 1 ? 'required' : ''; ?> value="<?php echo isset($products[$i - 1]['date']) ? htmlspecialchars($products[$i - 1]['date']) : ''; ?>">
                                    </td>
                                    <td>
                                        <select class="form-select" name="type<?php echo $i; ?>" <?php echo $i == 1 ? 'required' : ''; ?>>
                                            <option value="">Selecione o tipo</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo htmlspecialchars($cat['nome']); ?>"
                                                    <?php echo (isset($products[$i - 1]['type']) && $products[$i - 1]['type'] == $cat['nome']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat['nome']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="quantity<?php echo $i; ?>" min="1" <?php echo $i == 1 ? 'required' : ''; ?> value="<?php echo isset($products[$i - 1]['quantity']) ? htmlspecialchars($products[$i - 1]['quantity']) : ''; ?>">
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