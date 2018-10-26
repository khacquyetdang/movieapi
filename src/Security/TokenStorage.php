<?php
namespace App\Security;

use Predis\Client;

class TokenStorage
{
    const KEY_SUFFIX = '-token';

    /*
     * @var Client
     */
    private $redisClient;

    public function __construct(Client $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    /**
     * @param email
     * @param token
     */
    public function storeToken(string $email, string $token): void
    {
        $key = $email . $this::KEY_SUFFIX;
        $this->redisClient->set($key, $token);
        $this->redisClient->expire($key, 3600);
    }

    /**
     * @param $email
     */
    public function invalidateToken(string $email): void
    {
        $key = $email . $this::KEY_SUFFIX;
        $this->redisClient->del($key);
    }

    public function isTokenValid(string $email, string $token): bool
    {
        $key = $email . $this::KEY_SUFFIX;
        return $this->redisClient->get($key) === $token;

    }

}
