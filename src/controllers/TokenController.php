<?php
namespace src\controllers;

use League\OAuth2\Client\Token\AccessToken;
use src\Database;
use src\models\TokenModel;

class TokenController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    // Метод для записи access_token и refresh_token
    public function saveTokens($accessToken)
    {
        $query = 'INSERT INTO tokens (access_token, refresh_token, expires_in, created_at) 
                  VALUES (:access_token, :refresh_token, :expires_in, NOW()) 
                  ON DUPLICATE KEY UPDATE access_token = :access_token, refresh_token = :refresh_token, expires_in = :expires_in';

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':access_token', $accessToken->getToken());
        $stmt->bindParam(':refresh_token', $accessToken->getRefreshToken());
        $stmt->bindParam(':expires_in', $accessToken->getExpires());

        try {
            $stmt->execute();
        } catch (\PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }

    public function getToken()
    {
        $query = 'SELECT access_token, refresh_token, expires_in FROM tokens ORDER BY created_at DESC LIMIT 1';
        $stmt = $this->pdo->query($query);

        if ($stmt->rowCount() > 0) {
            $data = $stmt->fetch();
            return new AccessToken($data);
        }
        return null;
    }
}
