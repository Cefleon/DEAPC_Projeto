<?php
session_start();

// Verifica se está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Mostrar erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexão com a base de dados
$db = new SQLite3('/var/www/html/public_html/inventory.db');

// Obter entregas confirmadas
$entregas = [];
try {
    $query = "
        SELECT company_name, delivery_date, status FROM deliveries
        WHERE status = 'completed'
        ORDER BY delivery_date DESC
    ";
    $result = $db->query($query);
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $entregas[] = $row;
    }
} catch (Exception $e) {
    error_log("Erro ao obter entregas: " . $e->getMessage());
}

$categorias = [];
$catResult = $db->query("SELECT nome FROM Categorias ORDER BY nome ASC");
while ($row = $catResult->fetchArray(SQLITE3_ASSOC)) {
    $categorias[] = $row['nome'];
}

// Processamento do formulário de stock
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $createTable = "CREATE TABLE IF NOT EXISTS AtualizaStock (
            Produto TEXT PRIMARY KEY,
            Quantidade INTEGER NOT NULL
        )";

        if (!$db->exec($createTable)) {
            die("Erro ao criar tabela: " . $db->lastErrorMsg());
        }

        $produtos = [];
        foreach ($categorias as $produto) {
            $produtos[$produto] = isset($_POST[$produto]) ? max(0, intval($_POST[$produto])) : 0;
        }

        /*foreach ($produtos as $produto => $quantidade) {
            if ($quantidade > 0) {
                $existing = $db->querySingle("SELECT Quantidade FROM AtualizaStock WHERE Produto = :produto", true);
                if ($existing) {
                    $novaquantidade = $existing['Quantidade'] + $quantidade;
                    $stmt = $db->prepare("UPDATE AtualizaStock SET Quantidade = :quantidade WHERE Produto = :produto");
                    $stmt->bindValue(':quantidade', $novaquantidade, SQLITE3_INTEGER);
                } else {
                    $stmt = $db->prepare("INSERT INTO AtualizaStock (Produto, Quantidade) VALUES (:produto, :quantidade)");
                    $stmt->bindValue(':quantidade', $quantidade, SQLITE3_INTEGER);
                }
                $stmt->bindValue(':produto', $produto, SQLITE3_TEXT);
                if (!$stmt->execute()) {
                    throw new Exception("Erro ao atualizar stock para $produto.");
                }
            }
        }*/
        date_default_timezone_set('Europe/Lisbon');
        $agora = date('Y-m-d H:i:s');

        foreach ($produtos as $produto => $quantidade) {
            if ($quantidade > 0) {
                // Verifica se já existe o produto na tabela
                $checkStmt = $db->prepare("SELECT Quantidade FROM AtualizaStock WHERE Produto = :produto");
                $checkStmt->bindValue(':produto', $produto, SQLITE3_TEXT);
                $result = $checkStmt->execute();
                $existing = $result->fetchArray(SQLITE3_ASSOC);

                if ($existing) {
                    $novaquantidade = $existing['Quantidade'] + $quantidade;
                    $stmt = $db->prepare("UPDATE AtualizaStock SET Quantidade = :quantidade, Data_Atualização = :data WHERE Produto = :produto");
                    $stmt->bindValue(':quantidade', $novaquantidade, SQLITE3_INTEGER);
                } else {
                    $stmt = $db->prepare("INSERT INTO AtualizaStock (Produto, Quantidade, Data_Atualização) VALUES (:produto, :quantidade, :data)");
                    $stmt->bindValue(':quantidade', $quantidade, SQLITE3_INTEGER);
                }

                $stmt->bindValue(':produto', $produto, SQLITE3_TEXT);
                $stmt->bindValue(':data', $agora, SQLITE3_TEXT);

                if (!$stmt->execute()) {
                    throw new Exception("Erro ao atualizar stock para $produto.");
                }
            }
        }

        header('Location: stock.php?status=success');
        exit;
    } catch (Exception $e) {
        die("Erro no sistema: " . $e->getMessage());
    }
}

// Obter entregas confirmadas

$entregas = [];
try {
    $query = "
        SELECT product_name, product_type, delivery_date, quantity
        FROM delivery_products
        ORDER BY delivery_date DESC
    ";
    $result = $db->query($query);
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $entregas[] = $row;
    }
} catch (Exception $e) {
    error_log("Erro ao obter entregas: " . $e->getMessage());
}



$stock_atual = [];
foreach ($categorias as $produto) {
    $stmt = $db->prepare("SELECT Quantidade, Data_Atualização FROM AtualizaStock WHERE Produto = :produto");
    $stmt->bindValue(':produto', $produto, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    $stock_atual[$produto] = $row ? $row['Quantidade'] : 0;
}

foreach ($categorias as $produto) {
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM AtualizaStock WHERE Produto = :produto");
    $stmt->bindValue(':produto', $produto, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    if ($row['total'] == 0) {
        // Inicializa o stock a zero para novas categorias
        $insert = $db->prepare("INSERT INTO AtualizaStock (Produto, Quantidade, Data_Atualização) VALUES (:produto, 0, :data)");
        $insert->bindValue(':produto', $produto, SQLITE3_TEXT);
        $insert->bindValue(':data', date('Y-m-d H:i:s'), SQLITE3_TEXT);
        $insert->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Gestão de Inventário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/dashboard.css">
    <link rel="stylesheet" href="styles/stock.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <?php include 'header.php'; ?>

        <main class="main-content">
            <!-- Atualização de Stock -->
            <section class="report-section">
                <h2><i class="fas fa-table"></i> Atualização de stock</h2>
                <form action="stock.php" method="post" id="stockForm">
                    <div class="table-scroll-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Stock Atual</th>
                                    <th>Nova Quantidade</th>
                                    <th>Data de Atualização</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($categorias as $produto) {
                                    $atual = htmlspecialchars($stock_atual[$produto]);
                                    // Buscar a data de atualização correta para cada produto
                                    $stmt = $db->prepare("SELECT Data_Atualização FROM AtualizaStock WHERE Produto = :produto");
                                    $stmt->bindValue(':produto', $produto, SQLITE3_TEXT);
                                    $result = $stmt->execute();
                                    $row = $result->fetchArray(SQLITE3_ASSOC);
                                    $dataatualizacao = $row && !empty($row['Data_Atualização']) ? htmlspecialchars($row['Data_Atualização']) : 'Nunca atualizado';

                                    echo "<tr>
                                    <td>$produto</td>
                                    <td>$atual</td>
                                    <td><input type='number' name='" . htmlspecialchars($produto, ENT_QUOTES) . "' value='' min='0'></td>
                                    <td>$dataatualizacao</td>
                                </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="data-hora"><?= date('d-m-Y, H:i:s') ?></div>
                    <div class="form-footer">
                        <button class="submit-btn">Atualizar</button>
                    </div>
                </form>
            </section>

            <!-- Entregas Confirmadas -->
            <section class="report-section">
                <h2><i class="fas fa-truck"></i> Entregas Concluídas</h2>
                <div class="table-scroll-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Tipo</th>
                                <th>Data de Entrega</th>
                                <th>Quantidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($entregas)): ?>
                                <?php foreach ($entregas as $entrega): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($entrega['product_name'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($entrega['product_type'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($entrega['delivery_date'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($entrega['quantity'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="no-results">Nenhuma entrega confirmada encontrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script type="text/javascript" src="scripts/stock.js"></script>

    <div id="confirmationPopup" class="popup-overlay" style="display: none;">
        <div class="popup-content">
            <h2>Confirmar Atualização</h2>
            <p>Tem a certeza que deseja atualizar o stock?</p>
            <div class="popup-buttons">
                <button id="confirmYes" class="popup-btn yes-btn">Sim</button>
                <button id="confirmNo" class="popup-btn no-btn">Não</button>
            </div>
        </div>
    </div>


</body>

</html>