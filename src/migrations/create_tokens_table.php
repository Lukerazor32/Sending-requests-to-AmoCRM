<?php

function createTokensTable(PDO $pdo) {
    $query = "
    CREATE TABLE IF NOT EXISTS tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        access_token TEXT NOT NULL,
        refresh_token TEXT NOT NULL,
        expires_in INT NOT NULL, 
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";

    // Выполняем запрос
    $pdo->exec($query);
    echo "Таблица 'tokens' проверена и создана, если не существовала.\n";
}
