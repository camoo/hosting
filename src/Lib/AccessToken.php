<?php

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

    protected static ?string $_Token = null;

    protected static ?string $_cacheFile = null;

    protected static array $_login = [];

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

    public function get(array $hLogin = []): AccessToken
    {
        if (empty($hLogin) && defined('cm_email') && defined('cm_passwd')) {
            $hLogin = ['email' => cm_email, 'password' => cm_passwd];
        }
        static::$_login = $hLogin;

        if (array_key_exists('email', $hLogin) && strpos($hLogin['email'], '@') !== false) {
            $asEmail = explode('@', $hLogin['email']);
            $sTmpName = $asEmail[0];
            static::$_cacheFile = dirname(__DIR__, 2) . '/tmp/' . $sTmpName . '.cm';
            if (file_exists(static::$_cacheFile)) {
                if (($iLastChangedTime = filemtime(static::$_cacheFile)) && (($iLastChangedTime + 1740) < time())) {
                    unlink(static::$_cacheFile);
                } else {
                    if (($xData = file_get_contents(static::$_cacheFile)) && ($sData = self::decrypt($xData))) {
                        static::$_Token = $sData;

                        return $this;
                    }
                }
            }
        }

        if ($hRep = $this->apiCall()) {
            static::$_Token = $hRep['result']['access_token'];
            file_put_contents(static::$_cacheFile, self::encrypt(static::$_Token) . PHP_EOL, LOCK_EX);
        }

        return $this;
    }
    // @codeCoverageIgnoreEnd

    public function delete(): void
    {
        if (null === static::$_cacheFile) {
            return;
        }
        unlink(static::$_cacheFile);
    }

    // @codeCoverageIgnoreStart
    protected function getLoginData(): array
    {
        return static::$_login;
    }

    protected function apiCall(): ?array
    {
        $oResponse = (new Client())->post(self::LOGIN_URL, $this->getLoginData());
        if ($oResponse->getStatusCode() === 200 && ($hRep = $oResponse->getJson()) && $hRep['status'] === Response::GOOD_STATUS) {
            return $hRep;
        }

        return null;
    }

    private static function encrypt(string $string, string $sCipher = 'AES-256-CBC'): string
    {
        if (empty($string)) {
            return '';
        }
        if (!defined('ACCESS_TOKEN_SALT')) {
            return $string;
        }
        $key = hash('sha256', ACCESS_TOKEN_SALT);
        $ivlen = openssl_cipher_iv_length($sCipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($string, $sCipher, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);

        return base64_encode($iv . $hmac . $ciphertext_raw);
    }

    private static function decrypt(string $string, string $sCipher = 'AES-256-CBC'): string
    {
        if (empty($string)) {
            return '';
        }
        if (!defined('ACCESS_TOKEN_SALT')) {
            return $string;
        }
        $enc = base64_decode($string);
        $key = hash('sha256', ACCESS_TOKEN_SALT);
        $ivlen = openssl_cipher_iv_length($sCipher);
        $iv = substr($enc, 0, $ivlen);
        $hmac = substr($enc, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($enc, $ivlen + $sha2len);

        return openssl_decrypt($ciphertext_raw, $sCipher, $key, OPENSSL_RAW_DATA, $iv);
    }
}
