<?php
namespace src\controllers;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiConnectExceptionException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;

class AmoCRMApiClientWrapper
{
    private $apiClient;
    private $tokenController;
    private $oAuthConfig;

    public function __construct(AmoCRMApiClient $apiClient, $oAuthConfig)
    {
        $this->apiClient = $apiClient;
        $this->tokenController = new TokenController();;
        $this->oAuthConfig = $oAuthConfig;
    }

    /**
     * Проверка и обновление токена перед выполнением запроса
     *
     * @throws AmoCRMMissedTokenException
     */
    private function checkAndUpdateAccessToken(): void
    {
        $accessToken = $this->apiClient->isAccessTokenSet();

        if (!$accessToken) {
            $accessToken = $this->tokenController->getToken();
        } else {
            return;
        }
        $this->apiClient->setAccountBaseDomain($this->oAuthConfig->getBaseDomain());
        // Проверка токена
        if ($accessToken) {
            if ($accessToken->hasExpired()) {
                $accessToken = $this->apiClient->getOAuthClient()->getAccessTokenByRefreshToken($accessToken->getRefreshToken());
                $this->tokenController->saveTokens($accessToken);
            }
        } else {
            $accessToken = $this->apiClient->getOAuthClient()->getAccessTokenByCode($this->oAuthConfig->getAuthCode());
            $this->tokenController->saveTokens($accessToken);
        }
        $this->apiClient->setAccessToken($accessToken);
    }

    /**
     * Метод, который будет проксировать запросы к реальному API-клиенту
     * и проверять токен перед выполнением
     *
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws AmoCRMMissedTokenException
     */
    public function __call($method, $params)
    {
        // Проверяем токен перед выполнением запроса
        $this->checkAndUpdateAccessToken();

        $maxRetries = 3;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                // Вызов метода в реальном API-клиенте
                return call_user_func_array([$this->apiClient, $method], $params);
            } catch (AmoCRMApiConnectExceptionException $e) {
                // Повторяем только при сетевых ошибках
                $attempt++;
                if ($attempt >= $maxRetries) {
                    throw $e; // если превысили лимит, пробрасываем
                }
                sleep(2); // пауза перед повтором
            } catch (\Exception $e) {
                // все остальные ошибки не трогаем
                throw $e;
            }
        }
    }
}
