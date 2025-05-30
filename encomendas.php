<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processar encomendas selecionadas
    $encomendas = [];

    // Verificar quais encomendas foram selecionadas
    if (isset($_POST['encomenda'])) {
        foreach ($_POST['encomenda'] as $index => $selected) {
            if ($selected === 'on') {  // Checkbox marcado
                $tipo = $_POST['tipo'][$index] ?? 'Desconhecido';
                $quantidade = $_POST['quantidade'][$index] ?? 0;
                $dia = $_POST['dia'][$index] ?? 'Data não especificada';

                $encomendas[] = [
                    'tipo' => $tipo,
                    'quantidade' => $quantidade,
                    'dia' => $dia
                ];
            }
        }
    }

    // Criar conteúdo do arquivo
    $dados = "=== ENCOMENDAS ATUALIZADAS ===\n";
    $dados .= "Data da atualização: " . date('d-m-Y H:i:s') . "\n\n";

    foreach ($encomendas as $encomenda) {
        $dados .= "Tipo: " . $encomenda['tipo'] . "\n";
        $dados .= "Quantidade: " . $encomenda['quantidade'] . "\n";
        $dados .= "Dia: " . $encomenda['dia'] . "\n";
        $dados .= "--------------------------\n";
    }

    $dados .= "=======================\n\n";

    // Caminho do arquivo
    $arquivo = '/var/www/html/public_html/data/encomendas.txt';

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

    // Redirecionar com mensagem de sucesso
    header('Location: /public_html/encomendas.php?status=success');
    exit;
}

else {
    // Se acessado diretamente, redirecionar
    //header('Location: /public_html/encomendas.php');
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
                    <input type="text" id="company" name="company" required>
                    <div class="form-footer">
                      <button type="submit" class="submit-btn">
                        <i class="fas fa-check"></i> Pesquisa
                      </button>
                    </div>
                </div>
            </form>
            <!-- Seção Tabela -->
            <form action="encomendas.php" method="post" id="encomendasForm">
            <section class="report-section">
                <h2><i class="fas fa-table"></i> Atualização de Encomendas</h2>
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
                          <tr>
                            <td>Tazos
				                      <input type="hidden" name="tipo[]" value="Tazos">
				                    </td>
				                    <td>127
				                      <input type="hidden" name="quantidade[]" value="127">
				                    </td>
				                    <td>03-12-2008
				                      <input type="hidden" name="dia[]" value="03-12-2008">
				                    </td>
				                    <td class="checkbox-cell">
				                      <input type="checkbox" name="encomenda[]" class="confirmar-encomenda">
				                    </td>
			                    </tr>
			                    <tr>
                            <td>Chinelos
				                      <input type="hidden" name="tipo[]" value="Chinelos">
				                    </td>
                            <td>56
				                      <input type="hidden" name="quantidade[]" value="56">
				                    </td>
                            <td>24-05-2015
				                      <input type="hidden" name="dia[]" value="24-05-2015">
				                    </td>
                            <td class="checkbox-cell">
				                      <input type="checkbox" name="encomenda[]" class="confirmar-encomenda">
				                    </td>
			                    </tr>
                          <tr>    
				                    <td>Carapins
				                      <input type="hidden" name="tipo[]" value="Carapins">
				                    </td>
                            <td>25
				                      <input type="hidden" name="quantidade[]" value="25">
				                    </td>
                            <td>13-05-1997
				                      <input type="hidden" name="dia[]" value="13-05-1997">
				                    </td>
                            <td class="checkbox-cell">
				                      <input type="checkbox"name="encomenda[]" class="confirmar-encomenda">
				                    </td>
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
