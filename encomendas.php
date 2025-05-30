<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexão com o banco de dados
$db_pedidos = new SQLite3('/var/www/html/public_html/data/pedidos.db');
$db_encomendas = new SQLite3('/var/www/html/public_html/data/encomendas.db');

// Processar pesquisa por empresa
$encomendas = [];
$empresa_pesquisada = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['company'])) {
    $empresa_pesquisada = SQLite3::escapeString($_GET['company']);
    $result = $db_pedidos->query("SELECT * FROM ListaEncomenda WHERE Companhia LIKE '%$empresa_pesquisada%'");
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $encomendas[] = $row;
    }
}

// Processar atualização de encomendas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['encomenda'])) {
        
        // Inserir encomendas selecionadas
        $stmt = $db_encomendas->prepare("
            INSERT INTO AtualizaEncomenda 
            (Tipo, Quantidade, Dia)
            VALUES (:tipo, :quantidade, :dia)
        ");
        
        // Verificar cada checkbox
        foreach ($_POST['tipo'] as $index => $tipo) {
            // Verifica se o checkbox correspondente foi marcado
            if (isset($_POST['encomenda'][$index]) && $_POST['encomenda'][$index] === 'on') {
                $stmt->bindValue(':tipo', $tipo, SQLITE3_TEXT);
                $stmt->bindValue(':quantidade', $_POST['quantidade'][$index], SQLITE3_INTEGER);
                $stmt->bindValue(':dia', $_POST['dia'][$index], SQLITE3_TEXT);
                
                if (!$stmt->execute()) {
                    die("Erro ao inserir encomenda: " . $db_encomendas->lastErrorMsg());
                }
            }
        }
        
        header('Location: encomendas.php?status=success&company='.urlencode($_GET['company']));
        exit;
    }
}
?>

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
		    <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="stock.php"><i class="fas fa-boxes"></i> Stock</a></li>
		    <li class="active"><a href="#"><i class="fas fa-truck"></i> Encomendas</a></li>
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
        <h2><i class="fas fa-check-circle"></i> Atualizar Encomendas</h2>
            <form action="encomendas.php" method="get" class="delivery-form">
                <div class="form-group">
                    <label for="company"><i class="fas fa-building"></i> Nome da Companhia</label>
                    <input type="text" id="company" name="company" value="<?= htmlspecialchars ($empresa_pesquisada)?>" required>
                    <div class="form-footer">
                      <button type="submit" class="submit-btn">
                        <i class="fas fa-check"></i> Pesquisa
                      </button>
                    </div>
                </div>
            </form>
            <!-- Seção Tabela -->
            <?php if(!empty($encomendas)): ?>
            <form action="encomendas.php" method="post" id="encomendasForm">
            <section class="report-section">
                <h2><i class="fas fa-table"></i> Encomendas <?= htmlspecialchars($empresa_pesquisada)?></h2>
                <div class="table-container">
                <table>
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
    <td><?= htmlspecialchars($encomenda['Tipo']) ?>
        <input type="hidden" name="tipo[<?= $index ?>]" value="<?= htmlspecialchars($encomenda['Tipo']) ?>">
    </td>
    <td><?= htmlspecialchars($encomenda['Quantidade']) ?>
        <input type="hidden" name="quantidade[<?= $index ?>]" value="<?= htmlspecialchars($encomenda['Quantidade']) ?>">
    </td>
    <td><?= htmlspecialchars($encomenda['Dia']) ?>
        <input type="hidden" name="dia[<?= $index ?>]" value="<?= htmlspecialchars($encomenda['Dia']) ?>">
    </td>
    <td class="checkbox-cell">
        <input type="checkbox" name="encomenda[<?= $index ?>]" value="on" class="confirmar-encomenda">
    </td>
</tr>
<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
	   <div id="data-hora">20-05-2025, 17:33:48 Sex
           </div>
           <div class="form-footer">
              <button type="submit" class="submit-btn">
                 <i class="fas fa-check"></i> Atualizar
              </button>
           </div>
         </form>
           <?php elseif (isset($_GET['company'])): ?>
                <p class="no-results">Nenhuma encomenda encontrada para "<?= htmlspecialchars($_GET['company']) ?>"</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
