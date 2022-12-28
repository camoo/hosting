<?php

declare(strict_types=1);

namespace Camoo\Hosting\Lib;

use BadMethodCallException;
use Camoo\Cache\Cache;
use Camoo\Cache\CacheConfig;

/**
 * Class AccessToken
 *
 * @author CamooSarl
 */
class AccessToken
{
    private const LOGIN_URL = 'auth';

    private const CACHE_KEY = 'http_access_token';

    protected static ?string $_Token = null;

    protected static array $_login = [];

    private Cache $cache;

    private function __construct()
    {
        $salt = defined('ACCESS_TOKEN_SALT') ? ACCESS_TOKEN_SALT : null;
        $this->cache = new Cache(CacheConfig::fromArray([
            'crypto_salt' => $salt,
            'tmpPath' => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'tmp',
        ]));
    }

    public static function __callStatic($name, $arguments)
    {
        if ($name === '_get') {
            $arg = empty($arguments[0]) ? [] : $arguments[0];

            return (new self())->get($arg);
        }
        throw new BadMethodCallException("Undefined method {$name}");
    }

    public function __toString(): string
    {
        return static::$_Token;
    }

    public function get(array $loginData = []): AccessToken
    {
        if (empty($loginData) && defined('cm_email') && defined('cm_passwd')) {
            $loginData = ['email' => cm_email, 'password' => cm_passwd];
        }
        self::$_login = $loginData;

        if (array_key_exists('email', $loginData) && str_contains($loginData['email'], '@')) {
            $token = $this->cache->read(self::CACHE_KEY);
            if (!empty($token)) {
                self::$_Token = $token;

                return $this;
            }
        }

        if ($hRep = $this->apiCall()) {
            static::$_Token = $hRep['result']['access_token'];
            $this->cache->write(self::CACHE_KEY, self::$_Token, 1790);
        }

        return $this;
    }
    // @codeCoverageIgnoreEnd

    public function delete(): void
    {
        $this->cache->delete(self::CACHE_KEY);
    }

    // @codeCoverageIgnoreStart
    protected function getLoginData(): array
    {
        return static::$_login;
    }

    protected function apiCall(): ?array
    {
        $oResponse = (new Client())->post(self::LOGIN_URL, $this->getLoginData());
        if ($oResponse->getStatusCode() !== 200) {
            return null;
        }

        $result = $oResponse->getJson();

        $status = $result['status'] ?? null;

        return  $status === Response::GOOD_STATUS ? $result : null;
    }
}
