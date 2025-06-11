<?php
session_start();
require_once 'config.php'; 

header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
    
    // Verifica a última entrega vista pelo utilizador
    $lastSeen = $_SESSION['last_delivery_seen'] ?? 0;
    
    // Conta entregas mais recentes que a última vista
    $stmt = $db->prepare("SELECT COUNT(*) FROM deliveries 
                         WHERE user_id = ? AND id > ?");
    $stmt->execute([$_SESSION['user']['id'], $lastSeen]);
    $count = $stmt->fetchColumn();
    
    // Atualiza a última entrega vista se houver novas
    if ($count > 0) {
        $stmt = $db->prepare("SELECT MAX(id) FROM deliveries WHERE user_id = ?");
        $stmt->execute([$_SESSION['user']['id']]);
        $_SESSION['last_delivery_seen'] = $stmt->fetchColumn();
    }
    
    echo json_encode(['count' => $count]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}