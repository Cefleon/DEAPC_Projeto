<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = new SQLite3(__DIR__ . '/inventory.db');

$empresa_pesquisada = '';
$encomendas = [];
$entregas = [];

// Processar pesquisa por companhia
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['company'])) {
    $empresa_pesquisada = trim($_GET['company']);


    // Buscar encomendas da companhia pesquisada
    $stmt = $db->prepare("SELECT * FROM deliveries WHERE company_name LIKE :companhia ORDER BY delivery_date DESC");
    $stmt->bindValue(':companhia', "%$empresa_pesquisada%", SQLITE3_TEXT);
    $result = $stmt->execute();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        // Mapear os campos para os nomes esperados no HTML
        $encomendas[] = [
            'Companhia' => $row['company_name'],
            'Tipo' => $row['Tipo'],
            'Quantidade' => $row['quantity'],
            'Dia' => $row['delivery_date']
        ];
    }
}

// Processar atualização de encomendas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['encomenda'])) {
    $stmt = $db->prepare("
        INSERT INTO AtualizaEncomenda (Companhia, Tipo, Quantidade, Dia)
        VALUES (:companhia, :tipo, :quantidade, :dia)
    ");
    foreach ($_POST['tipo'] as $index => $tipo) {
        if (isset($_POST['encomenda'][$index]) && $_POST['encomenda'][$index] === 'on') {
            $stmt->bindValue(':companhia', $_POST['companhia'][$index], SQLITE3_TEXT);
            $stmt->bindValue(':tipo', $tipo, SQLITE3_TEXT);
            $stmt->bindValue(':quantidade', $_POST['quantidade'][$index], SQLITE3_INTEGER);
            $stmt->bindValue(':dia', $_POST['dia'][$index], SQLITE3_TEXT);
            $stmt->execute();
        }
    }
    header('Location: encomendas.php?status=success&company=' . urlencode($_POST['companhia'][0]));
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestão de Inventário</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/dashboard.css">
    <link rel="stylesheet" href="styles/encomendas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <?php include 'header.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <h2><i class="fas fa-check-circle"></i> Atualizar Encomendas</h2>
            <!-- Formulário de pesquisa -->
            <form action="encomendas.php" method="get" class="delivery-form">
                <div class="form-group">
                    <label for="company"><i class="fas fa-building"></i> Nome da Companhia</label>
                    <input type="text" id="company" name="company" value="<?= htmlspecialchars($empresa_pesquisada) ?>" required>
                    <div class="form-footer">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-search"></i> Pesquisar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Listagem de encomendas -->
            <?php if (!empty($encomendas)): ?>
                <form action="encomendas.php" method="post" id="encomendasForm">
                    <input type="hidden" name="company" value="<?= htmlspecialchars($empresa_pesquisada) ?>">
                    <section class="report-section">
                        <h2><i class="fas fa-table"></i> Encomendas de <?= htmlspecialchars($empresa_pesquisada) ?></h2>
                        <div class="table-container">
                            <table class="encomendas-table">
                                <thead>
                                    <tr>
                                        <th>Tipo de Encomenda</th>
                                        <th>Quantidade</th>
                                        <th>Dia</th>
                                        <th>Selecionar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($encomendas as $index => $encomenda): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($encomenda['Tipo']) ?>
                                                <input type="hidden" name="tipo[<?= $index ?>]" value="<?= htmlspecialchars($encomenda['Tipo']) ?>">
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($encomenda['Quantidade']) ?>
                                                <input type="hidden" name="quantidade[<?= $index ?>]" value="<?= htmlspecialchars($encomenda['Quantidade']) ?>">
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($encomenda['Dia']) ?>
                                                <input type="hidden" name="dia[<?= $index ?>]" value="<?= htmlspecialchars($encomenda['Dia']) ?>">
                                                <input type="hidden" name="companhia[<?= $index ?>]" value="<?= htmlspecialchars($encomenda['Companhia']) ?>">
                                            </td>
                                            <td class="checkbox-cell">
                                                <input type="checkbox" name="encomenda[<?= $index ?>]" value="on">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                    <div class="form-footer">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-check"></i> Atualizar
                        </button>
                    </div>
                    <div id="data-hora"><?= date('d-m-Y, H:i:s') ?></div>
                </form>
            <?php elseif (isset($_GET['company'])): ?>
                <section class="report-section">
                    <h2><i class="fas fa-table"></i> Encomendas</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="no-results">Nenhuma encomenda encontrada para "<?= htmlspecialchars($_GET['company']) ?>"</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Mensagem de sucesso -->
            <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
                <div class="alert alert-success" id="successMsg">
                    Encomenda(s) atualizada(s) com sucesso!
                </div>
            <?php endif; ?>        
        </main>
    </div>

    <!-- Pop-up de Confirmação -->
    <div id="confirmationPopup" class="popup-overlay" style="display: none;">
        <div class="popup-content">
            <h2>Atenção</h2>
            <p>Quer efetuar a atualização das encomendas?</p>
            <div class="popup-buttons">
                <button id="confirmYes" class="popup-btn yes-btn">Sim</button>
                <button id="confirmNo" class="popup-btn no-btn">Não</button>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="scripts/func_encomendas.js"></script>
</body>

</html>