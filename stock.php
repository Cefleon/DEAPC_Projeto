<?php

// Informação sobre erros na execução do script

  error_reporting(E_ALL);
  ini_set('display_errors' , 1);

// Método de request e leitura de dados da página stock

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $carnes = isset($_POST['Carnes']);
	if ($carnes != NULL){
	  $carnes = intval($_POST['Carnes']);
	}
	else{
	  $carnes = 0;
	}

    $peixes = isset($_POST['Peixes']);
	if ($peixes != NULL){
          $peixes = intval($_POST['Peixes']);
        }
        else{
          $peixes = 0;
        }

    $frutas = isset($_POST['Frutas']);
	if ($frutas != NULL){
          $frutas = intval($_POST['Frutas']);
        }
        else{
          $frutas = 0;
        }

    $congelados = isset($_POST['Congelados']);
	if ($congelados != NULL){
          $congelados = intval($_POST['Congelados']);
        }
        else{
          $congelados = 0;
        }

    // Validação de quantidades positivas
    if ($carnes < 0 || $peixes < 0 || $frutas < 0 || $congelados < 0) {
        die("Erro: Quantidades não podem ser negativas.");
    }

    // Criação de arquivo TXT
    $dados = "=== STOCK ATUALIZADO ===\n";
    $dados .= "Data: " . date('d-m-Y H:i:s') . "\n\n";
    $dados .= "Carnes: $carnes\n";
    $dados .= "Peixes: $peixes\n";
    $dados .= "Frutas: $frutas\n";
    $dados .= "Congelados: $congelados\n";
    $dados .= "=======================\n\n";

    // Caminho do arquivo
    $arquivo = '/var/www/html/public_html/data/stock.txt';

    // Verifica se ficheiro existe ou precisa ser criado
    if (file_exists($arquivo)){
	if (($file = fopen($arquivo, "a")) == NULL){
	   printf("Erro na abertura do ficheiro!!\n");
	   return 1;
	}
    }
    else{
	if(($file = fopen($arquivo, "w")) == NULL){
	   printf("Erro na criação do ficheiro!!\n");
           return 1;
	}
    }

    if (fwrite($file, $dados) == NULL){
	fclose($file);
	printf("Erro na escrita do ficheiro!!\n");
        return 1;
    }

    fclose($file);

    // Redireciona com mensagem de sucesso
    header('Location: /public_html/stock.php?status=success');
    exit;
}
else {
    // Se não for POST, redireciona
    //header('Location: /public_html/stock.php');
    //exit;
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
				<td><input type="number" name="Carnes" value="40" min="0"></td>
			    </tr>
			    <tr>
                                <td>Peixes</td>
				<td><input type="number" name="Peixes" value="4" min="0"></td>
			    </tr>
                            <tr>    
				<td>Frutas</td>
				<td><input type="number" name="Frutas" value="90" min="0"></td>
			    </tr>
			    <tr>
                                <td>Congelados</td>
				<td><input type="number" name="Congelados" value="258" min="0"></td>
                            </tr>
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
        </main>
    </div>
</body>
</html>
