<?php

// Informação sobre erros na execução do script

  error_reporting(E_ALL);
  ini_set('display_errors' , 1);

// Método de request e leitura de dados da página stock

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $carnes = isset($_POST['Carnes']);
	if ($carnes != 0){
	  $carnes = intval($_POST['Carnes']);
	}
	else{
	  $carnes = 0;
	}

    $peixes = isset($_POST['Peixes']);
	if ($peixes != 0){
          $peixes = intval($_POST['Peixes']);
        }
        else{
          $peixes = 0;
        }

    $frutas = isset($_POST['Frutas']);
	if ($frutas != 0){
          $frutas = intval($_POST['Frutas']);
        }
        else{
          $frutas = 0;
        }

    $congelados = isset($_POST['Congelados']);
	if ($congelados != 0){
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
    header('Location: /public_html/stock.html?status=success');
    exit;
}
else {
    // Se não for POST, redireciona
    header('Location: /public_html/stock.html');
    exit;
}
?>

