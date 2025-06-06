<?php
try {
    $db = new PDO('sqlite:/var/www/html/database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conexão bem-sucedida!<br>";
    
    $stmt = $db->query("SELECT * FROM users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>

