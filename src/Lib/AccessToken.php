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

    private const ENCRYPTED_ENV_PASSWORD_PREFIX = 'enc:v1:';

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
        if (empty($loginData)) {
            $loginData = $this->credentialsFromEnvironment();
        }
        static::$loginData = $loginData;

        $cached = $this->localCache();

        if ($cached) {
            return $this;
        }

        if ($response = $this->apiCall()) {
            $result = $response['result'] ?? null;
            if (!is_array($result)
                || !isset($result['access_token'], $result['token_type'], $result['expires_in'], $result['scope'])
                || !is_string($result['access_token'])
                || !is_string($result['token_type'])
                || !is_numeric($result['expires_in'])
                || !is_string($result['scope'])
            ) {
                throw new AccessTokenException('Authentication response is invalid.');
            }

            $issuedAt = time();
            $expiresIn = max((int)$result['expires_in'] - 60, 10);
            self::$tokenDTO = new AccessTokenDTO(
                $result['access_token'],
                $result['token_type'],
                $this->getExpiresIn($issuedAt, $expiresIn),
                $issuedAt,
                $result['scope']
            );
            $this->saveTokenToLocalCache($issuedAt);
        } else {
            throw new AccessTokenException('Authentication request failed.');
        }

        return $this;
    }

    /**
     * Resolve credentials from standard environment variables while keeping
     * the original constants backwards compatible.
     *
     * @return array{email:string,password:string}
     */
    private function credentialsFromEnvironment(): array
    {
        $email = getenv('CAMOO_HOSTING_EMAIL')
            ?: (defined('CAMOO_HOSTING_EMAIL') ? (string)CAMOO_HOSTING_EMAIL : null)
            ?: (defined('cm_email') ? (string)cm_email : '');
        $password = getenv('CAMOO_HOSTING_PASSWORD')
            ?: (defined('CAMOO_HOSTING_PASSWORD') ? (string)CAMOO_HOSTING_PASSWORD : null)
            ?: (defined('cm_passwd') ? (string)cm_passwd : '');

        $password = $this->decryptEnvironmentPassword($password);

        if ($email === '' || $password === '') {
            throw new AccessTokenException(
                'Camoo.Hosting credentials are missing. Set CAMOO_HOSTING_EMAIL and CAMOO_HOSTING_PASSWORD, '
                . 'or pass email and password explicitly to AccessToken::get().'
            );
        }

        return ['email' => $email, 'password' => $password];
    }

    private function decryptEnvironmentPassword(string $password): string
    {
        if (!str_starts_with($password, self::ENCRYPTED_ENV_PASSWORD_PREFIX)) {
            return $password;
        }

        if (!defined('ACCESS_TOKEN_SALT') || ACCESS_TOKEN_SALT === '') {
            throw new AccessTokenException('ACCESS_TOKEN_SALT is required for an encrypted password.');
        }

        try {
            $decrypted = self::decrypt(substr($password, strlen(self::ENCRYPTED_ENV_PASSWORD_PREFIX)));
        } catch (\Throwable $exception) {
            throw new AccessTokenException('The encrypted Camoo.Hosting password could not be decrypted.', 0, $exception);
        }

        if ($decrypted === '') {
            throw new AccessTokenException('The encrypted Camoo.Hosting password is empty.');
        }

        return $decrypted;
    }

    public function getTokenDTO(): ?AccessTokenDTO
    {
        return self::$tokenDTO;
    }

    // @codeCoverageIgnoreEnd

    public function delete(): void
    {
        self::$tokenDTO = null;
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
        $encryptedData = file_get_contents(self::$tmpPath ?? '');
        if (false === $encryptedData || !($decryptedData = self::decrypt($encryptedData))) {
            unlink(self::$tmpPath ?? '');

            return null;
        }

        $data = json_decode($decryptedData, true);

        if (!is_array($data)
            || !isset($data['access_token'], $data['token_type'], $data['expires_in'], $data['issued_at'], $data['scope'])
            || !is_string($data['access_token'])
            || !is_string($data['token_type'])
            || !is_numeric($data['expires_in'])
            || !is_numeric($data['issued_at'])
            || !is_string($data['scope'])
            || (int)$data['issued_at'] + (int)$data['expires_in'] <= time()
        ) {
            unlink(self::$tmpPath ?? '');

            return null;
        }

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
        if (empty($iv_length)) {
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
        if (empty($iv_length)) {
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
