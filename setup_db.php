<?php
try {
    $db = new SQLite3('inventory.db');

    $createTableSQL = '
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            username TEXT NOT NULL,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT "user"
        )
    ';
    $db->exec($createTableSQL);

    $email = 'admin@example.com';
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $role = 'admin';

    $stmt = $db->prepare('SELECT COUNT(*) as count FROM users WHERE email = :email');
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if ($result['count'] == 0) {
        $insertSQL = 'INSERT INTO users (email, username, password, role) VALUES (:email, :username, :password, :role)';
        $insertStmt = $db->prepare($insertSQL);
        $insertStmt->bindValue(':email', $email, SQLITE3_TEXT);
        $insertStmt->bindValue(':username', $username, SQLITE3_TEXT);
        $insertStmt->bindValue(':password', $password, SQLITE3_TEXT);
        $insertStmt->bindValue(':role', $role, SQLITE3_TEXT);
        $insertStmt->execute();
    }
} catch (Exception $e) {
}
?>
