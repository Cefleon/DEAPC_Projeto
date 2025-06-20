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
    header('Location: /public_html/encomendas.html?status=success');
    exit;
}

else {
    // Se acessado diretamente, redirecionar
    header('Location: /public_html/encomendas.html');
    exit;
}
?>
