<?php

declare(strict_types=1);

namespace Camoo\Hosting\Lib;

use Camoo\Hosting\Dto\AccessTokenDTO;
use Camoo\Hosting\Exception\AccessTokenException;
use Stringable;

/**
 * Class AccessToken
 *
 * @author CamooSarl
 */
class AccessToken implements Stringable
{
    private const LOGIN_URL = 'auth';

    private const ENCRYPT_KEY = 'AES-256-CBC';

    protected static ?string $tmpPath = null;

    /** @var array<string,string> */
    protected static array $loginData = [];

    private static ?AccessTokenDTO $tokenDTO = null;

    private static ?self $instance = null;

    public function __construct(private ?Client $client = null)
    {
        $this->client ??= new Client();
    }

    public function __toString(): string
    {
        if (null === self::$tokenDTO) {
            return '';
        }

        return self::$tokenDTO->accessToken;
    }

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /** @param array<string,string> $loginData */
    public function get(array $loginData = []): self
    {
        if (empty($loginData) && defined('cm_email') && defined('cm_passwd')) {
            $loginData = ['email' => cm_email, 'password' => cm_passwd];
        }
        static::$loginData = $loginData;

        $cached = $this->localCache();

        if ($cached) {
            return $this;
        }

        if ($response = $this->apiCall()) {
            $issuedAt = time();
            $expiresIn = max((int)$response['result']['expires_in'] - 60, 10);
            self::$tokenDTO = new AccessTokenDTO(
                $response['result']['access_token'],
                $response['result']['token_type'],
                $this->getExpiresIn($issuedAt, $expiresIn),
                $issuedAt,
                $response['result']['scope']
            );
            $this->saveTokenToLocalCache($issuedAt);
        }

        return $this;
    }

    public function getTokenDTO(): ?AccessTokenDTO
    {
        return self::$tokenDTO;
    }

    // @codeCoverageIgnoreEnd

    public function delete(): void
    {
        if (null === self::$tmpPath) {
            return;
        }
        if (is_file(self::$tmpPath)) {
            unlink(self::$tmpPath);
        }
    }

    // @codeCoverageIgnoreStart

    /** @return string[] */
    protected function getLoginData(): array
    {
        return static::$loginData;
    }

    /** @return array<string,mixed>|null */
    protected function apiCall(): ?array
    {
        /** @var Client $client */
        $client = $this->client;
        $oResponse = $client->post(self::LOGIN_URL, $this->getLoginData());

        if ($oResponse->getStatusCode() !== 200) {
            return null;
        }

        $result = $oResponse->getJson();

        return $result['status'] === Response::GOOD_STATUS ? $result : null;
    }

    private function localCache(): ?string
    {
        if (empty(self::$loginData['email']) || !str_contains(self::$loginData['email'], '@')) {
            return null;
        }
        self::$tmpPath = $this->generateTmpPath(static::$loginData['email']);

        if (!is_file(self::$tmpPath)) {
            return null;
        }

        return $this->getCachedToken();
    }

    /** Reads the cached token, checks its validity, and deletes if expired. */
    private function getCachedToken(): ?string
    {
        $lastChangedTime = filemtime(self::$tmpPath ?? '');
        if ($lastChangedTime && ($lastChangedTime + 1740) < time()) {
            unlink(self::$tmpPath ?? '');

            return null;
        }

        $encryptedData = file_get_contents(self::$tmpPath ?? '');
        if (false === $encryptedData || !($decryptedData = self::decrypt($encryptedData))) {
            unlink(self::$tmpPath ?? '');

            return null;
        }

        $data = json_decode($decryptedData, true);

        self::$tokenDTO = new AccessTokenDTO(
            $data['access_token'],
            $data['token_type'],
            $this->getExpiresIn($data['issued_at'], $data['expires_in']),
            $data['issued_at'],
            $data['scope']
        );

        return self::decrypt($encryptedData);
    }

    /**
     * Calculates and returns the remaining time in seconds until the token expires.
     *
     * @return int Remaining time in seconds
     */
    private function getExpiresIn(int $issuedAt, int $originalExpiresIn): int
    {

        $currentTime = time();
        $expiresAt = $issuedAt + $originalExpiresIn;
        $remainingTime = $expiresAt - $currentTime;

        return max($remainingTime, 0);
    }

    /** Saves the current token to local cache. */
    private function saveTokenToLocalCache(int $issuedAt): void
    {
        $data = json_encode([
            'access_token' => self::$tokenDTO?->accessToken,
            'token_type' => self::$tokenDTO?->tokenType,
            'expires_in' => self::$tokenDTO?->expiresIn,
            'issued_at' => $issuedAt,
            'scope' => self::$tokenDTO?->scope,
        ]);
        if (false === $data) {
            return;
        }
        file_put_contents(self::$tmpPath ?? '', self::encrypt($data) . PHP_EOL, LOCK_EX);
    }

    /** Generates a temporary path based on the email. */
    private function generateTmpPath(string $email): string
    {
        [$sTmpName] = explode('@', $email);

        return dirname(__DIR__, 2) . '/tmp/' . $sTmpName . '.cm';
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
        if ($iv_length === false) {
            throw new AccessTokenException('Encryption IV length not supported');
        }
        $iv = openssl_random_pseudo_bytes($iv_length);
        $ciphertext_raw = openssl_encrypt($string, self::ENCRYPT_KEY, $key, OPENSSL_RAW_DATA, $iv);
        if ($ciphertext_raw === false) {
            throw new AccessTokenException('Encryption Cipher not supported');
        }
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
        if (false === $iv_length) {
            throw new AccessTokenException('Decryption IV length not supported');
        }
        $iv = substr($enc, 0, $iv_length);
        $sha2len = 32;
        $hmac = substr($enc, $iv_length, $sha2len);
        $ciphertext_raw = substr($enc, $iv_length + $sha2len);
        // Recalculate the HMAC on the ciphertext to verify integrity
        $calculatedHmac = hash_hmac('sha256', $ciphertext_raw, $key, true);

        // Check if the extracted HMAC matches the recalculated HMAC
        if (!hash_equals($hmac, $calculatedHmac)) {
            throw new AccessTokenException('Integrity check failed: HMAC does not match.');
        }

        $result = openssl_decrypt($ciphertext_raw, self::ENCRYPT_KEY, $key, OPENSSL_RAW_DATA, $iv);
        if ($result === false) {
            throw new AccessTokenException('Decryption failed.');
        }

        return $result;
    }
}
