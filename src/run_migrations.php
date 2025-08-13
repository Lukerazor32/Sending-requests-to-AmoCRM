<?php

use src\Database;
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/migrations/create_tokens_table.php';

error_log("Migration started");
$pdo = Database::getInstance();

createTokensTable($pdo);
