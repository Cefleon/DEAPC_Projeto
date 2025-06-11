<?php

// Informação sobre erros na execução do script

  error_reporting(E_ALL);
  ini_set('display_errors' , 1);

// Método de request e leitura de dados da página stock
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  try{
    // Conexão com o SQLite
    $database = new SQLite3('/var/www/html/public_html/data/stock.db');
    
    // Verifica se a conexão foi estabelecida
    if (!$database) {
        die("Erro ao conectar ao banco de dados. ");
    }

    // Cria a tabela se não existir
    $createTable = "CREATE TABLE IF NOT EXISTS AtualizaStock (
                    Produto TEXT PRIMARY KEY,
                    Quantidade INTEGER NOT NULL
                    )";
                    
    if (!$database->exec($createTable)) {
    	die("Erro ao criar tabela: " . $database->lastErrorMsg());
    }
    
    // Processamento dos dados (versão simplificada e segura)
        $produtos = [
             'Carnes' => isset($_POST['Carnes']) ? max(0, intval($_POST['Carnes'])) : 0,
            'Peixes' => isset($_POST['Peixes']) ? max(0, intval($_POST['Peixes'])) : 0,
            'Frutas' => isset($_POST['Frutas']) ? max(0, intval($_POST['Frutas'])) : 0,
            'Congelados' => isset($_POST['Congelados']) ? max(0, intval($_POST['Congelados'])) : 0
            ];

        // Operação de atualização
        $stmt = $database->prepare("
            INSERT OR REPLACE INTO AtualizaStock (Produto, Quantidade)
            VALUES (:produto, :quantidade)
        ");
        
        foreach ($produtos as $produto => $quantidade) {
            $stmt->bindValue(':produto', $produto, SQLITE3_TEXT);
            $stmt->bindValue(':quantidade', $quantidade, SQLITE3_INTEGER);
            if (!$stmt->execute()) {
                throw new Exception("Erro ao atualizar stock para $produto.");
            }
        }

        header('Location: stock.php?status=success');
        exit;
        
  } catch (Exception $e) {
        die("Erro no sistema: " . $e->getMessage());
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
                    <li class="active"><a href="#"><i class="fas fa-boxes"></i> Stock</a></li>
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
                <h2><i class="fas fa-table"></i> Atualização de stock</h2>
                <form action="stock.php" method="post" id="stockForm">
		  <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Carnes</td>
				<td><input type="number" name="Carnes" value="0" min="0"></td>
			    </tr>
			    <tr>
                                <td>Peixes</td>
				<td><input type="number" name="Peixes" value="0" min="0"></td>
			    </tr>
                            <tr>    
				<td>Frutas</td>
				<td><input type="number" name="Frutas" value="0" min="0"></td>
			    </tr>
			    <tr>
                                <td>Congelados</td>
				<td><input type="number" name="Congelados" value="0" min="0"></td>
                            </tr>
                        </tbody>
                    </table>
                  </div>
            </section>
	  <div id="data-hora"><?= date('d-m-Y, H:i:s') ?>
	  </div>
	  <div class="form-footer">
	     <button type="submit" class="submit-btn">
                <i class="fas fa-check"></i> Atualizar
             </button>
          </div>
	</form>
        </main>
    </div>
    
    	<!-- Pop-up de Confirmação -->
	<div id="confirmationPopup" class="popup-overlay" style="display: none;">
    		<div class="popup-content">
        		<h2>Atenção</h2>
        		<p>Quer efetuar a atualização de stock?</p>
        	<div class="popup-buttons">
            		<button id="confirmYes" class="popup-btn yes-btn">Sim</button>
            		<button id="confirmNo" class="popup-btn no-btn">Não</button>
        	</div>
    		</div>
     	</div>
     	
     	<script type="text/javascript" src="scripts/stock.js"></script>
</body>
</html>
