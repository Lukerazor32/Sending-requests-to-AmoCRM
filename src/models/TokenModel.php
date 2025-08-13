<?php
namespace src\models;

class TokenModel
{
    private $accessToken;
    private $refreshToken;

    public function __construct($accessToken, $refreshToken)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }

    public static function fromArray(array $data)
    {
        return new self($data['access_token'], $data['refresh_token']);
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }
}
