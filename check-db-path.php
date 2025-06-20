<?php

$db_path = __DIR__ . '/inventory.db';
$db = new SQLite3($db_path);

$result = $db->exec("UPDATE users SET role = 'Fornecedor' WHERE id = 3");

if ($result) {
    echo "Atualização bem-sucedida";
} else {
    echo "Erro: " . $db->lastErrorMsg();
}

/*$db_path = __DIR__ . '/inventory.db';  // Definir o caminho correto

echo "Tentando abrir DB em: $db_path\n";

if (file_exists($db_path)) {
    echo "Ficheiro existe.\n";
} else {
    echo "Ficheiro NÃO existe!\n";
    exit; // Parar execução porque o ficheiro não existe
}

$db = new SQLite3($db_path);

echo "Permissões do ficheiro:\n";
$perms = fileperms($db_path);
echo substr(sprintf('%o', $perms), -4) . "\n";*/
?>