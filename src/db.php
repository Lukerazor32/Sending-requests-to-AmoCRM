<?php
namespace src;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = getenv('MYSQL_HOST') ?: 'localhost';
        $port = getenv('MYSQL_PORT') ?: '3333';
        $db   = getenv('MYSQL_DATABASE') ?: 'amocrm';
        $user = getenv('MYSQL_USER') ?: 'root';
        $pass = getenv('MYSQL_PASSWORD') ?: 'example';

        // Устанавливаем соединение
        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";
        try {
            $this->pdo = new \PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Возвращаем экземпляр подключения
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }

    // Запрещаем клонирование объекта
    private function __clone() {}

    // Запрещаем сериализацию
    private function __wakeup() {}
}