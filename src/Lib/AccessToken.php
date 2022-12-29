<?php

declare(strict_types=1);

namespace Camoo\Hosting\Lib;

use BadMethodCallException;

/**
 * Class AccessToken
 *
 * @author CamooSarl
 */
class AccessToken
{
    private const LOGIN_URL = 'auth';

    private const ENCRYPT_KEY = 'AES-256-CBC';

    protected static ?string $accessToken = null;

    protected static ?string $tmpPath = null;

    protected static array $loginData = [];

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
        return static::$accessToken;
    }

    public function get(array $loginData = []): AccessToken
    {
        if (empty($loginData) && defined('cm_email') && defined('cm_passwd')) {
            $loginData = ['email' => cm_email, 'password' => cm_passwd];
        }
        static::$loginData = $loginData;

        $cached = $this->localCache($loginData);

        if (null !== $cached) {
            return $this;
        }

        if ($hRep = $this->apiCall()) {
            static::$accessToken = $hRep['result']['access_token'];
            file_put_contents(self::$tmpPath, self::encrypt(static::$accessToken) . PHP_EOL, LOCK_EX);
        }

        return $this;
    }
    // @codeCoverageIgnoreEnd

    public function delete(): void
    {
        if (null === static::$tmpPath) {
            return;
        }
        unlink(static::$tmpPath);
    }

    // @codeCoverageIgnoreStart
    protected function getLoginData(): array
    {
        return static::$loginData;
    }

    protected function apiCall(): ?array
    {
        $oResponse = (new Client())->post(self::LOGIN_URL, $this->getLoginData());

        if ($oResponse->getStatusCode() !== 200) {
            return null;
        }

        $result = $oResponse->getJson();

        return $result['status'] === Response::GOOD_STATUS ? $result : null;
    }

    private function localCache(array $loginData): ?string
    {
        if (!array_key_exists('email', $loginData) || !str_contains($loginData['email'], '@')) {
            return null;
        }
        [$sTmpName] = explode('@', $loginData['email']);

        static::$tmpPath = dirname(__DIR__, 2) . '/tmp/' . $sTmpName . '.cm';

        if (!is_file(self::$tmpPath)) {
            return null;
        }

        self::$accessToken = null;
        if (($iLastChangedTime = filemtime(self::$tmpPath)) && (($iLastChangedTime + 1740) < time())) {
            unlink(static::$tmpPath);
        } else {
            if (($xData = file_get_contents(self::$tmpPath)) && ($sData = self::decrypt($xData))) {
                self::$accessToken = $sData;
            }
        }

        return self::$accessToken;
    }

    private static function encrypt(string $string): string
    {
        if (empty($string)) {
            return '';
        }
        if (!defined('ACCESS_TOKEN_SALT')) {
            return $string;
        }
        $key = hash('sha256', ACCESS_TOKEN_SALT);
        $iv_length = openssl_cipher_iv_length(self::ENCRYPT_KEY);
        $iv = openssl_random_pseudo_bytes($iv_length);
        $ciphertext_raw = openssl_encrypt($string, self::ENCRYPT_KEY, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);

        return base64_encode($iv . $hmac . $ciphertext_raw);
    }

    private static function decrypt(string $string): string
    {
        if (empty($string)) {
            return '';
        }
        if (!defined('ACCESS_TOKEN_SALT')) {
            return $string;
        }
        $enc = base64_decode($string);
        $key = hash('sha256', ACCESS_TOKEN_SALT);
        $iv_length = openssl_cipher_iv_length(self::ENCRYPT_KEY);
        $iv = substr($enc, 0, $iv_length);
        substr($enc, $iv_length, $sha2len = 32);
        $ciphertext_raw = substr($enc, $iv_length + $sha2len);

        return openssl_decrypt($ciphertext_raw, self::ENCRYPT_KEY, $key, OPENSSL_RAW_DATA, $iv);
    }
}
